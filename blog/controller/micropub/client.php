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

		if($this->session->data['is_owner'] && isset($this->request->get['id']) && !empty($this->request->get['id'])){
		    $this->load->model('blog/post');
		    $data['post'] = $this->model_blog_post->getPost($this->request->get['id']);
		}

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/note.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/note.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/note.tpl', $data));
		}
	}

	public function article() {
		$this->document->setTitle('Create a New Article');
		$data['title'] = 'Create a New Article';

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

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/article.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/article.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/article.tpl', $data));
		}
	}

	public function checkin() {
		$this->document->setTitle('Create a New Check-in');
		$data['title'] = 'Create a New Check-in';

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

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/checkin.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/checkin.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/checkin.tpl', $data));
		}
	}

	public function rsvp() {
		$this->document->setTitle('New RSVP');
		$data['title'] = 'New RSVP';

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

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/rsvp.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/rsvp.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/rsvp.tpl', $data));
		}
	}

	public function like() {
		$this->document->setTitle('New Like');
		$data['title'] = 'New Like';

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

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/like.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/like.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/like.tpl', $data));
		}
	}

	public function bookmark() {
		$this->document->setTitle('New Bookmark');
		$data['title'] = 'New Bookmark';

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

		$data['token'] = isset($this->session->data['token']);

		$data['article_create_link'] = $this->url->link('micropub/client/article');
		$data['rsvp_create_link'] = $this->url->link('micropub/client/rsvp');
		$data['like_create_link'] = $this->url->link('micropub/client/like');
		$data['bookmark_create_link'] = $this->url->link('micropub/client/bookmark');
		$data['checkin_create_link'] = $this->url->link('micropub/client/checkin');
		$data['note_create_link'] = $this->url->link('micropub/client');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/micropub/bookmark.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/micropub/bookmark.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/micropub/bookmark.tpl', $data));
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
        if (in_array($result, array(200,201))){
            $this->session->data['success'] = 'Post Submitted.';
        } else {
            $this->session->data['error'] = 'Error in Creation.  Return code '.$result.'.';
        }
        if(isset($this->request->post['type']) && $this->request->post['type'] == 'article'){
            $this->response->redirect($this->url->link('micropub/client/article'));
        } else {
            $this->response->redirect($this->url->link('micropub/client'));
        }
    }
}
?>
