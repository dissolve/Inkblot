<?php  
require_once DIR_BASE . '/libraries/php-mf2/Mf2/Parser.php';
require_once DIR_BASE . '/libraries/php-comments/src/indieweb/comments.php';
require_once DIR_BASE . '/libraries/cassis/cassis-loader.php';

class ControllerWebmentionReceive extends Controller {
    public function index() {
        $source = $this->request->post['source'];
        $target = $this->request->post['target'];
        $vouch = $this->request->post['vouch'];
        //$this->log->write('v1: ='.$vouch);

        // are URLs Valid?
        if(!$this->is_valid_url($source) || !$this->is_valid_url($target)){
            header('HTTP/1.1 400 Bad Request');
            exit();
        } elseif (!$this->is_approved_source($source)){

            if(!USE_VOUCH){
                header('HTTP/1.1 400 Bad Request');
                exit();
            } else {
                if(!isset($this->request->post['vouch'])){

                    $this->load->model('webmention/queue');
                    $queue_id = $this->model_webmention_queue->addUnvouchedEntry($source, $target, $vouch);

                    if(isset($this->request->post['callback'])){
                        $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
                    }

                    $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

                    $this->response->addHeader('Link: <'.$link.'>; rel="status"');
                    header('HTTP/1.1 449 Retry With vouch');
                    exit();

                } elseif(!$this->is_valid_url($vouch)){
                    header('HTTP/1.1 400 Bad Request');
                    exit();
                } elseif(!$this->is_approved_source($vouch)){
                    header('HTTP/1.1 400 Bad Request');
                    exit();

                } else {
                    //todo  review this
                    $this->response->addHeader('HTTP/1.1 202 Accepted');

                    $this->load->model('webmention/queue');
                    $queue_id = $this->model_webmention_queue->addEntry($source, $target, $vouch);
                    //$this->log->write('v2: ='.$vouch);

                    if(isset($this->request->post['callback'])){
                        $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
                    }

                    $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

                    $this->response->addHeader('Link: <'.$link.'>; rel="status"');

                    $this->response->setOutput($link);
                }
            }

        } else {
            $this->response->addHeader('HTTP/1.1 202 Accepted');

            $this->load->model('webmention/queue');
            //$queue_id = $this->model_webmention_queue->addEntry($source, $target);
            //$this->log->write('v3: ='.$vouch);
            $queue_id = $this->model_webmention_queue->addEntry($source, $target, $vouch);

            if(isset($this->request->post['callback'])){
                $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
            }

            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');

            $this->response->setOutput($link);
        }
    }

    private function is_approved_source($url){
        if(!USE_VOUCH){
            return true;
        }
        $this->load->model('webmention/vouch');
        return $this->model_webmention_vouch->isWhiteListed($url);
    }

    //very basic function to determine if URL is valid, this is certainly a great place for improvement
    private function is_valid_url($url){
        if(!isset($url)) {
            return false;
        }
        if(empty($url)) {
            return false;
        } 
        if(strpos($url, '.') == 0) {
            return false;
        }
        return true;
    }

    public function process_webmentions(){
        //check if target is at this site
        $result = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
        $webmention = $result->row;

        while($webmention){

            $source_url = trim($webmention['source_url']);
            $target_url = trim($webmention['target_url']);
            $vouch_url = null;
            if($webmention['vouch_url']){
                $vouch_url = trim($webmention['vouch_url']);
            }

            $webmention_id = $webmention['webmention_id'];

            $resulting_comment_id = (int)$webmention['resulting_comment_id'];
            $resulting_mention_id = (int)$webmention['resulting_mention_id'];
            $resulting_like_id = (int)$webmention['resulting_like_id'];
            $editing = FALSE;
            if($resulting_comment_id > 0 || $resulting_mention_id > 0 || $resulting_like_id > 0){
                $editing = TRUE;
            }

            if($vouch_url){
                $valid_link_found = false;
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_URL, $vouch_url);
                curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
                $vouch_content = curl_exec($c);
                curl_close($c);
                unset($c);

                $short_vouch  =  trim(str_replace(array('http://', 'https://'),array('',''), $vouch_url), '/');

                $reg_ex_match = '/(href=[\'"](?<href>[^\'"]+)[\'"][^>]*(rel=[\'"](?<rel>[^\'"]+)[\'"])?)/';
                $matches = array();
                preg_match_all($reg_ex_match, $vouch_content ,$matches);
                for($i = 0; $i < count($matches['href']); $i++){
                    //$this->log->write('checking '.$href . '   rel '.$rel);
                    $href = strtolower($matches['href'][$i]);
                    $rel = strtolower($matches['rel'][$i]);

                    if(strpos($rel, "nofollow") === FALSE){
                        if(strpos($href, $short_vouch) !== FALSE){
                            $valid_link_found = true;
                        }
                    }
                }
                if(!$valid_link_found){
                    //repeat all that for rel before href (because preg_match_all doesn't like reused names)
                    $reg_ex_match = '/(rel=[\'"](?<rel>[^\'"]+)[\'"][^>]*href=[\'"](?<href>[^\'"]+)[\'"])/';
                    $matches = array();
                    preg_match_all($reg_ex_match, $vouch_content ,$matches);

                    for($i = 0; $i < count($matches['href']); $i++){
                        //$this->log->write('checking '.$href . '   rel '.$rel);
                        $href = strtolower($matches['href'][$i]);
                        $rel = strtolower($matches['rel'][$i]);

                        if(strpos($rel,"nofollow") === FALSE){
                            if(strpos($href, $short_vouch) !== FALSE){
                                $valid_link_found = true;
                            }
                        }
                    }
                }


                if(!$valid_link_found){
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Vouch Invalid' WHERE webmention_id = ". (int)$webmention_id);
                    $result = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
                    $webmention = $result->row;
                    continue;
                }
            }
             //TODO shortcut this if it matches our HTTP_SERVER OR HTTPS_SERVER

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
            //curl_setopt($c, CURLOPT_HEADER, true); //including header causes php-mf2 parsing to fail
            $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
            $page_content = curl_exec($c);

            $return_code = curl_getinfo($c, CURLINFO_HTTP_CODE);


            //TODO test if vouch points to source_url

            curl_close($c);
            unset($c);

            if($page_content === FALSE){
                if($editing && $return_code == 410){
                    if($resulting_comment_id > 0){
                        $this->db->query("UPDATE ". DATABASE.".comments SET body = '*Comment Deleted*' WHERE comment_id = ". (int)$resulting_comment_id);
                    }
                    if($resulting_mention_id > 0){
                        $this->db->query("DELETE FROM ". DATABASE.".mentions WHERE mention_id = ". (int)$resulting_mention_id);
                    }
                    if($resulting_like_id > 0){
                        $this->db->query("DELETE FROM ". DATABASE.".likes WHERE like_id = ". (int)$resulting_like_id);
                    }
                    //our curl command failed to fetch the source site
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '410', webmention_status = 'Deleted' WHERE webmention_id = ". (int)$webmention_id);

                } else {
                    //our curl command failed to fetch the source site
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Failed To Fetch Source' WHERE webmention_id = ". (int)$webmention_id);
                }

            //} elseif(strpos($real_url, HTTP_SERVER) !== 0 && strpos($real_url, HTTPS_SERVER) !== 0){
                ////target_url does not point actually redirect to our site
                //$this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Does Not Point Here' WHERE webmention_id = ". (int)$webmention_id);
        //
            } elseif(stristr($page_content, $target_url) === FALSE){
                //we could not find the target_url anywhere on the source page.
                $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Not Found At Source' WHERE webmention_id = ". (int)$webmention_id);
                    if($resulting_comment_id > 0){
                        $this->db->query("UPDATE ". DATABASE.".comments SET body = '*Comment Deleted*' WHERE comment_id = ". (int)$resulting_comment_id);
                    }
                    if($resulting_mention_id > 0){
                        $this->db->query("DELETE FROM ". DATABASE.".mentions WHERE mention_id = ". (int)$resulting_mention_id);
                    }
                    if($resulting_like_id > 0){
                        $this->db->query("DELETE FROM ". DATABASE.".likes WHERE like_id = ". (int)$resulting_like_id);
                    }

            } else {
                $mf2_parsed = Mf2\parse($page_content, $real_source_url);
                foreach($mf2_parsed['items'] as $item){
                    $comment_data = IndieWeb\comments\parse($item, $target_url);
                    if(!empty($comment_data['url'])){ //break out of loop if we found one
                        break;
                    }
                }

    $psc_matches = array();
    $psc_pattern = '/\(([^ ]+ [^ ]+)\)$/';
    preg_match($psc_pattern, $comment_data['text'], $psc_matches);

    if(isset($matches[1]) && !empty($matches[1])){
        $src = 'http://' .str_replace(' ','/', $matches[1]);
        //TODO, find real source
        //$comment_data['text'] = str_replace(' '.$matches[0], '', $comment_data['text']);
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $src);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    }

                $this->log->write('target = ' . $target_url . ' real_source_url = '. $real_source_url);

                require DIR_BASE . '/routes.php';

                $data = array();
                foreach($advanced_routes as $adv_route){
                    $matches = array();
                    $real_url = ltrim(str_replace(array(HTTP_SERVER, HTTPS_SERVER),array('',''), $real_url),'/');
                    preg_match($adv_route['expression'], $real_url, $matches);
                    if(!empty($matches)){
                        $model = $adv_route['controller'];
                            foreach($matches as $field => $value){
                                $data[$field] = $value;
                            }
                    }
                }

                try {
                    if(!$model){
                        throw new Exception('No Model Set.');
                    } else {
                        $this->load->model($model);
                        if($editing){
                            $this->registry->get('model_'. str_replace('/', '_', $model))->editWebmention($data, $webmention_id, $comment_data);
                        } else {
                            $this->log->write(' calling model_'. str_replace('/', '_', $model) . ' addWebmention with '. print_r($data, true). ' ' .  $webmention_id . " " . print_r( $comment_data, true));
                            $this->registry->get('model_'. str_replace('/', '_', $model))->addWebmention($data, $webmention_id, $comment_data);
                            $this->log->write(' DONE');
                        }
                    }
                } catch (Exception $e) {
                    if(empty($comment_data['url'])){
                        $comment_data['url'] = $real_source_url;
                    }
                    if(isset($comment_data['type']) && $comment_data['type'] == 'like'){
                        $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                            ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                            ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                            ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                            "");
                        $like_id = $this->db->getLastId();
                        $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                        $this->cache->delete('likes');
                        break;
                    } else {
                        $this->db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$comment_data['url']."'".
                            ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                            ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                            ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                            ", post_id = ".(int)$post['post_id'] .", parse_timestamp = NOW(), approved=1");
                        $mention_id = $this->db->getLastId();
                        $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                        $this->cache->delete('mentions');
                    }
                }


            }

            $result = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
            $webmention = $result->row;

        } //end while($webmention) loop
    }
}
?>
