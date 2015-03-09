<?php  
class ControllerMicropubReceive extends Controller {
	public function index() {
        //$this->log->write(print_r($this->request->post, true));
        //$this->log->write(file_get_contents("php://input"));
        $supported_array = array(
                "edit" => "https://ben.thatmustbe.me/edit?url={url}",
                "new" => "https://ben.thatmustbe.me/new",
                "reply" => "https://ben.thatmustbe.me/new?reply_to={url}",
                "repost" => "https://ben.thatmustbe.me/new?url={url}",
                "bookmark" => "https://ben.thatmustbe.me/new?type=bookmark&bookmark={url}",
                "favorite" => "https://ben.thatmustbe.me/new?type=like&like-of={url}",
                "like" => "https://ben.thatmustbe.me/new?type=like&like={url}",
                "delete" => "https://ben.thatmustbe.me/delete?url={url}",
                "undelete" => "https://ben.thatmustbe.me/undelete?url={url}");

        $supported_syndication_array = array(
            "syndicate-to[]=https://www.brid.gy/publish/twitter",
            "syndicate-to[]=https://www.brid.gy/publish/facebook",
            "mp-syndicate-to[]=https://www.brid.gy/publish/twitter",
            "mp-syndicate-to[]=https://www.brid.gy/publish/facebook"
        );
        
        if(isset($this->request->get['q']) && $this->request->get['q'] == 'actions'){
            if($this->request->server['HTTP_ACCEPT'] == 'application/json'){
                $json = $supported_array;
                $this->response->setOutput(json_encode($json));
            } else {
                $build_array = array();
                foreach($supported_array as $type => $value){
                    $build_array[] = $type . '='. urlencode($value);
                }
                $supported = implode('&', $build_array);

                $this->response->setOutput($supported);
            }

        } elseif(isset($this->request->get['q']) && $this->request->get['q'] == 'syndicate-to'){
            $supported = implode('&', $supported_syndication_array);
            $this->response->setOutput($supported);

        } elseif(isset($this->request->get['q']) && $this->request->get['q'] == 'mp-syndicate-to'){
            $supported = implode('&', $supported_syndication_array);
            $this->response->setOutput($supported);

        } elseif(isset($this->request->get['q']) && $this->request->get['q'] == 'json_actions'){
            $json = $supported_array;
			$this->response->setOutput(json_encode($json));
        } elseif(isset($this->request->get['q']) && $this->request->get['q'] == 'indie-config'){
            $build_array = array();
            foreach($supported_array as $type => $value){
                $build_array[] = $type . ": '". $value. "'";
            }
            $indieconfig ="
<script>
(function() {
  if (window.parent !== window) {
    window.parent.postMessage(JSON.stringify({
      // The endpoint you use to write replies
".implode(",\n", $build_array) ."
    }), '*');
  }
}());
</script>";
			$this->response->setOutput($indieconfig);


        } else {
            $headers = apache_request_headers();
            if(isset($this->request->post['access_token']) || (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) || isset($headers['Authorization'])){
                $token = $this->request->post['access_token'];
                if(!$token){
                    $parts = explode(' ', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
                    $token = $parts[1];
                }
                if(!$token){
                    $parts = explode(' ', $headers['Authorization']);
                    $token = $parts[1];
                }

                $this->load->model('auth/token');
                $auth_info = $this->model_auth_token->getAuthFromToken(urldecode($token));


                if(!empty($auth_info) && in_array('post', explode(' ', $auth_info['scope']))) {

                    $token_user = str_replace(array('http://', 'https://'),array('',''), $auth_info['user']);
                    $myself = str_replace(array('http://', 'https://'),array('',''), HTTP_SERVER);

                    if($token_user == $myself || $token_user.'/' == $myself || $token_user == $myself .'/' ) {

                        if(isset($this->request->get['url']) && !empty($this->request->get['url'])){
                            $this->getPost();
                        //$this->log->write(print_r($this->request->post, true));
                        } elseif(isset($this->request->post['mp-action']) && strtolower($this->request->post['mp-action']) == 'delete'){
                            $this->deletePost();
                        } elseif(isset($this->request->post['mp-action']) && strtolower($this->request->post['mp-action']) == 'undelete'){
                            $this->undeletePost();
                        } elseif(isset($this->request->post['mp-type']) && $this->request->post['mp-type'] == 'article'){
                            $this->createArticle();
                        } elseif(isset($this->request->post['mp-type']) && $this->request->post['mp-type'] == 'checkin'){
                            $this->createCheckin();
                        } elseif(isset($this->request->post['mp-type']) && $this->request->post['mp-type'] == 'rsvp'){
                            $this->createRsvp();
                        } elseif(isset($this->request->post['bookmark']) && !empty($this->request->post['bookmark'])){
                            $this->createBookmark();
                        } elseif(isset($this->request->post['like-of']) && !empty($this->request->post['like-of'])){
                            $this->createLike();
                        } elseif(isset($_FILES['video']) && !empty($_FILES['video'])){
                            $this->createVideo();
                        } elseif(isset($_FILES['audio']) && !empty($_FILES['audio'])){
                            $this->createAudio();
                        } elseif(isset($_FILES['photo']) && !empty($_FILES['photo'])){
                            $this->createPhoto();
                        } elseif(isset($this->request->post['mp-action']) && strtolower($this->request->post['mp-action']) == 'edit'){
                            $this->editPost();
                        } else {
                            $this->createNote();
                        }
                        
                    } else {
                        header('HTTP/1.1 401 Unauthorized');
                        exit();
                    }
                } else {
                    header('HTTP/1.1 401 Unauthorized');
                    exit();
                }
            } else {
                header('HTTP/1.1 401 Unauthorized');
                exit();
            }
        }
    }

    private function getPost(){
        $post = $this->getPostByURL($this->request->get['url']);
        if($post) {
            $this->response->addHeader('HTTP/1.1 200 OK');
            $this->response->setOutput(http_build_query($post));
        }

    }

    private function undeletePost(){
        //$this->log->write('called undeletePost()');
        $post = $this->getPostByURL($this->request->post['url']);
        if($post) {
            $this->load->model('blog/post');
            $this->model_blog_post->undeletePost($post['post_id']);
            $this->cache->delete('post.'.$post['post_id']);
            $this->cache->delete('posts.'.$post['post_id']);

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }

    private function deletePost(){
        $this->log->write('called deletePost() '.$this->request->post['url'] );
        $post = $this->getPostByURL($this->request->post['url']);
        if($post) {
            $this->log->write('found post');
            $this->load->model('blog/post');
            $this->model_blog_post->deletePost($post['post_id']);

            $this->cache->delete('post.'.$post['post_id']);

            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($post['post_id']);

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);


        }
    }

    private function editPost(){
        //$this->log->write('called editPost()');
        $post = $this->getPostByURL($this->request->post['url']);
        if($post){
            //$this->log->write('post set');
            //$this->log->write(print_r($post,true));
            $this->load->model('blog/post');
            if(isset($this->request->post['syndication'])){
                $this->model_blog_post->addSyndication($post['post_id'], $this->request->post['syndication']);
            }

            $simple_editable_fields = array(
                'title' => 'title',
                'content' => 'body',
                'location' => 'location',
                'place_name' => 'place_name',
                'like-of' => 'like-of',
                'bookmark' => 'bookmark',
                'slug' => 'slug');

            if(isset($this->request->post['delete-fields']) && !empty($this->request->post['delete-fields'])){
                foreach($simple_editable_fields as $field_name => $db_name){
                    if(in_array($field_name, $this->request->post['delete-fields'])){
                        $post[$db_name] = '';
                    }
                }
                if(in_array('category', $this->request->post['delete-fields'])){
                    $this->model_blog_post->removeFromAllCategories($post['post_id']);
                }
            }

            foreach($simple_editable_fields as $field_name => $db_name){
                if(isset($this->request->post[$field_name]) && !empty($this->request->post[$field_name])){
                    $post[$db_name] = $this->request->post[$field_name];
                }
            }
            if(isset($this->request->post['category']) && empty($this->request->post['category'])){
                foreach($this->request->post['category'] as $category){
                    $this->model_blog_post->addToCategory($post['post_id'], $category);
                }
            }

            //$this->log->write(print_r($post,true));
            $this->model_blog_post->editPost($post);

            $this->response->addHeader('HTTP/1.1 200 OK');
            //$this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }


    private function createNote(){
        //$this->log->write('called createNote()');
        $this->load->model('blog/note');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['slug'] = $this->request->post['slug'];

        $data['slug'] = '';
        if(isset($this->request->post['slug'])) {
            $data['slug'] = $this->request->post['slug'];
        }

        //if(isset($this->request->post['draft'])){
            //$data['draft'] = $this->request->post['draft'];
        //}

        //TODO
        // $this->request->post['h'];
        if(isset($this->request->post['published'])){
            $data['published'] = $this->request->post['published'];
        }
        if(isset($this->request->post['category'])){
            $data['category'] = $this->request->post['category'];
        }
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
        }
        if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
            $data['location'] = $this->request->post['location'];
        }
        if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
            $data['place_name'] = $this->request->post['place_name'];
        }

        
        //$this->log->write(print_r($data,true));
        $note_id = $this->model_blog_note->newNote($data);
        //$this->log->write($note_id);
        $this->cache->delete('posts');
        $this->cache->delete('notes');

        $note = $this->model_blog_note->getNote($note_id);

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $syn_extra = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                if(strlen($note['body'].$note['permashortcitation']) < 140){
                    $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                } else {
                    $syn_extra .= '<a href="'.$synto.'"></a>';
                }
            }
            $this->model_blog_note->setSyndicationExtra($note['post_id'], $syn_extra);
        } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $syn_extra = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                if(strlen($note['body'].$note['permashortcitation']) < 140){
                    $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                } else {
                    $syn_extra .= '<a href="'.$synto.'"></a>';
                }
            }
            $this->model_blog_note->setSyndicationExtra($note['post_id'], $syn_extra);
        }

        if($note && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($note['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$note['post_id']);
        }
        //if($note['draft'] != 1){
            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($note['post_id'], $this->request->post['vouch']);
        //}

        $this->cache->delete('post.'.$note['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $note['permalink']);
        $this->response->setOutput($note['permalink']);
    }

    private function createArticle(){
        //$this->log->write('called createArticle()');
        //$this->log->write($this->request->post['content']);
        $this->load->model('blog/article');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['title'] = $this->request->post['title'];
        $data['slug'] = $this->request->post['slug'];

        if(isset($this->request->post['draft'])){
            $data['draft'] = $this->request->post['draft'];
        }

        //TODO
        // $this->request->post['h'];
        if(isset($this->request->post['published'])){
            $data['published'] = $this->request->post['published'];
        }
        if(isset($this->request->post['category'])){
            $data['category'] = $this->request->post['category'];
        }
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
        }
        if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
            $data['location'] = $this->request->post['location'];
        }
        if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
            $data['place_name'] = $this->request->post['place_name'];
        }

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        
        $article_id = $this->model_blog_article->newArticle($data);
        $this->cache->delete('posts');
        $this->cache->delete('articles');

        $article = $this->model_blog_article->getArticle($article_id);

        if($article && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($article['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$article['post_id']);
        }

        if($article['draft'] != 1){
            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($article_id, $this->request->post['vouch']);
        }

        $this->cache->delete('post.'.$article['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $article['permalink']);
        $this->response->setOutput($article['permalink']);
    }

    private function createPhoto(){
        //$this->log->write('called createPhoto()');
        
        if(isset($_FILES['photo'])){
            $upload_shot = $_FILES['photo'];
        } else {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }


        if( $upload_shot['error'] == 0) {

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD .'/photo/'. urldecode($upload_shot["name"]));

            $this->load->model('blog/photo');
            $data = array();
            $data['image_file'] = DIR_UPLOAD_REL .'/photo/'. $upload_shot["name"];
            $data['body'] = $this->request->post['content'];

            //TODO
            // $this->request->post['h'];
            // $this->request->post['photo'];
            if(isset($this->request->post['published'])){
                $data['published'] = $this->request->post['published'];
            }
            if(isset($this->request->post['category'])){
                $data['category'] = $this->request->post['category'];
            }
            if(isset($this->request->post['in-reply-to'])){
                $data['replyto'] = $this->request->post['in-reply-to'];
                $this->load->model('webmention/vouch');
                $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
            }
            if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
                $data['location'] = $this->request->post['location'];
            }
            if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
                $data['place_name'] = $this->request->post['place_name'];
            }
            if(isset($this->request->post['rsvp']) && !empty($this->request->post['rsvp'])){
                $inputval = strtolower($this->request->post['rsvp']);
                if($inputval == 'yes'){
                    $data['rsvp'] = 'yes';
                } else {
                    $data['rsvp'] = 'no';
                }
            }


            $photo_id = $this->model_blog_photo->newPhoto($data);
            $this->cache->delete('posts');
            $this->cache->delete('photos');

            $photo = $this->model_blog_photo->getPhoto($photo_id);

            //$data['syndication_extra'] = '<a href="https://www.brid.gy/publish/twitter" class="u-bridgy-omit-link"></a>';
            //$data['syndication_extra'] .= '<a href="https://www.brid.gy/publish/facebook"></a>';
            if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['syndicate-to'] as $synto){
                    if(strlen($photo['body'].$photo['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_photo->setSyndicationExtra($photo['post_id'], $syn_extra);
            } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['mp-syndicate-to'] as $synto){
                    if(strlen($photo['body'].$photo['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_photo->setSyndicationExtra($photo['post_id'], $syn_extra);
            }

            $this->load->model('blog/post');
            if($photo && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
                $this->model_blog_post->addSyndication($photo['post_id'], $this->request->post['syndication']);
            }

            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($photo_id, $this->request->post['vouch']);

            $this->cache->delete('post.'.$photo['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $photo['permalink']);
            $this->response->setOutput($photo['permalink']);
        }
    }
    private function createVideo(){
        //$this->log->write('called createVideo()');
        
        if(isset($_FILES['video'])){
            $upload_shot = $_FILES['video'];
        } else {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }


        if( $upload_shot['error'] == 0) {

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD .'/video/'. urldecode($upload_shot["name"]));

            $this->load->model('blog/video');
            $data = array();
            $data['video_file'] = DIR_UPLOAD_REL . '/video/'. $upload_shot["name"];
            $data['body'] = $this->request->post['content'];

            //TODO
            // $this->request->post['h'];
            // $this->request->post['video'];
            if(isset($this->request->post['published'])){
                $data['published'] = $this->request->post['published'];
            }
            if(isset($this->request->post['category'])){
                $data['category'] = $this->request->post['category'];
            }
            if(isset($this->request->post['in-reply-to'])){
                $data['replyto'] = $this->request->post['in-reply-to'];
                $this->load->model('webmention/vouch');
                $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
            }
            if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
                $data['location'] = $this->request->post['location'];
            }
            if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
                $data['place_name'] = $this->request->post['place_name'];
            }
            if(isset($this->request->post['rsvp']) && !empty($this->request->post['rsvp'])){
                $inputval = strtolower($this->request->post['rsvp']);
                if($inputval == 'yes'){
                    $data['rsvp'] = 'yes';
                } else {
                    $data['rsvp'] = 'no';
                }
            }


            $video_id = $this->model_blog_video->newVideo($data);
            $this->cache->delete('posts');
            $this->cache->delete('videos');

            $video = $this->model_blog_video->getVideo($video_id);

            if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['syndicate-to'] as $synto){
                    if(strlen($video['body'].$video['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_video->setSyndicationExtra($video['post_id'], $syn_extra);
            } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['mp-syndicate-to'] as $synto){
                    if(strlen($video['body'].$video['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_video->setSyndicationExtra($video['post_id'], $syn_extra);
            }

            $this->load->model('blog/post');
            if($video && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
                $this->model_blog_post->addSyndication($video['post_id'], $this->request->post['syndication']);
            }

            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($video_id, $this->request->post['vouch']);

            $this->cache->delete('post.'.$video['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $video['permalink']);
            $this->response->setOutput($video['permalink']);
        }
    }
    private function createAudio(){
        //$this->log->write('called createAudio()');
        
        if(isset($_FILES['audio'])){
            $upload_shot = $_FILES['audio'];
        } else {
            $this->log->write('cannot find file in $_FILES');
            $this->log->write(print_r($_FILES, true));
            header('HTTP/1.1 449 Retry With file');
            exit();
        }


        if( $upload_shot['error'] == 0) {

            move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD .'/audio/'. urldecode($upload_shot["name"]));

            $this->load->model('blog/audio');
            $data = array();
            $data['audio_file'] = DIR_UPLOAD_REL . '/audio/'. $upload_shot["name"];
            $data['body'] = $this->request->post['content'];

            //TODO
            // $this->request->post['h'];
            // $this->request->post['audio'];
            if(isset($this->request->post['published'])){
                $data['published'] = $this->request->post['published'];
            }
            if(isset($this->request->post['category'])){
                $data['category'] = $this->request->post['category'];
            }
            if(isset($this->request->post['in-reply-to'])){
                $data['replyto'] = $this->request->post['in-reply-to'];
                $this->load->model('webmention/vouch');
                $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
            }
            if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
                $data['location'] = $this->request->post['location'];
            }
            if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
                $data['place_name'] = $this->request->post['place_name'];
            }
            if(isset($this->request->post['rsvp']) && !empty($this->request->post['rsvp'])){
                $inputval = strtolower($this->request->post['rsvp']);
                if($inputval == 'yes'){
                    $data['rsvp'] = 'yes';
                } else {
                    $data['rsvp'] = 'no';
                }
            }


            $audio_id = $this->model_blog_audio->newAudio($data);
            $this->cache->delete('posts');
            $this->cache->delete('audios');

            $audio = $this->model_blog_audio->getAudio($audio_id);


            if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['syndicate-to'] as $synto){
                    if(strlen($audio['body'].$audio['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_audio->setSyndicationExtra($audio['post_id'], $syn_extra);
            } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
                $syn_extra = '';
                foreach($this->request->post['mp-syndicate-to'] as $synto){
                    if(strlen($audio['body'].$audio['permashortcitation']) < 140){
                        $syn_extra .= '<a href="'.$synto.'" class="u-bridgy-omit-link"></a>';
                    } else {
                        $syn_extra .= '<a href="'.$synto.'"></a>';
                    }
                }
                $this->model_blog_audio->setSyndicationExtra($audio['post_id'], $syn_extra);
            }

            $this->load->model('blog/post');
            if($audio && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
                $this->model_blog_post->addSyndication($audio['post_id'], $this->request->post['syndication']);
            }

            $this->load->model('webmention/send_queue');
            $this->model_webmention_send_queue->addEntry($audio_id, $this->request->post['vouch']);

            $this->cache->delete('post.'.$audio['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $audio['permalink']);
            $this->response->setOutput($audio['permalink']);
        }
    }

    private function createBookmark(){
        $this->load->model('blog/bookmark');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['bookmark'] = $this->request->post['bookmark'];

        if(isset($this->request->post['category'])){
            $data['category'] = $this->request->post['category'];
        }
        if(isset($this->request->post['description']) && !empty($this->request->post['description'])){
            $data['description'] = $this->request->post['description'];
        }
        if(isset($this->request->post['name']) && !empty($this->request->post['name'])){
            $data['name'] = $this->request->post['name'];
        }

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        //$this->log->write(print_r($data,true));
        $bookmark_id = $this->model_blog_bookmark->newBookmark($data);
        //$this->log->write($bookmark_id);
        $this->cache->delete('posts');
        $this->cache->delete('bookmarks');

        $bookmark = $this->model_blog_bookmark->getBookmark($bookmark_id);

        if($bookmark && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($bookmark['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$bookmark['post_id']);
        }
        $this->load->model('webmention/send_queue');
        $this->model_webmention_send_queue->addEntry($bookmark_id);

        $this->cache->delete('post.'.$bookmark['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $bookmark['permalink']);
        $this->response->setOutput($bookmark['permalink']);
    }

    private function createCheckin(){
        $this->load->model('blog/checkin');
        $data = array();
        $data['body'] = $this->request->post['content'];

        $data['slug'] = '';

        //TODO
        // $this->request->post['h'];
        if(isset($this->request->post['published'])){
            $data['published'] = $this->request->post['published'];
        }
        if(isset($this->request->post['location']) && !empty($this->request->post['location'])){
            $data['location'] = $this->request->post['location'];
        }
        if(isset($this->request->post['place_name']) && !empty($this->request->post['place_name'])){
            $data['place_name'] = $this->request->post['place_name'];
        }

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        //$this->log->write(print_r($data,true));
        $checkin_id = $this->model_blog_checkin->newCheckin($data);
        //$this->log->write($checkin_id);
        $this->cache->delete('posts');
        $this->cache->delete('checkins');

        $checkin = $this->model_blog_checkin->getCheckin($checkin_id);

        if($checkin && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($checkin['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$checkin['post_id']);
        }

        $this->cache->delete('post.'.$checkin['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $checkin['permalink']);
        $this->response->setOutput($checkin['permalink']);
    }

    private function createRsvp(){
        $this->load->model('blog/rsvp');
        $data = array();
        $data['body'] = $this->request->post['content'];

        $data['slug'] = '';

        if(isset($this->request->post['rsvp']) && !empty($this->request->post['rsvp'])){
            $inputval = strtolower($this->request->post['rsvp']);
            if($inputval == 'yes'){
                $data['rsvp'] = 'yes';
            } else {
                $data['rsvp'] = 'no';
            }
        }

        //TODO
        // $this->request->post['h'];
        if(isset($this->request->post['published'])){
            $data['published'] = $this->request->post['published'];
        }
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($data['replyto']);
        }

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        } elseif(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        //$this->log->write(print_r($data,true));
        $rsvp_id = $this->model_blog_rsvp->newRsvp($data);
        //$this->log->write($rsvp_id);
        $this->cache->delete('posts');
        $this->cache->delete('rsvps');

        $rsvp = $this->model_blog_rsvp->getRsvp($rsvp_id);

        if($rsvp && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($rsvp['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$rsvp['post_id']);
        }

        $this->cache->delete('post.'.$rsvp['post_id']);

        //todo add vouch url in here
        $this->load->model('webmention/send_queue');
        $this->model_webmention_send_queue->addEntry($rsvp_id, $this->request->post['vouch']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $rsvp['permalink']);
        $this->response->setOutput($rsvp['permalink']);
    }

    private function createLike(){
        $this->load->model('blog/like');
        $data = array();
        $data['like-of'] = $this->request->post['like-of'];

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        } if(isset($this->request->post['mp-syndicate-to']) && !empty($this->request->post['mp-syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['mp-syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        //$this->log->write(print_r($data,true));
        $like_id = $this->model_blog_like->newLike($data);
        //$this->log->write($note_id);
        $this->cache->delete('posts');
        $this->cache->delete('likes');

        $like = $this->model_blog_like->getLike($like_id);

        if($like && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($like['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$like['post_id']);
        }

        $this->load->model('webmention/send_queue');
        $this->model_webmention_send_queue->addEntry($bookmark_id, $this->request->post['vouch']);

	$this->cache->delete('post.'.$like['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
	$this->response->addHeader('Location: '. $like['permalink']);
	$this->response->setOutput($note['permalink']);
    }

    private function getPostByURL($real_url){
        include DIR_BASE . '/routes.php';

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
            $this->load->model($model);
            $post = $this->registry->get('model_'. str_replace('/', '_', $model))->getByData($data);
            return $post;
        } catch (Exception $e) {
            $this->log->write('failed to parse ' . $real_url . ' as a url for the site');
            return null;
        }


    }
    private function whiteListUrls($content){
        $this->load->model('webmention/vouch');
        $reg_ex_match = '/(href=[\'"](?<href>[^\'"]+)[\'"][^>]*(rel=[\'"](?<rel>[^\'"]+)[\'"])?)/';
        $matches = array();
        preg_match_all($reg_ex_match, $content ,$matches);

        for($i = 0; $i < count($matches['href']); $i++){
            $href = strtolower($matches['href'][$i]);
            $rel = strtolower($matches['rel'][$i]);

            if(strpos($rel,"nofollow") === FALSE){
                $this->model_webmention_vouch->addWhitelistEntry($href);
            }
        }

        $reg_ex_match = '/(rel=[\'"](?<rel>[^\'"]+)[\'"][^>]*href=[\'"](?<href>[^\'"]+)[\'"])/';
        $matches = array();
        preg_match_all($reg_ex_match, $vouch_content ,$matches);
        for($i = 0; $i < count($matches['href']); $i++){
            //$this->log->write('checking '.$href . '   rel '.$rel);
            $href = strtolower($matches['href'][$i]);
            $rel = strtolower($matches['rel'][$i]);

            if(strpos($rel,"nofollow") === FALSE){
                $this->model_webmention_vouch->addWhitelistEntry($href);
            }
        }
    }

}
?>
