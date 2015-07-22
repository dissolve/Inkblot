<?php  
require_once DIR_BASE . '/libraries/php-mf2/Mf2/Parser.php';
require_once DIR_BASE . '/libraries/php-comments/src/indieweb/comments.php';
require_once DIR_BASE . '/libraries/cassis/cassis-loader.php';

class ControllerWebmentionReceive extends Controller {
    public function index() {
        if($this->request->server['REQUEST_METHOD'] != 'POST' && $this->session->data['is_owner']){
            $this->webmention_manager();
        } else {
            $this->receive_webmention();
        }
    }

    private function webmention_manager(){
        $this->load->model('webmention/queue');
        if(isset($this->request->get['id']) && isset($this->request->get['action'])){
            $webmention_id = $this->request->get['id'];
            $action = $this->request->get['action'];

            switch($action){
            case 'retry':
                $this->model_webmention_queue->retry($webmention_id);
                break;
            case 'dismiss':
                $this->model_webmention_queue->dismiss($webmention_id);
                break;
            case 'approve':
                $this->model_webmention_queue->whitelistAndRetry($webmention_id);
                break;
            }
        }

        $list = $this->model_webmention_queue->getUnhandledWebmentions();

        foreach($list as $entry){
            $data['list'][] = array_merge($entry, array(
                'action_retry' => $this->url->link('webmention/receive', 'id='.$entry['webmention_id']. '&action=retry', ''),
                'action_dismiss' => $this->url->link('webmention/receive', 'id='.$entry['webmention_id']. '&action=dismiss', ''),
                'action_approve' => $this->url->link('webmention/receive', 'id='.$entry['webmention_id']. '&action=approve', '')
            ));

        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/webmention/list.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/webmention/list.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/webmention/list.tpl', $data));
        }

    }

    private function receive_webmention(){

        $source = $this->request->post['source'];
        $target = $this->request->post['target'];
        $vouch = $this->request->post['vouch'];

        // make sure our source and target are valid urls
        if(!$this->is_valid_url($source) || !$this->is_valid_url($target)){
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
    
        //if the source is approved, i don't need or want the vouch, i just auto accept it and throw the vouch away
        //  or if I am not using vouches, and i have a valid source, and target, i just auto accept
        if ($this->is_approved_source($source) || !USE_VOUCH){
            $this->load->model('webmention/queue');
            $queue_id = $this->model_webmention_queue->addEntry($source, $target, null, '202');

            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');
            $this->response->addHeader('HTTP/1.1 202 Accepted');

            $this->response->setOutput($link);
            exit();
        }
        // if we are using vouch, and there is not vouch, or its invalid,  respond retry with 449
        //  still save webmention in case i want to approve manually later
        if(!$this->is_valid_url($vouch)){
            $this->load->model('webmention/queue');
            $queue_id = $this->model_webmention_queue->addEntry($source, $target, null, '449' );


            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');
            header('HTTP/1.1 449 Retry With vouch');
            exit();
        }
        if($this->is_approved_source($vouch)){
            $this->load->model('webmention/queue');
            $queue_id = $this->model_webmention_queue->addEntry($source, $target, $vouch, '202');


            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');
            $this->response->addHeader('HTTP/1.1 202 Accepted');

            $this->response->setOutput($link);
        }

        // we are using vouch, and they have given us a valid vouch url but its not an acceptable vouch
        //   we queue for moderation
        $this->load->model('webmention/queue');
        // even though we return 202 (as it is pending moderation) we set this to 449 so is not processed as acceptable
        $queue_id = $this->model_webmention_queue->addEntry($source, $target, $vouch, '449' );

        $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

        $this->response->addHeader('Link: <'.$link.'>; rel="status"');
        $this->response->addHeader('HTTP/1.1 202 Accepted');

        $this->response->setOutput($link);


    }

    private function is_approved_source($url){
        if(!USE_VOUCH){
            return true;
        }
        if(strpos($url, 'http://') === 0 && strpos($url, HTTP_SERVER) === 0){
            return true;
        }
        if(strpos($url, 'https://') === 0 && strpos($url, HTTPS_SERVER) === 0){
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

            $webmention_id = $webmention['webmention_id'];


            //TODO this is a big issue, if the webmention sets updated (salmention) after I have already started processing, the result could be a race condition of the two mentions, leading to two or more interactions showing up
            
            // some fetches were taking too long and there would end up being 2 processes running on the same webmention
            // this resulted in double likes, etc 
            // This prevents another run from picking up the same webmentions now.
            $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '102', webmention_status = 'Processing' WHERE webmention_id = ". (int)$webmention_id);

            $source_url = trim($webmention['source_url']);
            $target_url = trim($webmention['target_url']);
            $vouch_url = null;
            if($webmention['vouch_url']){
                $vouch_url = trim($webmention['vouch_url']);
            }

            $editing = FALSE;
            $edit_q = $this->db->query("SELECT * FROM ".DATABASE.".interaction_syndication WHERE webmention_id=".(int)$webmention_id." LIMIT 1");
            if(!empty($edit_q->row)) {
                $interaction_id = $edit_q->row['interaction_id'];
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
                    $action = $this->load->controller('webmention/notification/pushMessage');
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

            //$this->log->write(print_r($real_source_url, true));
            $return_code = curl_getinfo($c, CURLINFO_HTTP_CODE);


            //TODO test if vouch points to source_url

            curl_close($c);
            unset($c);

            if($page_content === FALSE){
                if($editing && $return_code == 410){
                    if(isset($interaction_id)){
                        $this->db->query("UPDATE ". DATABASE.".interaction SET deleted=1 WHERE interaction_id = ". (int)$interaction_id);
                    }

                    //our curl command failed to fetch the source site
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '410', webmention_status = 'Deleted' WHERE webmention_id = ". (int)$webmention_id);

                } else {
                    //our curl command failed to fetch the source site
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Failed To Fetch Source' WHERE webmention_id = ". (int)$webmention_id);
                }

            } elseif(stristr($page_content, $target_url) === FALSE){
                //we could not find the target_url anywhere on the source page.
                $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Not Found At Source' WHERE webmention_id = ". (int)$webmention_id);

                if($editing && isset($interaction_id)){
                    $this->db->query("UPDATE ". DATABASE.".interaction SET deleted=1 WHERE interaction_id = ". (int)$interaction_id);
                }

            } else {
                $mf2_parsed = Mf2\parse($page_content, $real_source_url);
                foreach($mf2_parsed['items'] as $item){
                    $comment_data = IndieWeb\comments\parse($item, $target_url);
                    if(!empty($comment_data['url'])){ //break out of loop if we found one
                        break;
                    }
                }

            //DISABLING PSC parsing as it seems to cause a lot of problems
                //$psc_matches = array();
                //$psc_pattern = '/\(([^ ]+ [^ ]+)\)$/';
                //preg_match($psc_pattern, $comment_data['text'], $psc_matches);

                //if(isset($matches[1]) && !empty($matches[1])){
                    //$this->log->write('matches: '.print_r($matches, true));
                    //$src = 'http://' .str_replace(' ','/', $matches[1]);
                    ////TODO, find real source
                    ////$comment_data['text'] = str_replace(' '.$matches[0], '', $comment_data['text']);
                    //$c = curl_init();
                    //curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                    //curl_setopt($c, CURLOPT_URL, $src);
                    //curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
                    //$real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
                    //$comment_data['url'] = $real_source_url;
                //}

                //$this->log->write('target = ' . $target_url . ' real_source_url = '. $real_source_url);

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
                        $this->load->model('blog/interaction');
                        if($editing){
                            $interaction_id = $this->model_blog_interaction->editWebmention($data, $webmention_id, $comment_data);
                        } else {
                            $interaction_id = $this->model_blog_interaction->addWebmention($data, $webmention_id, $comment_data);
                        }

                        //salmention
                        $res = $this->db->query("SELECT post_id FROM " . DATABASE . ".interactions WHERE interaction_id = '".(int)$interaction_id."' LIMIT 1");
                        if($res->row){
                            $post_id = $res->row['post_id'];
                            if(defined('QUEUED_SEND')){
                                $this->model_webmention_send_queue->addEntry($post_id);
                            } else {
                                $this->load->controller('webmention/queue/sendWebmention', $post_id);
                            }
                        }
                        //end salmention
                    }
                } catch (Exception $e) {
                    if(empty($comment_data['url'])){
                        $comment_data['url'] = $real_source_url;
                    }

		    $interaction_type = 'mention';
		    if(isset($comment_data['type']) && $comment_data['type'] == 'like'){
		        $interaction_type = 'like';
		    }elseif(isset($comment_data['type']) && $comment_data['type'] == 'tagged'){
		        $interaction_type = 'tagged';
		    }



                    $this->db->query("INSERT INTO ". DATABASE.".interactions SET source_url = '".$comment_data['url']."'".
                        ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                        ((isset($comment_data['tag-of']) && !empty($comment_data['tag-of']))? ", tag_of='".$comment_data['tag-of']."'" : "") .
			", interaction_type='".$interaction_type."'".
			", webmention_id='".$webmention_id."'".
                        "");
                    $interaction_id = $this->db->getLastId();
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('interactions');


                }


            }
            $action = $this->load->controller('webmention/notification/pushMessage');

            $result = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
            $webmention = $result->row;

        } //end while($webmention) loop
    }
}
?>
