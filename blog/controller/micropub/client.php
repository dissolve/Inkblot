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

        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
        }

		$this->document->setDescription($this->config->get('config_meta_description'));

		if(isset($this->session->data['user_site'])){
		    $data['user_name'] = $this->session->data['user_site'];
		    $endpoint = IndieAuth\Client::discoverMicropubEndpoint($data['user_name']);
		    if($endpoint){
                $data['micropubEndpoint'] = $endpoint;
                $data['action'] = $this->url->link('micropub/client/send', '', '');
		    }
		}

		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		if(isset($this->request->get['url']) && !empty($this->request->get['url'])){
		    $data['post'] = $this->download_entry($this->request->get['url'], isset($this->request->get['type']) && $this->request->get['type']);
		}

		if(isset($this->request->get['type'])){
		   $data['type'] = strtolower($this->request->get['type']);
		}
		if(isset($this->request->get['reply_to'])){
		   $data['post'] = array('replyto' => $this->request->get['reply_to']);
		}
		if(isset($this->request->get['bookmark'])){
		   $data['post'] = array('bookmark' => $this->request->get['bookmark']);
		}
		if(isset($this->request->get['like'])){
		   $data['post'] = array('like' => $this->request->get['like']);
		}

		$data['token'] = isset($this->session->data['token']);

		$data['new_entry_link'] = $this->url->link('micropub/client');
		$data['edit_entry_link'] = $this->url->link('micropub/client/editPost');
		$data['delete_entry_link'] = $this->url->link('micropub/client/deletePost');
		$data['undelete_entry_link'] = $this->url->link('micropub/client/undeletePost');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/new.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/new.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/new.tpl', $data));
		}
	}

	public function note() {

		$this->document->setTitle('Post A Note');
		$this->document->setIcon('/image/static/note.png');
        $this->document->addMeta("mobile-web-app-capable","yes");
		$data['title'] = 'Post A Note';

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['login'] = $this->url->link('auth/login');

        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
        }

		$this->document->setDescription($this->config->get('config_meta_description'));

		if(isset($this->session->data['user_site'])){
		    $data['user_name'] = $this->session->data['user_site'];
		    $endpoint = IndieAuth\Client::discoverMicropubEndpoint($data['user_name']);
		    if($endpoint){
                $data['micropubEndpoint'] = $endpoint;
                $data['action'] = $this->url->link('micropub/client/send', '', '');
		    }
		}

		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		$data['token'] = isset($this->session->data['token']);


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/note.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/note.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/note.tpl', $data));
		}
	}

	public function checkin() {

		$this->document->setTitle('Check-in');
		$data['title'] = 'Check-in';
		$this->document->setIcon('/image/static/checkin.png');
        $this->document->addMeta("mobile-web-app-capable","yes");

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['login'] = $this->url->link('auth/login');

        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
        }

		$this->document->setDescription($this->config->get('config_meta_description'));

		if(isset($this->session->data['user_site'])){
		    $data['user_name'] = $this->session->data['user_site'];
		    $endpoint = IndieAuth\Client::discoverMicropubEndpoint($data['user_name']);
		    if($endpoint){
                $data['micropubEndpoint'] = $endpoint;
                $data['action'] = $this->url->link('micropub/client/send', '', '');
		    }
		}

		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		$data['token'] = isset($this->session->data['token']);

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/checkin.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/checkin.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/checkin.tpl', $data));
		}
	}

	public function editPost() {

		$this->document->setTitle('Edit a Post');
		$data['title'] = 'Edit a Post';

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

		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		if(isset($this->request->get['url']) && !empty($this->request->get['url'])){
		    $data['post'] = $this->download_entry($this->request->get['url']);
		}

		if(isset($this->request->get['op'])){
		   $data['op'] = $this->request->get['op'];
		}

		$data['token'] = isset($this->session->data['token']);

		$data['new_entry_link'] = $this->url->link('micropub/client');
		$data['edit_entry_link'] = $this->url->link('micropub/client/editPost');
		$data['delete_entry_link'] = $this->url->link('micropub/client/deletePost');
		$data['undelete_entry_link'] = $this->url->link('micropub/client/undeletePost');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/edit.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/edit.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/edit.tpl', $data));
		}
	}

	public function deletePost() {
		$this->document->setTitle('Delete a Post');
		$data['title'] = 'Delete a Post';

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
		
		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		if(isset($this->request->get['url']) && !empty($this->request->get['url'])){
		    //$data['post'] = $this->download_entry($this->request->get['url'], true);
		    $data['post'] = array('permalink'=>$this->request->get['url']);
		}


		$data['token'] = isset($this->session->data['token']);

		$data['new_entry_link'] = $this->url->link('micropub/client');
		$data['edit_entry_link'] = $this->url->link('micropub/client/editPost');
		$data['delete_entry_link'] = $this->url->link('micropub/client/deletePost');
		$data['undelete_entry_link'] = $this->url->link('micropub/client/undeletePost');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/delete.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/delete.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/delete.tpl', $data));
		}
	}
	public function unDeletePost() {
		$this->document->setTitle('Undelete a Post');
		$data['title'] = 'Undelete a Post';

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
		
		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		if(isset($this->request->get['url']) && !empty($this->request->get['url'])){
		    //$data['post'] = $this->download_entry($this->request->get['url'], true);
		    $data['post'] = array('permalink'=>$this->request->get['url']);
		}


		$data['token'] = isset($this->session->data['token']);

		$data['new_entry_link'] = $this->url->link('micropub/client');
		$data['edit_entry_link'] = $this->url->link('micropub/client/editPost');
		$data['delete_entry_link'] = $this->url->link('micropub/client/deletePost');
		$data['undelete_entry_link'] = $this->url->link('micropub/client/undeletePost');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/undelete.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/undelete.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/undelete.tpl', $data));
		}
	}

    public function send() {

        $post_data_array = $this->request->post;
        $post_data_array['h'] = 'entry';

        if(isset($post_data_array['type']) && $post_data_array['type'] == 'article'){
            $post_data_array['content']  = html_entity_decode($post_data_array['content']);
        }

       $syn_to_hack = ''; 
        if(isset($post_data_array['syndicate-to'])){
            $syn_to_hack = 'syndicate-to='.urlencode(implode(',', $post_data_array['syndicate-to'])) . '&';
        }

        //$this->log->write(print_r($post_data_array, true));
        $post_data = $syn_to_hack . http_build_query($post_data_array);

        $user = $this->session->data['user_site'];
        $micropub_endpoint = IndieAuth\Client::discoverMicropubEndpoint($user);

        $ch = curl_init($micropub_endpoint);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->session->data['token']));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        /////////////////////////////////////////////////
        //TODO: once my hosting provider fixes its issue i can remove this
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /////////////////////////////////////////////////

        $response = curl_exec($ch);
        $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (in_array($result, array(200,201,204,301,302))){
            if(in_array($result, array(201,301,302))){
                $target_url =  curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $this->session->data['success_url'] = $target_url;
            }
            $this->session->data['success'] = 'Post Submitted.';
        } else {
            $this->session->data['error'] = 'Error:  Return code '.$result.'.';
        }
        if(isset($this->request->post['type']) && $this->request->post['type'] == 'article'){
            $this->response->redirect($this->url->link('micropub/client/article'));
        } else {
            $this->response->redirect($this->url->link('micropub/client'));
        }
    }

    private function download_entry($entry_url, $as_html=false){
        $ch = curl_init($entry_url);

        if(!$ch){$this->log->write('error with curl_init');}

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $real_source_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $page_content = curl_exec($ch);
        $mf = Mf2\parse($page_content, $real_source_url)['items'][0];
        //$this->log->write($response);
        $post = array();
        if(array_key_exists('type', $mf) && in_array('h-entry', $mf['type']) && array_key_exists('properties', $mf)) {
            $properties = $mf['properties'];
            $this->log->write(print_r($properties,true));

            if(array_key_exists('content', $properties)) {
                if($as_html){
                    $post['body'] = $properties['content'][0]['html'];
                } else {
                    $post['body'] = strip_tags($properties['content'][0]['html']);
                }
            }

            if(array_key_exists('name', $properties)) {
                $post['title'] = $properties['name'][0];
            }

            if(array_key_exists('in-reply-to', $properties)) {
                $post['replyto'] = $properties['in-reply-to'][0]['value'];
            }
            $post['permalink'] = $real_source_url;
        }

        //$post['body'] = print_r($mf2_parsed['items'][0],true);
        return $post;
    }
}
?>
