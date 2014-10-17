<?php

require_once('script_setup.php'); //this is to set up interactions to the MVC
//TODO  i should be able to just do this, and get rid of the rest of the file
//$loader->model('blog/context');
//$registry->get('model_blog_context')->processContexts();

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';
include '../libraries/cassis/cassis-loader.php';
include '../libraries/php-mf2-shim/Mf2/functions.php';
include '../libraries/php-mf2-shim/Mf2/Shim/Twitter.php';
//include '../libraries/php-mf2-shim/Mf2/Shim/Facebook.php';

function get_context_id($db, $source_url){
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $source_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    $page_content = curl_exec($c);
    curl_close($c);
    unset($c);

    if($page_content !== FALSE){

        $mf2_parsed = Mf2\parse($page_content, $real_source_url);
        $source_data = IndieWeb\comments\parse($mf2_parsed['items'][0]);
        if(empty($source_data['url'])){
            $mf2_parsed = Mf2\Shim\parseTwitter($page_content, $real_source_url);
            $source_data = IndieWeb\comments\parse($mf2_parsed['items'][0]);
        }
        //if(empty($source_data['url'])){
            //$mf2_parsed = Mf2\Shim\parseFacebook($page_content, $real_source_url);
            //$source_data = IndieWeb\comments\parse($mf2_parsed['items'][0]);
        //}
        if(empty($source_data['url'])){
            return null;
        }


        $real_url = $source_data['url'];

        $query = $db->query("SELECT * FROM ".DATABASE.".context WHERE source_url='".$db->escape($real_url)."' LIMIT 1");

        if(!empty($query->row)){
            return $query->row['context_id'];

        } else {
            $published = $source_data['published'];
            $body = $source_data['text'];
            $source_name = $source_data['name'];

            $author_name = $source_data['author']['name'];
            $author_url = $source_data['author']['url'];
            $author_image = $source_data['author']['photo'];


            // do our best to conver to local time
            date_default_timezone_set(LOCALTIMEZONE);
            $date = new DateTime($published);
            $now = new DateTime;
            $tz = $now->getTimezone();
            $date->setTimezone($tz);
            $published = $date->format('Y-m-d H:i:s')."\n";

            
            if(empty($real_url)){
                return null;
            }

            $db->query("INSERT INTO ". DATABASE.".context SET 
                author_name = '".$db->escape($author_name)."',
                author_url = '".$db->escape($author_url)."',
                author_image = '".$db->escape($author_image)."',
                source_name = '".$db->escape($source_name)."',
                source_url = '".$db->escape($real_url)."',
                body = '".$db->escape($body)."',
                timestamp ='".$published."'");

            $context_id = $db->getLastId();

            foreach($mf2_parsed['items'][0]['properties']['in-reply-to'] as $citation) {
                if(isset($citation['properties'])){
                    foreach($citation['properties']['url'] as $reply_to_url){
                        $ctx_id = get_context_id($db, $reply_to_url);
                        if($ctx_id){
                            $db->query("INSERT INTO ". DATABASE.".context_to_context SET 
                            context_id = ".(int)$context_id.",
                            parent_context_id = ".(int)$ctx_id);
                        }

                    }
                } else  {
                    $reply_to_url = $citation;

                    $ctx_id = get_context_id($db, $reply_to_url);
                    if($ctx_id){
                        $db->query("INSERT INTO ". DATABASE.".context_to_context SET 
                        context_id = ".(int)$context_id.",
                        parent_context_id = ".(int)$ctx_id);
                    }
                }

            }
            return $context_id;
        }
    } else {
        return null;
    }
}

$result = $db->query("SELECT * FROM ". DATABASE.".posts WHERE NOT replyto is NULL AND context_parsed=0 LIMIT 1");
$post = $result->row;

while($post){
    //immediately update this to say that it is parsed.. this way we don't end up trying to run it multiple times on the same post
    $db->query("UPDATE ". DATABASE.".posts SET context_parsed = 1 WHERE post_id = ". (int)$post_id);

    $source_url = trim($post['replyto']); //todo want to support multiples

    $post_id = $post['post_id'];
    $context_id = get_context_id($db, $source_url);

    if($context_id){
        $db->query("INSERT INTO ". DATABASE.".post_context SET 
            post_id = ".(int)$post_id.",
            context_id = ".(int)$context_id);
    }
                    

    $result = $db->query("SELECT * FROM ". DATABASE.".posts WHERE NOT replyto is NULL AND context_parsed=0 LIMIT 1");
    $post = $result->row;

} //end while($post) loop
$cache->delete('context');
 
