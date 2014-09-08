<?php  
class ControllerAuthLogin extends Controller {
	public function index() {
        if(isset($this->request->get['code'])){

            $code = $this->request->get['code'];
            $client_id = $this->url->link('');

            $redir_url = $this->url->link('auth/login');
            if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
                $redir_url = $this->url->link('auth/login', 'c='.$this->request->get['c'], '');
            }

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
                //$this->log->write('success logging in '. $results['me']);
                $this->session->data['user_site'] = $results['me'];
                $this->session->data['success'] = "You are now logged in as ".$results['me'];
            }
            //$this->log->write('me: '. $me);
            //$this->log->write('response: '. $response);

        }
        $url = $this->url->link('');
        if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
            $url = $this->url->link($this->request->get['c']);
        }
        $this->response->redirect($url);
	}
}
?>
