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

            $this->log->write(print_r($auth_info,true));
            //$this->log->write($token)
            $upload_shot = $_FILES['photo'];
            $this->log->write(print_r($upload_shot['name'],true));

            if( $upload_shot['error'] == 0) {

                move_uploaded_file($upload_shot["tmp_name"], DIR_IMAGE .'/uploaded/'. $upload_shot["name"]);

                $this->load->model('blog/photo');
                $data = array();
                $data['image_file'] = '/image/uploaded/'. $upload_shot["name"];

                $photo_id = $this->model_blog_photo->newPhoto($data);
                $this->cache->delete('posts')
                $this->cache->delete('photos')

                $photo = $this->model_blog_photo->getPhoto($photo_id);

                $this->response->addHeader('HTTP/1.1 201 Created');
                $this->response->addHeader('Location: '. $photo['permalink']);

                $this->response->setOutput($photo['permalink']);
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
