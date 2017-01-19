<?php
//TODO:!!!! send out webmention for main feed to pubsub_hub if defined
require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
require_once DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
require_once DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class ControllerMicropubReceive extends Controller {
    public function index()
    {
        $this->log->write(print_r($this->request->post, true));

        if( isset($this->request->get['q']) && !empty($this->request->get['q'])){

            if ($this->request->get['q'] == 'source'){
                $this->query_endpoint_source($this->request->get);
            } else {
                $this->log->write(print_r($this->request->get, true));
                $this->query_endpoint_config($this->request->get['q']);
            }

        } else {
            $headers = apache_request_headers();
            //check that we were even offered an access token
            if (!isset($this->request->post['access_token'])
                && (!isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
                && !isset($headers['Authorization'])) {
                //$this->log->write('err0');
                header('HTTP/1.1 401 Unauthorized');
                exit();
            } else {
                $token = $this->request->post['access_token'];
                if (!$token) {
                    $parts = explode(' ', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
                    $token = $parts[1];
                }
                if (!$token) {
                    $parts = explode(' ', $headers['Authorization']);
                    $token = $parts[1];
                }

                $this->load->model('auth/token');
                $auth_info = $this->model_auth_token->getAuthFromToken(urldecode($token));

                $scopes = explode(' ', $auth_info['scope']);

                $has_post_access = false;
                $has_edit_access = false;
                $has_delete_access = false;
                $has_follow_access = false;
                $has_contacts_access = false;
                $has_register_access = false;

                if (!empty($auth_info)  && isset($auth_info['scope']) && !empty($auth_info['scope'])) {
                    $has_post_access = in_array('post', $scopes);
                    $has_edit_access = in_array('update', $scopes) || in_array('edit', $scopes);
                    $has_delete_access = in_array('delete', $scopes);
                    $has_follow_access = in_array('follow', $scopes);
                    $has_contacts_access = in_array('contacts', $scopes);
                    $has_register_access = in_array('register', $scopes);
                }

                $token_user = str_replace(array('http://', 'https://'), array('',''), $auth_info['user']);
                $myself = str_replace(array('http://', 'https://'), array('',''), HTTP_SERVER);

                if ($token_user != $myself && $token_user . '/' != $myself && $token_user != $myself . '/' ) {
                    //$this->log->write('err1');
                    header('HTTP/1.1 401 Unauthorized');
                    exit();
                } else {

                    $post_data = $this->request->post;
                    if( $_SERVER["CONTENT_TYPE"] == 'application/json') { 
                        // todo: supposedly there is a documented bug of this coming in as HTTP_CONTENT_TYPE
                        //   need to look in to this more

                        try {
                            $data = json_decode(file_get_contents('php://input'), true);
                            $post_data = array();
                            if(isset($data['type'])){
                                $post_data['h'] = $data['type'][0];
                                unset($data['type']);
                            }
                            if(isset($data['properties'])){
                                foreach($data['properties'] as $key => &$value){
                                    if(is_array($value) && !$this->isHash($value) && count($value) == 1 && $key != 'photo' && $key != 'video' && $key != 'audio'){
                                        $value = $value[0];
                                    }
                                }
                                $post_data = array_merge($post_data, $data['properties']);
                                unset($data['properties']);
                            }
                            $post_data = array_merge($post_data, $data);

                        } catch (Exception $e){
                            $post_data = array();
                        }
                    }
                    $this->log->write(print_r($post_data,true));

                    // ----------------------------------------
                    // Handle new micropub endpoint registration
                    // ----------------------------------------
                    if ($this->request->get['register'] && $has_register_access) {
                        $token = $this->request->get['register_token'];
                        $site = $this->request->get['register'];

                        $this->load->model('auth/mpsyndicate');
                        $this->model_auth_mpsyndicate->addSite($site, $token);

                        //TODO:  test all of this

                        $this->response->addHeader('HTTP/1.1 200 OK');
                        $this->response->setOutput("Syndication Target Added!");
                        exit();

                    }
                    // ----------------------------------------
                    // END Handle new micropub endpoint registration
                    // ----------------------------------------

                    $mp_action = '';
                    if (isset($post_data['mp-action']) && !empty($post_data['mp-action'])) {
                        $mp_action = strtolower($post_data['mp-action']);
                    }

                    switch ($mp_action) {
                        case 'delete':
                            if ($has_delete_access) {
                                $this->deletePost($post_data);
                            } else {
                        //$this->log->write('err7');
                                header('HTTP/1.1 401 Insufficient Scope');
                                exit();
                            }
                            break;
                        case 'undelete':
                            // NOTE: should undeletePost() need post access? delete access? both?
                            if ($has_post_access) {
                                $this->undeletePost($post_data);
                            } else {
                        //$this->log->write('err8');
                                header('HTTP/1.1 401 Insufficient Scope');
                                exit();
                            }
                            break;
                            //Classic Edit functionality
                        case 'edit':
                            if ($has_edit_access) {
                                $this->editPost($post_data);
                            } else {
                        //$this->log->write('err9');
                                header('HTTP/1.1 401 Insufficient Scope');
                                exit();
                            }
                            break;
                            //new update function
                        case 'update':
                            if ($has_edit_access) {
                                $this->updatePost($post_data);
                            } else {
                        //$this->log->write('err9');
                                header('HTTP/1.1 401 Insufficient Scope');
                                exit();
                            }
                            break;
                        case 'create':
                        default:
                            if ($has_post_access) {
                                // NOTE: should getPost() need post access?
                                //if (isset($this->request->get['url']) && !empty($this->request->get['url'])) {
                                    //$this->getPost();
                                //} else
                                if (isset($post_data['mp-type']) && $post_data['mp-type'] == 'article') {
                                    $this->createPost($post_data, 'article', $auth_info['client_id']);
                                } elseif (isset($post_data['mp-type']) && $post_data['mp-type'] == 'checkin') {
                                    $this->createPost($post_data, 'checkin', $auth_info['client_id']);
                                } elseif ((isset($post_data['mp-type']) && $post_data['mp-type'] == 'rsvp') || (!isset($post_data['mp-type']) && isset($post_data['rsvp']) && !empty($post_data['rsvp']))) {
                                    $this->createPost($post_data, 'rsvp', $auth_info['client_id']);
                                } elseif (isset($post_data['mp-type']) && $post_data['mp-type'] == 'tag') {
                                    $this->createPost($post_data, 'tag', $auth_info['client_id']);
                                } elseif (isset($post_data['mp-type']) && $post_data['mp-type'] == 'snark') {
                                    $this->createPost($post_data, 'snark', $auth_info['client_id']);
                                } elseif (isset($post_data['weight_value']) && !empty($post_data['weight_value'])) {
                                    $this->createPost($post_data, 'weight', $auth_info['client_id']);
                                } elseif (isset($post_data['weight']) && !empty($post_data['weight'])) {
                                    $this->createPost($post_data, 'weight', $auth_info['client_id']);
                                } elseif (isset($post_data['bookmark-of']) && !empty($post_data['bookmark-of'])) {
                                    $this->createPost($post_data, 'bookmark', $auth_info['client_id']);
                                } elseif (isset($post_data['like-of']) && !empty($post_data['like-of'])) {
                                    $this->createPost($post_data, 'like', $auth_info['client_id']);
                                } elseif (isset($_FILES['video']) && !empty($_FILES['video'])) {
                                    $this->createPost($post_data, 'video', $auth_info['client_id']);
                                } elseif (isset($_FILES['audio']) && !empty($_FILES['audio'])) {
                                    $this->createPost($post_data, 'audio', $auth_info['client_id']);
                                } elseif (isset($_FILES['photo']) && !empty($_FILES['photo'])) {
                                    $this->createPost($post_data, 'photo', $auth_info['client_id']);
                                } else {
                                    $this->createPost($post_data, 'note', $auth_info['client_id']);
                                }
                            } else {
                        //$this->log->write('err10');
                                header('HTTP/1.1 401 Insufficient Scope');
                                exit();
                            }
                            break;
                    } //end switch case

                }  // end check for token is my own
            }  // end check for access token offered
        } //end else from endpoint data lookup
    } //end index funciton

    private function getPost()
    {
        $post = $this->getPostByURL($this->request->get['url']);
        if ($post) {
            $this->response->addHeader('HTTP/1.1 200 OK');
            if ($this->request->server['HTTP_ACCEPT'] == 'application/json') {
                //TODO need to convert $post to mf2json UGH
                $this->response->setOutput(json_encode($post));
            } else {
                $this->response->setOutput(http_build_query($post));
            }
        } else {
            return array();
        }

    }

    private function undeletePost($post_data)
    {
        //$this->log->write('called undeletePost()');
        $post = $this->getPostByURL($post_data['url']);
        if ($post) {
            $this->load->model('blog/post');
            $this->model_blog_post->undeletePost($post['id']);
            $this->cache->delete('post.' . $post['id']);
            $this->cache->delete('posts.' . $post['id']);

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }

    private function deletePost($post_data)
    {
        $this->log->write('called deletePost() ' . $post_data['url']);
        $post = $this->getPostByURL($post_data['url']);
        if ($post) {
            $this->log->write('found post');
            $this->load->model('blog/post');
            $this->model_blog_post->deletePost($post['id']);

            $this->cache->delete('post.' . $post['id']);

            $this->load->model('webmention/send_queue');
            if (defined('QUEUED_SEND')) {
                $this->model_webmention_send_queue->addEntry($post['id']);
            } else {
                $this->load->controller('webmention/queue/sendWebmention', $post['id']);
            }

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);


        }
    }

    private function updatePost($post_data)
    {
        $this->log->write('called updatePost() with ' . print_r($post_data, true));
        $this->load->model('blog/post');
        $post = $this->getPostByURL($post_data['url']);
        if ($post) {
            $old_body = $post['content'];
            $post_id = $post['id'];
            
            if(isset($post_data['delete'])){
                if(!is_array($post_data['delete'])){
                    header('HTTP/1.1 400 Invalid Request');
                    exit();
                }
                if($this->isHash($post_data['delete'])){
                    foreach($post_data['delete'] as $field => $value){
                        if(!is_array($value)){
                            //this submission was not according to spec!, tsk tsk tsk
                            $value = array($value);
                        }
                        $this->model_blog_post->removePropertyValues($post['id'], $field, $value);
                    }
                } else {
                    foreach($post_data['delete'] as $field){
                        $this->model_blog_post->deleteProperty($post['id'], $field);
                    }
                }

            }
            if(isset($post_data['replace'])){
                if(!is_array($post_data['replace'])){
                    header('HTTP/1.1 400 Invalid Request');
                    exit();
                }
                foreach($post_data['replace'] as $field => $value){
                    $this->model_blog_post->setProperty($post['id'], $field, $value);
                }

            }
            if(isset($post_data['add'])){

                if(!is_array($post_data['add'])){
                    header('HTTP/1.1 400 Invalid Request');
                    exit();
                }
                foreach($post_data['add'] as $field => $value){
                    if(is_array($value)){
                        foreach($value as $val){
                            $this->model_blog_post->addProperty($post['id'], $field, $val);
                        }
                    } else {
                        $this->model_blog_post->addProperty($post['id'], $field, $value);
                    }
                }
            }

            $this->load->model('blog/post');
            if (isset($post_data['syndication'])) {
                $this->model_blog_post->addSyndication($post['id'], $post_data['syndication']);
            }



            $this->load->model('webmention/send_queue');
            if (defined('QUEUED_SEND')) {
                $this->model_webmention_send_queue->addEntry($post['id']);
            } else {
                $this->load->controller('webmention/queue/sendWebmention', $post['id'], $old_body);
            }

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }


    private function editPost($post_data)
    {
        //$this->log->write('called editPost()');
        $post = $this->getPostByURL($post_data['url']);
        if ($post) {
            $old_body = $post['content'];
            //$this->log->write('post set');
            //$this->log->write(print_r($post,true));
            $this->load->model('blog/post');
            if (isset($post_data['syndication'])) {
                $this->model_blog_post->addSyndication($post['id'], $post_data['syndication']);
            }

            $simple_editable_fields = array(
                'name' => 'name',
                'content' => 'content',
                'location' => 'location',
                'place_name' => 'place_name',
                'like-of' => 'like-of',
                'bookmark' => 'bookmark-of',
                'slug' => 'slug');

            if (isset($post_data['delete-fields']) && !empty($post_data['delete-fields'])) {
                foreach ($simple_editable_fields as $field_name => $db_name) {
                    if (in_array($field_name, $post_data['delete-fields'])) {
                        $post[$db_name] = '';
                    }
                }
                if (in_array('category', $post_data['delete-fields'])) {
                    $this->model_blog_post->removeFromAllCategories($post['id']);
                }
            }

            foreach ($simple_editable_fields as $field_name => $db_name) {
                if (isset($post_data[$field_name]) && !empty($post_data[$field_name])) {
                    $post[$db_name] = $post_data[$field_name];
                }
            }
            if (isset($post_data['category']) && !empty($post_data['category'])) {
                if(is_array($post_data['category'])){
                    foreach ($post_data['category'] as $category) {
                        $this->model_blog_post->addToCategory($post['id'], $category);
                    }
                } else {
                    $categories = explode(',', urldecode($post_data['category']));
                    $this->log->write(print_r($categories));
                    foreach ($categories as $category) {
                        $this->model_blog_post->addToCategory($post['id'], $category);
                    }
                }
            }

            //$this->log->write(print_r($post,true));
            $this->model_blog_post->editPost($post);

            $this->load->model('webmention/send_queue');
            if (defined('QUEUED_SEND')) {
                $this->model_webmention_send_queue->addEntry($post['id']);
            } else {
                $this->load->controller('webmention/queue/sendWebmention', $post['id'], $old_body);
            }

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }


    private function createPost($post_data, $type, $client_id = null)
    {

        $this->load->model('blog/post');

        $data = array();

        if (isset($_FILES['photo'])) {
            $upload_shot = $_FILES['photo'];
            if ( $upload_shot['error'] != 0) {
                header('HTTP/1.1 500 Error');
                exit();
            }

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD . '/photo/' . urldecode($upload_shot["name"]));

            $data['photo'] = DIR_UPLOAD_REL . '/photo/' . $upload_shot["name"];
        } elseif (isset($post_data['photo'])){
            $data['photo'] =  $post_data["photo"];

        }

        if (isset($_FILES['video'])) {
            $upload_shot = $_FILES['video'];
            if ( $upload_shot['error'] != 0) {
                header('HTTP/1.1 500 Error');
                exit();
            }

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD . '/video/' . urldecode($upload_shot["name"]));

            $data['video'] = DIR_UPLOAD_REL . '/video/' . $upload_shot["name"];
        } elseif (isset($post_data['video'])){
            $data['video'] =  $post_data["video"];
        }
        if (isset($_FILES['audio'])) {
            $upload_shot = $_FILES['audio'];
            if ( $upload_shot['error'] != 0) {
                header('HTTP/1.1 500 Error');
                exit();
            }

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD . '/audio/' . urldecode($upload_shot["name"]));

            $data['audio'] = DIR_UPLOAD_REL . '/audio/' . $upload_shot["name"];
        } elseif (isset($post_data['audio'])){
            $data['audio'] =  $post_data["audio"];
        }

        if ($type == 'photo' && !isset($data['photo'])) {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }

        if ($type == 'video' && !isset($data['video'])) {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }
        if ($type == 'audio' && !isset($data['audio'])) {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }

        //TODO
        // $this->request->post['h'];

        if (isset($post_data['content'])) {
            $content_data = $post_data['content'];
            if(is_array($content_data) && !$this->isHash($content_data)){
                $content_data = $content_data[0];
            }
            if($this->isHash($content_data) && isset($content_data['html'])){
                $data['content'] = $content_data['html'];
            } else {
                $data['content'] = htmlentities($content_data);
            }
        } else {
            $data['content'] = '';
        }
        if (isset($post_data['published'])) {
            $data['published'] = $post_data['published'];
        }
        if (isset($post_data['slug'])) {
            $data['slug'] = $post_data['slug'];
        } else {
            $data['slug'] = '';
        }
        if (isset($post_data['draft'])) {
            $data['draft'] = $post_data['draft'];
        }
        if (isset($post_data['like-of'])) {
            $data['like-of'] = $post_data['like-of'];
        }
        if (isset($post_data['bookmark-of'])) {
            $data['bookmark-of'] = $post_data['bookmark-of'];
        }
        if (isset($post_data['in-reply-to'])) {
            $data['in-reply-to'] = $post_data['in-reply-to'];
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($data['in-reply-to']);
        }
        if (isset($post_data['tag-of'])) {
            //TODO: correct this once I have the DB updated
            $data['in-reply-to'] = $post_data['tag-of'];
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($data['in-reply-to']);
        }
        if (isset($post_data['name'])) {
            $data['name'] = $post_data['name'];
        }
        if (isset($post_data['description'])) {
            $data['description'] = $post_data['description'];
        }
        if (isset($post_data['weight'])) {
            $data['weight'] = $post_data['weight'];
        }
        if (isset($post_data['location'])) {
            $data['location'] = $post_data['location'];
        }
        if (isset($post_data['weight'])) {

            $data['weight_value'] = $post_data['weight']['value'];
            $data['weight_unit'] = $post_data['weight']['unit'];
        }
        if (isset($post_data['weight_value'])) {
            $data['weight_value'] = $post_data['weight_value'];
        }
        if (isset($post_data['weight_unit'])) {
            $data['weight_unit'] = $post_data['weight_unit'];
        }
        if (isset($post_data['place_name'])) {
            $data['place_name'] = $post_data['place_name'];
        }
        if ($type == 'rsvp' && isset($post_data['rsvp']) && !empty($post_data['rsvp'])) {
            $inputval = strtolower($post_data['rsvp']);
            if ($inputval == 'yes') {
                $data['rsvp'] = 'yes';
            } else {
                $data['rsvp'] = 'no';
            }
        }

        if (isset($post_data['mp-syndicate-to']) && !empty($post_data['mp-syndicate-to'])) {
            $data['syndication_extra'] = '';
            foreach ($post_data['mp-syndicate-to'] as $synto) {
                $data['syndication_extra'] .= '<a href="' . $synto . '" class="u-category"></a>';
            }
        }

        if ($client_id) {
            $data['created_by'] = $client_id;
        }

        $post_id = $this->model_blog_post->newPost($type, $data);

        if (isset($post_data['category']) && !empty($post_data['category'])) {
            if(is_array($post_data['category'])){
                foreach ($post_data['category'] as $category) {
                    $this->model_blog_post->addToCategory($post_id, $category);
                }
            } else {
                $categories = explode(',', urldecode($post_data['category']));
                //$this->log->write(print_r($categories));
                foreach ($categories as $category) {
                    $this->model_blog_post->addToCategory($post_id, $category);
                }
            }
        }
        $this->cache->delete('posts');

        $post = $this->model_blog_post->getPost($post_id);

        if ($post && isset($post_data['syndication']) && !empty($post_data['syndication'])) {
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($post['id'], $post_data['syndication']);
        }

        $this->syndicateByMp($post_data, $post['shortlink'], $post_id);

        $this->load->model('webmention/send_queue');
        if (defined('QUEUED_SEND')) {
            $this->model_webmention_send_queue->addEntry($post_id);
        } else {
            $this->load->controller('webmention/queue/sendWebmention', $post_id);
        }

        $this->cache->delete('post.' . $post['id']);


        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: ' . $post['permalink']);
        $this->response->setOutput($post['permalink']);
    }

    private function getPostByURL($real_url)
    {
        include DIR_BASE . '/routes.php';

        $data = array();
        foreach ($advanced_routes as $adv_route) {
            $matches = array();
            $real_url = ltrim(str_replace(array(HTTP_SERVER, HTTPS_SERVER), array('',''), $real_url), '/');
            preg_match($adv_route['expression'], $real_url, $matches);
            if (!empty($matches)) {
                //$model = $adv_route['controller'];
                foreach ($matches as $field => $value) {
                    $data[$field] = $value;
                }
            }
        }

        try {
            $this->load->model('blog/post');
            $post = $this->model_blog_post->getPostByData($data);
            return $post;
        } catch (Exception $e) {
            $this->log->write('failed to parse ' . $real_url . ' as a url for the site');
            return null;
        }


    }
    private function whiteListUrls($content)
    {
        $this->load->model('webmention/vouch');
        $reg_ex_match = '/(href=[\'"](?<href>[^\'"]+)[\'"][^>]*(rel=[\'"](?<rel>[^\'"]+)[\'"])?)/';
        $matches = array();
        preg_match_all($reg_ex_match, $content, $matches);

        for ($i = 0; $i < count($matches['href']); $i++) {
            $href = strtolower($matches['href'][$i]);
            $rel = strtolower($matches['rel'][$i]);

            if (strpos($rel, "nofollow") === false) {
                $this->model_webmention_vouch->addWhitelistEntry($href);
            }
        }

        $reg_ex_match = '/(rel=[\'"](?<rel>[^\'"]+)[\'"][^>]*href=[\'"](?<href>[^\'"]+)[\'"])/';
        $matches = array();
        preg_match_all($reg_ex_match, $vouch_content, $matches);
        for ($i = 0; $i < count($matches['href']); $i++) {
            //$this->log->write('checking '.$href . '   rel '.$rel);
            $href = strtolower($matches['href'][$i]);
            $rel = strtolower($matches['rel'][$i]);

            if (strpos($rel, "nofollow") === false) {
                $this->model_webmention_vouch->addWhitelistEntry($href);
            }
        }
    }


    private function syndicateByMp($data, $url, $post_id)
    {
        //$this->log->write('called syndicateByMp with '. $url);
        //$this->log->write(print_r($data,true));
        $this->load->model('blog/post');
        $this->load->model('auth/mpsyndicate');
        $mp_syndication_targets = $this->model_auth_mpsyndicate->getSiteList();

        $data['url'] = $url;

        $syndicate_tos = $data['syndicate-to'];
        if (empty($syndicate_tos)) {
            $syndicate_tos = $data['mp-syndicate-to'];
        }
        unset($data['syndicate-to']);
        unset($data['mp-syndicate-to']);
        //we want to make sure we don't relay syndicate-to values

        foreach ($syndicate_tos as $mp_target) {
            if (in_array($mp_target, $mp_syndication_targets)) {
                $site_data = $this->model_auth_mpsyndicate->getDataForName($mp_target);

                $token = $site_data['token'];

                $micropub_endpoint = IndieAuth\Client::discoverMicropubEndpoint($mp_target);
                $ch = curl_init($micropub_endpoint);

                if (!$ch) {
                    $this->log->write('error with curl_init');
                }

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $body = curl_exec($ch);
                //$this->log->write(' -------------' . print_r($body, true) . '-------------------' );
                $headers = curl_getinfo($ch);

                if ($headers['http_code'] == 201) {
                    //$this->log->write('responded with  '. $headers['redirect_url']);
                    $matches = null;
                    preg_match('/Location\s*:\s*(https?:\/\/[^\s]+)/', $body, $matches);
                    if ($matches && isset($matches[1])) {
                        $this->model_blog_post->addSyndication($post_id, $matches[1]);
                    }
                    //$this->log->write('all_headers  '. print_r($headers, true));

                } else {
                    $this->log->write('error got http_code  ' . $headers['http_code']);

                }

            }
        }

    }

    //private function convert_post_to_mf2_json

    // assumes q=content has already verified to be set
    private function query_endpoint_source($getdata){
        //$this->log->write(print_r($getdata,true));

        $this->response->addHeader( 'Content-Type: application/json');

        $this->load->model('blog/category');

        $result = array();

        if(isset($getdata['properties']) && !is_array($getdata['properties'])){
            $getdata['properties'] = array($getdata['properties']);
        }


        if(isset($getdata['url'])){
            $post = $this->getPostByURL($getdata['url']);
            if ($post) {
                $result['properties'] = array();
                
                foreach($post as $key => $value){
                    if(!empty($getdata['properties'])){
                        if(!in_array($key, $getdata['properties'])){
                            $value = null;// shortcut to not add this to the properties array;
                        }
                    }
                    if(!empty($value)){
                        if(is_array($value)){
                            $result['properties'][$key] = $value;
                        } else {
                            $result['properties'][$key] = array($value);
                        }
                    }
                }

                $categories = $this->model_blog_category->getCategoriesForPost($post['id']);
                if(!empty($categories)){
                    $result['properties']['category'] = array();
                    foreach($categories as $category){
                        $result['properties']['category'][]= $category['name'];
                    }
                }

            }

        }
        $this->response->setOutput(json_encode($result));

    } // end function 



    private function query_endpoint_config($query){


        $config = array (
            'media-endpoint' => $this->url->link('micropub/mediaendpoint'), 

            'syndicate-to' => array(
                array(
                    'name' => 'Brid.gy Twitter',
                    'uid' => 'https://www.brid.gy/publish/twitter'
                ),
                array(
                    'name' => 'Brid.gy FaceBook',
                    'uid' => 'https://www.brid.gy/publish/facebook'
                ),
                array(
                    'name' => 'IndieNews',
                    'uid' => 'http://news.indiewebcamp.com/en'
                ),
            ),
            
            // include indie-config like actions here

            'actions' => array(
                "edit" => "https://ben.thatmustbe.me/edit?url={url}",
                "new" => "https://ben.thatmustbe.me/new",
                "reply" => "https://ben.thatmustbe.me/new?in-reply-to={url}",
                "repost" => "https://ben.thatmustbe.me/new?url={url}",
                "bookmark" => "https://ben.thatmustbe.me/new?type=bookmark&bookmark-of={url}",
                "favorite" => "https://ben.thatmustbe.me/new?type=like&like-of={url}",
                "like" => "https://ben.thatmustbe.me/new?type=like&like={url}",
                "delete" => "https://ben.thatmustbe.me/delete?url={url}",
                "undelete" => "https://ben.thatmustbe.me/undelete?url={url}"
            ),

            //advertise that I support both form an json encoding
            'format' => array('application/x-www-form-urlencoded', 'application/json'),

            //TODO consider adding format to fields if needed
            'fields' => array(
                 'bookmark-of'      => array( 'type' => array('url')),
                 'category'         => array( 'type' => array('text')),
                 'content'          => array( 'type' => array('text')),
                 'in-reply-to'      => array( 'type' => array('url')),
                 'like-of'          => array( 'type' => array('url')),
                 'location'         => array( 'type' => array('text', 'url')),
                 'location-name'    => array( 'type' => array('text')),
                 'mp-type'          => array( 
                                    'type' => array('text'),
                                    'values' => array('article','note','snark','checkin','rsvp','tag')
                                    ),
                 'name'             => array( 'type' => array('text')),
                 'repost-of'        => array( 'type' => array('url')),
                 'slug'             => array( 'type' => array('text')),
                 'summary'          => array( 'type' => array('text')),
                 'tag-of'           => array( 'type' => array('url')),
            )
        );



        if($query == 'mp-syndicate-to'){
            $query = 'syndication-to';
        }
        if($query == 'json_actions'){
            $query = 'actions';
        }


        //this is for syndication endpoints added by micropub and syndicate via micropub
        $this->load->model('auth/mpsyndicate');
        $mp_syndication_targets = $this->model_auth_mpsyndicate->getSiteList();
        foreach ($mp_syndication_targets as $target) {
            $config['syndicate-to'][] = array('name' => $target['name'], 'uid' => $target['url']);

        }
    
        if($query == 'config'){
            $this->response->addHeader( 'Content-Type: application/json');
            $this->response->setOutput(json_encode($config));

        //queries for specific fields
        } elseif(isset($config[$query])){
            $this->response->addHeader( 'Content-Type: application/json');
            $json = array( $query => $config[$query]);
            $this->response->setOutput(json_encode($json));


        } elseif ($query == 'indie-config') {

            $build_array = array();
            foreach ($config['actions'] as $type => $value) {
                $build_array[] = $type . ": '" . $value . "'";
            }
            $indieconfig = "
<script>
(function() {
  if (window.parent !== window) {
    window.parent.postMessage(JSON.stringify({
      // The endpoint you use to write replies
" . implode(",\n", $build_array) . "
    }), '*');
  }
}());
</script>";
            $this->response->setOutput($indieconfig);

        } // end indie-config block
    } // end function

    private function isHash(array $in)
    {
        return is_array($in) && count(array_filter(array_keys($in), 'is_string')) > 0;
    }

}
