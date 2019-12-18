<?php

class ControllerMicropubMediaendpoint extends Controller {
    public function index()
    {
        $headers = apache_request_headers();
        //check that we were even offered an access token
        if (!isset($this->request->post['access_token'])
            && (!isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            && (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION']))
            && !isset($headers['Authorization'])) {
            //$this->log->write('err0');
            $this->response->addHeader( 'HTTP/1.1 400 Bad Request');
            $response_array = array();
            $response_array['error'] = 'invalid_request';
            $response_array['error_description'] = 'No Auth Token provided';
            $this->response->setOutput(json_encode($response_array));
        } else {
            $token = $this->request->post['access_token'];
            if (!$token) {
                $parts = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
                $token = $parts[1];
            }
            if (!$token) {
                $parts = explode(' ', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
                $token = $parts[1];
            }
            if (!$token) {
                $parts = explode(' ', $headers['Authorization']);
                $token = $parts[1];
            }


            $this->load->model('auth/token');
            $auth_info = $this->model_auth_token->getAuthFromToken(urldecode($token));

            $token_user = str_replace(array('http://', 'https://'), array('',''), $auth_info['user']);
            $myself = str_replace(array('http://', 'https://'), array('',''), HTTP_SERVER);


            if ($token_user != $myself && $token_user . '/' != $myself && $token_user != $myself . '/' ) {
                //$this->log->write('err1');
                header('HTTP/1.1 401 Unauthorized');
                exit();
            } else {

                $this->load->model('auth/token');
                $auth_info = $this->model_auth_token->getAuthFromToken(urldecode($token));

                if (empty($auth_info) || !in_array('post', explode(' ', $auth_info['scope']))) {
                    //token does not have post access
                    
                } else {

                    //$this->log->write('debug');
                    $file_url = $this->uploadFile($auth_info['client_id']);
                    //$this->log->write($file_url);
                    header('HTTP/1.1 201 Created');
                    header('Location: ' . $file_url);
                    exit();
                    //$this->response->setOutput('');
                }


            }  // end check for token is my own

        }  // end check for access token offered


    } //end index funciton

    private function uploadFile($client_id)
    {


        if (isset($_FILES['file'])) {
            $upload_shot = $_FILES['file'];
            if ( $upload_shot['error'] != 0) {
                header('HTTP/1.1 500 Error');
                exit();
            }
        }

        $file_count = (int)file_get_contents(DIR_UPLOAD . "/file_count.txt", "r");

        $new_count = $file_count +1;


        //TODO: check that the file doesn't exist first
        move_uploaded_file($upload_shot["tmp_name"], DIR_UPLOAD . '/files/' . $new_count . '_' . urldecode($upload_shot["name"]));

        file_put_contents(DIR_UPLOAD . "/file_count.txt", $new_count);

        $file_rel_url = DIR_UPLOAD_REL . '/files/' .$new_count . '_' . $upload_shot["name"];

        return HTTPS_SERVER . $file_rel_url;

    }

}
