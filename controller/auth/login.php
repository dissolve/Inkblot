<?php

require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
require_once DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
require_once DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class ControllerAuthLogin extends Controller {

    public function index()
    {

        $me = $this->request->get['me'];

        $controller = (isset($this->request->get['c']) ? $this->request->get['c'] : null) ;
        if (!$controller) {
            $this->session->data['auth_redir'] = $_SERVER['HTTP_REFERER'];
            //$this->log->write('set session' . $_SERVER['HTTP_REFERER']);
        }

        $scope = (isset($this->request->get['scope']) ? $this->request->get['scope'] : null) ;

        // to simplify things lets figure out the URL to redirect to if we have any problems
        $fail_url = $this->url->link('');
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $fail_url = $_SERVER['HTTP_REFERER'];
        }

        // make sure they actually submitted something
        if (!empty($me)) {
            $me = $this->normalizeUrl($me);

            //look up user's auth provider
            $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);

            if (!$auth_endpoint) {
                $this->session->data['error'] = 'No Auth Endpoint Found';
                $this->response->redirect($fail_url);
            } else {
                $redir_url = $this->url->link('auth/login/callback', ($controller ? 'c=' . $controller : ''), '');
                if ($scope) {
                    // if a scope is given we are actually looking to get a token
                    $redir_url = $this->url->link('auth/login/tokencallback', ($controller ? 'c=' . $controller : ''), '');
                }

                //build our get request
                $trimmed_me = trim($me, '/'); //in case we get it back without the /
                $data_array = array(
                    'me' => $me,
                    'redirect_uri' => $redir_url,
                    'response_type' => 'id',
                    'state' => substr(md5($trimmed_me . $this->url->link('')), 0, 8),
                    'client_id' => $this->url->link('')
                );
                //$this->log->write(print_r($data_array,true));
                if ($scope) {
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

    public function callback()
    {

        // first figure out where we are going after we process
        $url = $this->url->link('');
        if (isset($this->request->get['c']) && !empty($this->request->get['c'])) {
            $url = $this->url->link($this->request->get['c']);
        } elseif (isset($this->session->data['auth_redir']) && !empty($this->session->data['auth_redir'])) {
            $url = $this->session->data['auth_redir'];
            unset($this->session->data['auth_redir']);
        }

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/callback', '', '');
        if (isset($this->request->get['c']) && !empty($this->request->get['c'])) {
            $redir_url = $this->url->link('auth/login/callback', 'c=' . $this->request->get['c'], '');
        }

        $me = $this->normalizeUrl($this->request->get['me']);
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null);

        //$this->log->write('callback received ...');
        //$this->log->write(print_r($this->request->get,true));

        $result = $this->confirmAuth($me, $code, $redir_url, $state);

        if ($result) {
            //lets try and see if they have an MP endpoint and if so, so they offer up the actions they have
            $mp_endpoint = IndieAuth\Client::discoverMicropubEndpoint($me);
            if ($mp_endpoint) {
                $ch = curl_init($mp_endpoint . '?q=actions');
                //$ch = curl_init($mp_endpoint.'?q=actions');

                if (!$ch) {
                    $this->log->write('error with curl_init');
                }

                //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

                $response = curl_exec($ch);
                $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($result == 200) {
                    $this->session->data['mp-config'] = $response;
                }
            }

            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];
            $this->session->data['success'] = "You are now logged in as " . $me;

            $token_user = str_replace(array('http://', 'https://'), array('',''), $me);
            $token_user = trim($token_user, '/');

            $myself = trim($this->normalizeUrl(HTTP_SERVER), '/');
            $myself = trim(str_replace(array('http://', 'https://'), array('',''), $myself), '/');

            if ($token_user == $myself) {
                $this->session->data['is_owner'] = true;
            }
        } else {
            $this->session->data['error'] = 'Authorization Failed.';
        }

        $this->response->redirect($url);
    }


    public function tokencallback()
    {
        // first figure out where we are going after we process
        $url = $this->url->link('');
        if (isset($this->request->get['c']) && !empty($this->request->get['c'])) {
            $url = $this->url->link($this->request->get['c']);
        } elseif (isset($this->session->data['auth_redir']) && !empty($this->session->data['auth_redir'])) {
            $url = $this->session->data['auth_redir'];
            unset($this->session->data['auth_redir']);
        }
        //$this->log->write('url:' . $url);
        //$this->log->write('session: ' .print_r($this->session->data,true));

        //recalculate the callback url
        $redir_url = $this->url->link('auth/login/tokencallback', '', '');
        if (isset($this->request->get['c']) && !empty($this->request->get['c'])) {
            $redir_url = $this->url->link('auth/login/tokencallback', 'c=' . $this->request->get['c'], '');
        }

        $me = $this->normalizeUrl($this->request->get['me']);
        $code = $this->request->get['code'];
        $state = (isset($this->request->get['state']) ? $this->request->get['state'] : null);

        $result = $this->confirmAuth($me, $code, $redir_url, $state);

        if ($result) {
            //lets try and see if they have an MP endpoint and if so, so they offer up the actions they have
            $mp_endpoint = IndieAuth\Client::discoverMicropubEndpoint($me);
            // we successfullly confirmed auth
            $this->session->data['user_site'] = $this->request->get['me'];
            $this->log->write($this->request->get['me'] . ' has logged in.');

            //TODO token stuff
            $token_results = $this->getToken($me, $code, $redir_url, $state);

            $this->session->data['token'] = $token_results['access_token'];
            $this->session->data['scope'] = $token_results['scope'];
            if ($mp_endpoint) {
                //$ch = curl_init($mp_endpoint.'?q=actions');
                $ch = curl_init($mp_endpoint . '?q=actions');

                if (!$ch) {
                    $this->log->write('error with curl_init');
                }

                //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                //if(isset($token_results['access_token'])){
                //curl_setopt($ch,
                //CURLOPT_HTTPHEADER,
                //array( 'Content-Type: application/json', 'Authorization: Bearer '. $token_results['access_token']));
                //}
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

                $response = curl_exec($ch);
                $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($result == 200) {
                    $this->session->data['mp-config'] = $response;
                }
            }

            $token_user = str_replace(array('http://', 'https://'), array('',''), $me);
            $token_user = trim($token_user, '/');

            $myself = trim($this->normalizeUrl(HTTP_SERVER), '/');
            $myself = trim(str_replace(array('http://', 'https://'), array('',''), $myself), '/');

            if ($token_user == $myself) {
                $this->session->data['is_owner'] = true;
            }

            $this->session->data['success'] = "You are now logged in as " . $this->request->get['me'];
        } else {
            $this->session->data['error'] = 'Authorization Step Failed.';
            $this->log->write('error authorizing');
            $this->log->write(print_r($this->request->get, true));
        }

        $this->response->redirect($url);
    }

    private function confirmAuth($me, $code, $redir, $state = null)
    {

        $client_id = $this->url->link('');

        //look up user's auth provider
        $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);

        $post_array = array(
            'code'          => $code,
            'redirect_uri'  => $redir,
            'client_id'     => $client_id
        );
        if ($state) {
            $post_array['state'] = $state;
        }

        $post_data = http_build_query($post_array);
        //$this->log->write('post_data: '.print_r($post_array,true));
        //$this->log->write('endpoint: '.$auth_endpoint);

        $ch = curl_init($auth_endpoint);

        if (!$ch) {
            $this->log->write('error with curl_init');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);

        $results = array();
        parse_str($response, $results);
        //$this->log->write('endpoint_response: '.$response);
        //$this->log->write(print_r($results, true));

        $results['me'] = $this->normalizeUrl($results['me']);

        $trimmed_me = trim($me, '/');
        $trimmed_result_me = trim($results['me'], '/');

        if ($state) {
            //$this->log->write('state = '.$state. ' ' .substr(md5($trimmed_me.$client_id),0,8));
            return ($trimmed_result_me == $trimmed_me && $state == substr(md5($trimmed_me . $client_id), 0, 8));
        } else {
            return $trimmed_result_me == $trimmed_me ;
        }

    }


    private function getToken($me, $code, $redir, $state = null)
    {

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
        if ($state) {
            $post_array['state'] = $state;
        }

        $post_data = http_build_query($post_array);

        $ch = curl_init($token_endpoint);

        if (!$ch) {
            $this->log->write('error with curl_init');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);

        $results = array();
        parse_str($response, $results);

        //$this->log->write(print_r($results, true));

        return $results;
    }


    private function normalizeUrl($url)
    {
            $url = trim($url);
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }
            return $url;
    }
}
