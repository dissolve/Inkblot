<?php
include DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
include DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
include DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';
class User {
	private $user_site = null;
	private $contact_id = null;
	private $is_owner = null;
	private $toke = null;
    

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['user_site'])) {
			$this->user_site = $this->session->data['user_site'];
            if (isset($this->session->data['is_owner'])) {
                $this->is_owner = $this->session->data['is_owner'];
            }
            if (isset($this->session->data['contact_id'])) {
                $this->contact_id = $this->session->data['contact_id'];
            }
            if (isset($this->session->data['token'])) {
                $this->token = $this->session->data['token'];
            }
		}
	}

	public function login($redirect_on_complete) {
        $me = $this->request->get['me'];

        $this->session->data['auth_redir'] = $redir_on_complete;

        $scope = (isset($this->request->get['scope']) ? $this->request->get['scope'] : null) ; 
        
        // to simplify things lets figure out the URL to redirect to if we have any problems
        $fail_url = $this->url->link('');
        if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            $fail_url = $_SERVER['HTTP_REFERER'];
        }

        // make sure they actually submitted something
        if(!empty($me)){
        
            $me = $this->normalize_url($me);
a
            //look up user's auth provider
            $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);

            if(!$auth_endpoint){
                $this->session->data['error'] = 'No Auth Endpoint Found';
                $this->response->redirect($fail_url);
            } else {

                $redir_url = $this->url->link('auth/login/callback', '', '');
                if($scope){
                    // if a scope is given we are actually looking to get a token
                    $redir_url = $this->url->link('auth/login/tokencallback', '', '');
                }

                //build our get request
                $trimmed_me = trim($me, '/'); //in case we get it back without the /
                $data_array = array(
                    'me' => $me,
                    'redirect_uri' => $redir_url,
                    'response_type' => 'id',
                    'state' => substr(md5($trimmed_me.$this->url->link('')),0,8),
                    'client_id' => $this->url->link('')
                );
                $this->log->write(print_r($data_array,true));
                if($scope){
                    $data_array['scope'] = $scope;
                    $data_array['response_type'] = 'code';
                }

                $get_data = http_build_query($data_array);

                //redirect to their provider
                $this->response->redirect($auth_endpoint . (strpos($auth_endpoint, '?') === false ? '?' : '&') . $get_data);
            }
            
        } else {
            $this->session->data['error'] = 'No Input';
            $this->response->redirect($fail_url);
        }
	}

    public function setToken($token){
        $this->session->data['token'] = $token;
        $this->token = $token;
    }

	public function logout() {
        unset($this->session->data['user_site']);
        unset($this->session->data['token']);
        unset($this->session->data['is_owner']);
        unset($this->session->data['contact_id']);

		$this->user_site  = null;
		$this->is_owner   = null;
		$this->token      = null;
		$this->contact_id = null;
	}

	public function isLogged() {
		return isset($this->user_site);
	}

	public function getSite() {
		return $this->user_site;
	}

	public function getToken() {
		return $this->token;
	}

	public function isOwner() {
		return $this->is_owner;
	}

	public function callback() {

        // first figure out where we are going after we process
        $url = $this->url->link('');
        if(isset($this->session->data['auth_redir']) && !empty($this->session->data['auth_redir'])){
            $url = $this->session->data['auth_redir'];
            unset($this->session->data['auth_redir']);
        }

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/callback','','');

        $me = $this->normalize_url($this->request->get['me']);
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null); 

        $this->log->write('callback received ...');
        $this->log->write(print_r($this->request->get,true));

        $result = $this->confirm_auth($me, $code, $redir_url, $state);

        if($result){
            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];
            $this->user_site = $this->request->get['me'];
            $this->session->data['success'] = "You are now logged in as ".$me;

            $token_user = str_replace(array('http://', 'https://'),array('',''), $me);

            $myself = trim($this->normalize_url(HTTP_SERVER),'/');
            $myself = trim(str_replace(array('http://', 'https://'),array('',''), $myself), '/');

            if($token_user == $myself) {
                $this->session->data['is_owner'] = true;
                $this->is_owner = true;
            }
        } else {
            $this->session->data['error'] = 'Authorization Failed.';
        }

        $this->response->redirect($url);
    }


	public function tokencallback() {
        // first figure out where we are going after we process
        $url = $this->url->link('');
        if(isset($this->session->data['auth_redir']) && !empty($this->session->data['auth_redir'])){
            $url = $this->session->data['auth_redir'];
            unset($this->session->data['auth_redir']);
        }

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/tokencallback', '', '');

        $me = $this->normalize_url($this->request->get['me']);
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null); 

        $result = $this->confirm_auth($me, $code, $redir_url, $state);

        if($result){
            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];
            $this->log->write($this->request->get['me'] . ' has logged in.');

            //TODO token stuff
            $token_results = $this->get_token($me, $code, $redir_url, $state);

            $this->session->data['token'] = $token_results['access_token'];

            $token_user = str_replace(array('http://', 'https://'),array('',''), $me);

            $myself = trim($this->normalize_url(HTTP_SERVER),'/');
            $myself = trim(str_replace(array('http://', 'https://'),array('',''), $myself), '/');

            if($token_user == $myself) {
                $this->session->data['is_owner'] = true;
            }

            $this->session->data['success'] = "You are now logged in as ".$this->request->get['me'];
        } else {
            $this->session->data['error'] = 'Authorization Step Failed.';
            $this->log->write('error authorizing');
            $this->log->write(print_r($this->request->get,true));
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
        $this->log->write('post_data: '.print_r($post_array,true));
        $this->log->write('endpoint: '.$auth_endpoint);

        $ch = curl_init($auth_endpoint);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);

        $results = array();
        parse_str($response, $results);
        $this->log->write('endpoint_response: '.$response);
        $this->log->write(print_r($results, true));

        $results['me'] = $this->normalize_url($results['me']);

        $trimmed_me = trim($me, '/');
        $trimmed_result_me = trim($results['me'], '/');

        if($state){
            $this->log->write('state = '.$state. ' ' .substr(md5($trimmed_me.$client_id),0,8));
            return ($trimmed_result_me == $trimmed_me && $state == substr(md5($trimmed_me.$client_id),0,8));
        } else {
            return $trimmed_result_me == $trimmed_me ;
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


    private function normalize_url($url) {
            $url = trim($url);
            if(strpos($url, 'http') !== 0){
                $url = 'http://'.$url;
            }
            return $url;
    }
}
