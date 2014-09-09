<?php  

include DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
include DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
include DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class ControllerAuthLogin extends Controller {

	public function index() {

        $me = $this->request->get['me'];

        $controller = (isset($this->request->get['c']) ? $this->request->get['c'] : null) ; 

        $scope = (isset($this->request->get['scope']) ? $this->request->get['scope'] : null) ; 
        
        // to simplify things lets figure out the URL to redirect to if we have any problems
        $fail_url = $this->url->link('');
        if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            $fail_url = $_SERVER['HTTP_REFERER'];
        }

        // make sure they actually submitted something
        if(!empty($me)){
        
            //clean up the url given to us, just in case
            $me = trim($me);
            if(strpos($me, 'http') !== 0){
                $me = 'http://'.$me;
            }

            //look up user's auth provider
            $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);

            if(!$auth_endpoint){
                $this->session->data['error'] = 'No Auth Endpoint Found';
                $this->response->redirect($fail_url);
            } else {

                $redir_url = $this->url->link('auth/login/callback', ($controller ? 'c='.$controller : ''), '');
                if($scope){
                    $redir_url = $this->url->link('auth/login/tokencallback', ($controller ? 'c='.$controller : ''), '');
                }

                //build our get request
                $data_array = array(
                    'me' => $me,
                    'redirect_uri' => $redir_url,
                    'response_type' => 'code',
                    'state' => substr(md5($me.$this->url->link('')),0,8),
                    'client_id' => $this->url->link('')
                );
                if($scope){
                    $data_array['scope'] = $scope;
                }

                $get_data = http_build_query($data_array);

                //redirect to their provider
                $this->response->redirect($auth_endpoint . (strpos($auth_endpoint, '?') === false ? '?' : '&') . $get_data);
            }
            
        } else {
            $this->response->redirect($fail_url);
        }
    }

	public function callback() {

        // first figure out where we are going after we process
        $url = $this->url->link('');
        if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
            $url = $this->url->link($this->request->get['c']);
        }

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/callback','','');
        if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
            $redir_url = $this->url->link('auth/login/callback', 'c='.$this->request->get['c'], '');
        }

        $me = $this->request->get['me'];
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null); 

        $result = $this->confirm_auth($me, $code, $redir_url, $state);

        if($result){
            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];
            $this->session->data['success'] = "You are now logged in as ".$this->request->get['me'];
        }

        $this->response->redirect($url);
    }


	public function tokencallback() {
        // first figure out where we are going after we process
        $url = $this->url->link('');
        if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
            $url = $this->url->link($this->request->get['c']);
        }

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/tokencallback', '', '');
        if(isset($this->request->get['c']) && !empty($this->request->get['c'])){
            $redir_url = $this->url->link('auth/login/tokencallback', 'c='.$this->request->get['c'], '');
        }

        $me = $this->request->get['me'];
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null); 

        $result = $this->confirm_auth($me, $code, $redir_url, $state);

        if($result){
            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];

            //TODO token stuff
            $token_results = $this->get_token($me, $code, $redir_url, $state);

            $this->session->data['token'] = $token_results['access_token'];

            $this->session->data['success'] = "You are now logged in as ".$this->request->get['me'];
        }

        $this->response->redirect($url);
    }

    private function confirm_auth( $me, $code, $redir, $state = null ) {
        
        $client_id = $this->url->link('');

        //look up user's auth provider
        $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);

        $post_array = array(
            'code'          => $code,
            'redirect_uri'  => $redir,
            'client_id'     => $client_id
        );
        if($state){
            $post_array['state'] = $state;
        }

        $post_data = http_build_query($post_array);

        $ch = curl_init($auth_endpoint);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);

        $results = array();
        parse_str($response, $results);
        
        if($state){
            return ($results['me'] == $me && $state == substr(md5($me.$client_id),0,8));
        } else {
            return $results['me'] == $me ;
        }

	}


    private function get_token( $me, $code, $redir, $state = null ) {
        
        $client_id = $this->url->link('');

        //look up user's token provider
        $token_endpoint = IndieAuth\Client::discoverTokenEndpoint($me);


        $post_array = array(
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redir,
            'client_id'     => $client_id,
            'me'            => $me
        );
        if($state){
            $post_array['state'] = $state;
        }

        $post_data = http_build_query($post_array);

        $ch = curl_init($token_endpoint);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);

        $results = array();
        parse_str($response, $results);

        //$this->log->write(print_r($results, true));
        
        return $results;
    }
}
?>
