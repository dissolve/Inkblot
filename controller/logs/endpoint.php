<?php  
class ControllerLogsEndpoint extends Controller {
	public function index() {
            $headers = apache_request_headers();
            if(isset($this->request->post['access_token']) || (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) || isset($headers['Authorization']) || $this->session->data['is_owner']){
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
                $auth_info = $this->model_auth_token->getAuthFromToken(urldecode($token));


                if((!empty($auth_info) && in_array('logs', explode(' ', $auth_info['scope']))) || $this->session->data['is_owner']) {

                    $token_user = str_replace(array('http://', 'https://'),array('',''), $auth_info['user']);
                    $myself = str_replace(array('http://', 'https://'),array('',''), HTTP_SERVER);

                    if(($token_user == $myself || $token_user.'/' == $myself || $token_user == $myself .'/' ) || $this->session->data['is_owner']) { 

                        if(isset($this->request->get['h']) && strtolower($this->request->get['h']) == 'feed'){
                            $this->getFeed();
                        } elseif(isset($this->request->post['h']) && strtolower($this->request->post['h']) == 'entry'){
                            $this->saveLogEntry();
                        } else {
                            $this->feedList();
                        }
                        
                    } else {
                        header('HTTP/1.1 401 Unauthorized');
                        echo 'Unauthorized';
                        exit();
                    }
                } else {
                    header('HTTP/1.1 401 Unauthorized');
                    echo 'Unauthorized';
                    exit();
                }
            } else {
                header('HTTP/1.1 401 Unauthorized');
                echo 'Unauthorized';
                exit();
            }
    }

    function getFeed(){
            $this->load->model('storage/logs');
            $data['feed'] = $this->model_storage_logs->getFeed($this->request->get['url']);

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/logs/feed.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/logs/feed.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/logs/feed.tpl', $data));
            }
    }
    function saveLogEntry(){
            $this->load->model('storage/logs');
            $this->model_storage_logs->addLogEntry();
    }
    function feedList(){
            $this->load->model('storage/logs');
            $data['feedlist'] = $this->model_storage_logs->getFeedList();
            $data['logger_endpoint'] =  $this->url->link('logs/endpoint');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/logs/feedlist.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/logs/feedlist.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/logs/feedlist.tpl', $data));
            }
    }
}

?>
