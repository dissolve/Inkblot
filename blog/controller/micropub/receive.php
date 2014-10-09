<?php  
class ControllerMicropubReceive extends Controller {
	public function index() {
        $this->log->write(print_r($this->request->post, true));
        $this->log->write(file_get_contents("php://input"));
        $headers = apache_request_headers();
        if(isset($this->request->post['access_token']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($headers['Authorization'])){
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
            $auth_info = $this->model_auth_token->getAuthFromToken($token);


            if(!empty($auth_info) && in_array('post', explode(' ', $auth_info['scope']))) {

                $token_user = str_replace(array('http://', 'https://'),array('',''), $auth_info['user']);
                $myself = str_replace(array('http://', 'https://'),array('',''), HTTP_SERVER);

                if($token_user == $myself || $token_user.'/' == $myself || $token_user == $myself .'/' ) {

                    //$this->log->write(print_r($this->request->post, true));
                    if(isset($this->request->post['operation']) && strtolower($this->request->post['operation']) == 'delete'){
                        $this->deletePost();
                    } elseif(isset($this->request->post['operation']) && strtolower($this->request->post['operation']) == 'undelete'){
                        $this->undeletePost();
                    } elseif(isset($this->request->post['type']) && $this->request->post['type'] == 'article'){
                        $this->createArticle();
                    } elseif(isset($this->request->post['type']) && $this->request->post['type'] == 'checkin'){
                        $this->createCheckin();
                    } elseif(isset($this->request->post['bookmark']) && !empty($this->request->post['bookmark'])){
                        $this->createBookmark();
                    } elseif(isset($this->request->post['like']) && !empty($this->request->post['like'])){
                        $this->createLike();
                    } elseif(isset($_FILES['photo']) && !empty($_FILES['photo'])){
                        $this->createPhoto();
                    } elseif(isset($this->request->post['operation']) && strtolower($this->request->post['operation']) == 'edit'){
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
        $this->log->write('called deletePost()');
        $post = $this->getPostByURL($this->request->post['url']);
        if($post) {
            $this->load->model('blog/post');
            $this->model_blog_post->deletePost($post['post_id']);

            $this->cache->delete('post.'.$post['post_id']);

            $this->load->model('blog/wmqueue');
            $this->model_blog_wmqueue->addEntry($post['post_id']);

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
            
            if(isset($this->request->post['title'])){
                $post['title'] = $this->request->post['title'];
            } else {
                $post['title'] = '';
            }
            if(isset($this->request->post['content'])){
                $post['body'] = $this->request->post['content'];
            } else {
                $post['body'] = '';
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
        // $this->request->post['published'];
        if(isset($this->request->post['category'])){
            $data['category'] = $this->request->post['category'];
        }
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
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
        }

        if($note && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($note['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$note['post_id']);
        }
        if($note['draft'] != 1){
            $this->load->model('blog/wmqueue');
            $this->model_blog_wmqueue->addEntry($note_id);
        }

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
        // $this->request->post['published'];
        if(isset($this->request->post['category'])){
            $data['category'] = $this->request->post['category'];
        }
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
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
            $this->load->model('blog/wmqueue');
            $this->model_blog_wmqueue->addEntry($article_id);
        }

        $this->cache->delete('post.'.$article['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $article['permalink']);
        $this->response->setOutput($article['permalink']);
    }

    private function createPhoto(){
        //$this->log->write('called createPhoto()');
        $upload_shot = $_FILES['photo'];

        if( $upload_shot['error'] == 0) {

            move_uploaded_file($upload_shot["tmp_name"], DIR_IMAGE .'/uploaded/'. $upload_shot["name"]);

            $this->load->model('blog/photo');
            $data = array();
            $data['image_file'] = '/image/uploaded/'. $upload_shot["name"];
            $data['body'] = $this->request->post['content'];

            //TODO
            // $this->request->post['h'];
            // $this->request->post['published'];
            // $this->request->post['photo'];
            if(isset($this->request->post['category'])){
                $data['category'] = $this->request->post['category'];
            }
            if(isset($this->request->post['in-reply-to'])){
                $data['replyto'] = $this->request->post['in-reply-to'];
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
            $data['syndication_extra'] = '<a href="https://www.brid.gy/publish/twitter"></a>';
            //$data['syndication_extra'] .= '<a href="https://www.brid.gy/publish/facebook"></a>';


            $photo_id = $this->model_blog_photo->newPhoto($data);
            $this->cache->delete('posts');
            $this->cache->delete('photos');

            $photo = $this->model_blog_photo->getPhoto($photo_id);

            $this->load->model('blog/post');
            if($photo && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
                $this->model_blog_post->addSyndication($photo['post_id'], $this->request->post['syndication']);
            }

            $this->load->model('blog/wmqueue');
            $this->model_blog_wmqueue->addEntry($photo_id);

            $this->cache->delete('post.'.$photo['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $photo['permalink']);
            $this->response->setOutput($photo['permalink']);
        }
    }
    private function createBookmark(){
        $this->load->model('blog/note');
        $data = array();
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
        $this->load->model('blog/wmqueue');
        $this->model_blog_wmqueue->addEntry($bookmark_id);

        $this->cache->delete('post.'.$bookmark['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $bookmark['permalink']);
        $this->response->setOutput($bookmark['permalink']);
    }
    private function createCheckin(){
        $this->load->model('blog/note');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['slug'] = $this->request->post['slug'];

        $data['slug'] = '';
        if(isset($this->request->post['slug'])) {
            $data['slug'] = $this->request->post['slug'];
        }

        if(isset($this->request->post['draft'])){
            $data['draft'] = $this->request->post['draft'];
        }

        //TODO
        // $this->request->post['h'];
        // $this->request->post['published'];
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
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
        }
        
        //$this->log->write(print_r($data,true));
        $note_id = $this->model_blog_note->newNote($data);
        //$this->log->write($note_id);
        $this->cache->delete('posts');
        $this->cache->delete('notes');

        $note = $this->model_blog_note->getNote($note_id);

        if($note && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($note['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$note['post_id']);
        }
        if($note['draft'] != 1){
            $this->load->model('blog/wmqueue');
            $this->model_blog_wmqueue->addEntry($note_id);
        }

        $this->cache->delete('post.'.$note['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $note['permalink']);
        $this->response->setOutput($note['permalink']);
    }
    private function createLike(){
        $this->load->model('blog/like');
        $data = array();
        $data['like'] = $this->request->post['like'];


        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
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

        $this->load->model('blog/wmqueue');
        $this->model_blog_wmqueue->addEntry($bookmark_id);

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
            $real_url = trim(str_replace(array(HTTP_SERVER, HTTPS_SERVER),array('',''), $real_url),'/');
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

}
?>
