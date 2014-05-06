<?php  
class ControllerAuthToken extends Controller {
	public function index() {
        if(isset($this->request->post['code']) && 
            isset($this->request->post['me']) &&
            isset($this->request->post['redirect_uri']) &&
            isset($this->request->post['client_id']) &&
            isset($this->request->post['state'])){


            $post_data = http_build_query(array(
                'code'          => $this->request->post['code'],
                'me'            => $this->request->post['me'],
                'redirect_uri'  => $this->request->post['redirect_uri'],
                'client_id'     => $this->request->post['client_id'],
                'state'         => $this->request->post['state']
            ));


            $ch = curl_init(AUTH_ENDPOINT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);

            $this->log->write('response from Auth endpoint: ' . $response);

            $results = parse_str($response);

            if($results['me']){
                $user = $results['me'];
                $scope = $results['scope'];
                $client_id = $this->request->post['client_id'];

                $this->load->model('auth/token');
                $token = $this->model_auth_token->newToken($user, $scope, $client_id);

                $this->response->setOutput(http_build_query(array(
                    'access_token' => $token,
                    'scope' => $scope,
                    'me' => $user)));
            } else {
                header('HTTP/1.1 400 Bad Request');
                exit();
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
	}

}
?>
