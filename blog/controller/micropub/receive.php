<?php  
class ControllerMicropubReceive extends Controller {
	public function index() {
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

                    if(isset($this->request->post['type']) && $this->request->post['type'] == 'article'){
                        $this->createArticle();
                    } elseif(isset($_FILES['photo']) && !empty($_FILES['photo'])){
                        $this->createPhoto();
                    } elseif(isset($this->request->post['url']) && !empty($this->request->post['url'])){
                        $this->editNote();
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

    private function editNote(){
        $post = $this->getPostByURL($this->request->post['url']);
        if($post && isset($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($post['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$post['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $post['permalink']);
            $this->response->setOutput($post['permalink']);
        }
    }

    private function createNote(){
        $this->load->model('blog/note');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['slug'] = $this->request->post['slug'];

        $data['slug'] = '_';
        if(isset($this->request->post['slug'])) {
            $data['slug'] = $this->request->post['slug'];
        }
        //TODO
        // $this->request->post['h'];
        // $this->request->post['published'];
        // $this->request->post['category'];
        // $this->request->post['location'];
        // $this->request->post['place_name'];
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
        }

        if(isset($this->request->post['syndicate-to']) && !empty($this->request->post['syndicate-to'])){
            $data['syndication_extra'] = '';
            foreach($this->request->post['syndicate-to'] as $synto){
                $data['syndication_extra'] .= '<a href="'.$synto.'"></a>';
            }
        }
        
        $note_id = $this->model_blog_note->newNote($data);
        $this->cache->delete('posts');
        $this->cache->delete('notes');

        $note = $this->model_blog_note->getNote($note_id);

        if($note && isset($this->request->post['syndication']) && !empty($this->request->post['syndication'])){
            $this->load->model('blog/post');
            $this->model_blog_post->addSyndication($note['post_id'], $this->request->post['syndication']);
            $this->cache->delete('post.'.$note['post_id']);
        }
        // send webmention
        include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';
        $client = new IndieWeb\MentionClient($note['shortlink'], '<a href="'.$note['replyto'].'">ReplyTo</a>' . html_entity_decode($note['body'].$note['syndication_extra']) );
        $client->debug(false);
        $sent = $client->sendSupportedMentions();
        $urls = $client->getReturnedUrls();
        foreach($urls as $syn_url){
            $this->model_blog_post->addSyndication($note['post_id'], $syn_url);
        }
        $this->cache->delete('post.'.$note['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $note['permalink']);
        $this->response->setOutput($note['permalink']);
	}

    private function createArticle(){
        $this->log->write('called createArticle');
        $this->load->model('blog/article');
        $data = array();
        $data['body'] = $this->request->post['content'];
        $data['title'] = $this->request->post['title'];
        $data['slug'] = $this->request->post['slug'];

        //TODO
        // $this->request->post['h'];
        // $this->request->post['published'];
        // $this->request->post['category'];
        // $this->request->post['location'];
        // $this->request->post['place_name'];
        if(isset($this->request->post['in-reply-to'])){
            $data['replyto'] = $this->request->post['in-reply-to'];
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
        // send webmention
        include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';
        $client = new IndieWeb\MentionClient($article['shortlink'], '<a href="'.$article['replyto'].'">ReplyTo</a>' . html_entity_decode($article['body'].$article['syndication_extra']) );
        $client->debug(false);
        $sent = $client->sendSupportedMentions();
        $urls = $client->getReturnedUrls();
        foreach($urls as $syn_url){
            $this->model_blog_post->addSyndication($article['post_id'], $syn_url);
        }
        $this->cache->delete('post.'.$article['post_id']);

        $this->response->addHeader('HTTP/1.1 201 Created');
        $this->response->addHeader('Location: '. $article['permalink']);
        $this->response->setOutput($article['permalink']);
	}

    private function createPhoto(){
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
            // $this->request->post['category'];
            // $this->request->post['location'];
            // $this->request->post['place_name'];
            // $this->request->post['photo'];
            if(isset($this->request->post['in-reply-to'])){
                $data['replyto'] = $this->request->post['in-reply-to'];
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

            // send webmention
            include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

            $client = new IndieWeb\MentionClient($photo['shortlink'], '<a href="'.$photo['replyto'].'">ReplyTo</a>' .
                '<img src="'.$photo['image_file'].'" class="u-photo photo-post" />' .html_entity_decode($photo['body'].$photo['syndication_extra']) );
            $client->debug(false);
            $sent = $client->sendSupportedMentions();
            $urls = $client->getReturnedUrls();
            //$this->log->write(print_r($urls,true));
            foreach($urls as $syn_url){
                $this->model_blog_post->addSyndication($photo['post_id'], $syn_url);
            }
            $this->cache->delete('post.'.$photo['post_id']);

            $this->response->addHeader('HTTP/1.1 201 Created');
            $this->response->addHeader('Location: '. $photo['permalink']);
            $this->response->setOutput($photo['permalink']);
        }
	}

    private function getPostByURL($real_url){
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
