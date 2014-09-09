<?php  
include DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
include DIR_BASE . 'libraries/link-rel-parser-php/src/IndieWeb/link_rel_parser.php';
include DIR_BASE . 'libraries/indieauth-client-php/src/IndieAuth/Client.php';

class ControllerMicropubClient extends Controller {
	public function index() {

		$this->document->setTitle('Create a New Post');
		$data['title'] = 'Create a New Post';

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
        $data['login'] = $this->url->link('auth/login');

		$this->document->setDescription($this->config->get('config_meta_description'));

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
            $endpoint = IndieAuth\Client::discoverMicropubEndpoint($data['user_name']);
            if($endpoint){
                $data['micropubEndpoint'] = $endpoint;
                $data['action'] = $this->url->link('micropub/client/send', '', '');
            }

        }

        $data['token'] = isset($this->session->data['token']);



		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/create.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/create.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/archive.tpl', $data));
		}
	}

    public function send() {

        $post_data_array = array(
            'h'             => 'entry',
            'content'       => $this->request->post['content']
        );
        if(isset($this->request->post['in-reply-to'])){
            $post_data_array['in-reply-to']  = $this->request->post['in-reply-to'];
        }
        if(isset($this->request->post['slug'])){
            $post_data_array['slug']  = $this->request->post['slug'];
        }

        $post_data = http_build_query($post_data_array);

        $user = $this->session->data['user_site'];
        $micropub_endpoint = IndieAuth\Client::discoverMicropubEndpoint($user);

        $ch = curl_init($micropub_endpoint);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->session->data['token']));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        /////////////////////////////////////////////////
        //TODO: once my hosting provider fixes its issue i can remove this
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /////////////////////////////////////////////////

        $response = curl_exec($ch);
        print_r($response);
    }
}
?>
