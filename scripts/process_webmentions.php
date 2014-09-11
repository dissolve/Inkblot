<?php

require_once('script_setup.php'); //this is to set up interactions to the MVC

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';
include '../libraries/cassis/cassis-loader.php';


//check if target is at this site
$result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
$webmention = $result->row;

while($webmention){

    $source_url = trim($webmention['source_url']);
    $target_url = trim($webmention['target_url']);

    $webmention_id = $webmention['webmention_id'];

    //echo 'id=' . $webmention_id;

    //to verify that target is on my site
    $c = curl_init();
    curl_setopt($c, CURLOPT_NOBODY, 1);
    curl_setopt($c, CURLOPT_URL, $target_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $real_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    curl_close($c);
    unset($c);


    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $source_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    $page_content = curl_exec($c);
    curl_close($c);
    unset($c);

    if($page_content === FALSE){
        //our curl command failed to fetch the source site
        $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Failed To Fetch Source' WHERE webmention_id = ". (int)$webmention_id);

    //} elseif(strpos($real_url, HTTP_SERVER) !== 0 && strpos($real_url, HTTPS_SERVER) !== 0){
        ////target_url does not point actually redirect to our site
        //$db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Does Not Point Here' WHERE webmention_id = ". (int)$webmention_id);
//
    } elseif(stristr($page_content, $target_url) === FALSE){
        //we could not find the target_url anywhere on the source page.
        $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Not Found At Source' WHERE webmention_id = ". (int)$webmention_id);

    } else {
        $mf2_parsed = Mf2\parse($page_content, $real_source_url);
        $comment_data = IndieWeb\comments\parse($mf2_parsed['items'][0], $target_url);

        include DIR_BASE . '/routes.php';

        $data = array();
        foreach($advanced_routes as $adv_route){
            $matches = array();
            preg_match($adv_route['expression'], $real_url, $matches);
            if(!empty($matches)){
                $model = $adv_route['controller'];
                    foreach($matches as $field => $value){
                        $data[$field] = $value;
                    }
            }
        }

        try {
            $loader->model($model);
            $registry->get('model_'. str_replace('/', '_', $model))->addWebmention($data, $webmention_id, $comment_data);
            //echo 'debug 1:';
            //print_r($data);
            //echo 'debug 2:';
            //print_r($webmention_id);
            //echo 'debug 3:';
            //print_r($comment_data);
        } catch (Exception $e) {
            if(isset($comment_data['type']) && $comment_data['type'] == 'like'){
                $db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    "");
                $like_id = $db->getLastId();
                $db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $cache->delete('likes');
                break;
            } else {
                $db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$post['post_id'] .", parse_timestamp = NOW(), approved=1");
                $mention_id = $db->getLastId();
                $db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $cache->delete('mentions');
            }
        }


    }

    $result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
    $webmention = $result->row;

} //end while($webmention) loop
