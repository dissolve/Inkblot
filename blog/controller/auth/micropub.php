<?php  
class ControllerAuthMicropub extends Controller {
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
                    $this->log->write(print_r($this->request->post,true));

                    
                    if(isset($_FILES['photo']) && !empty($_FILES['photo'])){
                        $upload_shot = $_FILES['photo'];

                        if( $upload_shot['error'] == 0) {

                            move_uploaded_file($upload_shot["tmp_name"], DIR_IMAGE .'/uploaded/'. $upload_shot["name"]);

                            $this->load->model('blog/photo');
                            $data = array();
                            $data['image_file'] = '/image/uploaded/'. $upload_shot["name"];
                            $data['body'] = $this->request->post['content'] . '<a href="https://www.brid.gy/publish/twitter"></a>';

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


                            $photo_id = $this->model_blog_photo->newPhoto($data);
                            $this->cache->delete('posts');
                            $this->cache->delete('photos');

                            $photo = $this->model_blog_photo->getPhoto($photo_id);

                            // send webmention
                            include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';
                            $client = new IndieWeb\MentionClient($note['permalink'], '<a href="'.$photo['replyto'].'">ReplyTo</a>' . html_entity_decode($photo['body']) );
                            $client->debug(false);
                            $sent = $client->sendSupportedMentions();

                            $this->response->addHeader('HTTP/1.1 201 Created');
                            $this->response->addHeader('Location: '. $photo['permalink']);
                            $this->response->setOutput($photo['permalink']);
                        }
                    } else {
                            $this->load->model('blog/note');
                            $data = array();
                            $data['body'] = $this->request->post['content'] . '<a href="https://www.brid.gy/publish/twitter"></a>';
                            $data['slug'] = $this->request->post['slug'];

                            $data['slug'] = 'note';
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

                            $note_id = $this->model_blog_note->newNote($data);
                            $this->cache->delete('posts');
                            $this->cache->delete('notes');

                            $note = $this->model_blog_note->getNote($note_id);

                            // send webmention
                            include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';
                            $client = new IndieWeb\MentionClient($note['permalink'], '<a href="'.$note['replyto'].'">ReplyTo</a>' . html_entity_decode($note['body']));
                            $client->debug(false);
                            $sent = $client->sendSupportedMentions();

                            $this->response->addHeader('HTTP/1.1 201 Created');
                            $this->response->addHeader('Location: '. $note['permalink']);
                            $this->response->setOutput($note['permalink']);
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
            //$this->log->write(print_r($this->request->post,true));
            //$this->log->write(print_r($_SERVER['REDIRECT_HTTP_AUTHORIZATION'],true));
            //$this->log->write(print_r($headers,true));
            header('HTTP/1.1 401 Unauthorized');
            exit();
        }
	}

}
?>
