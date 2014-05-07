<?php  
class ControllerAuthMicropub extends Controller {
	public function index() {
        $headers = apache_request_headers();
        if(isset($this->request->post['access_token']) || isset($headers['Authorization'])){
            $token = $this->request->post['access_token'];
            if(!$token){
                $parts = explode(' ', $headers['Authorization']);
                $token = $parts[1];
            }

            $this->load->model('auth/token');
            $auth_info = $this->model_auth_token->getAuthFromToken($token);

            $this->log->write(print_r($auth_info,true));


        } else {
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
	}

}
?>
