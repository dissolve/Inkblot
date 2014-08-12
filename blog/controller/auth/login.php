<?php  
class ControllerAuthLogin extends Controller {
	public function index() {
        if(isset($this->request->get['code'])){
            //TODO login
            $code = $this->request->get['code'];
            $client_id = $this->url->link('');
            $redir_url = $this->url->link('auth/login');
            $me = $this->request->get['me'];

            $post_data = http_build_query(array(
                'code'          => $code,
                'redirect_uri'  => $redir_url,
                'client_id'     => $client_id
            ));

            $ch = curl_init(AUTH_ENDPOINT);

            if(!$ch){$this->log->write('error with curl_init');}

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            $response = curl_exec($ch);

            $results = array();
            parse_str($response, $results);

            if($results['me'] == $me){
                $this->log->write('success logging in '. $results['me']);
                $this->session->data['user_id'] = $results['me'];
                //$data['success'] = "You are now logged in as ".$results['me'];
            }
            //$this->log->write('me: '. $me);
            //$this->log->write('response: '. $response);

        }
        $this->response->redirect($this->url->link(''));
	}
}
?>
