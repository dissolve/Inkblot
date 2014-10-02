<?php  
class ControllerContactsMe extends Controller {
	public function index() {
        $headers = apache_request_headers();
        if(isset($this->request->post['access_token']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($headers['Authorization'])){
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
            $auth_info = $this->model_auth_token->getAuthFromToken($token);
            if(!empty($auth_info)) {
                $this->session->data['user_site'] = $auth_info['user'];

            }

        }

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['site_title'] = SITE_TITLE;
		$data['site_subtitle'] = SITE_SUBTITLE;

		$webmention_handler = $this->url->link('webmention/receive');
        $auth_endpoint = AUTH_ENDPOINT;
        $token_endpoint = $this->url->link('auth/token');
        $micropub_endpoint = $this->url->link('micropub/receive');

        $this->response->addHeader('Link: <'. $webmention_handler.'>; rel="webmention"', false);
        $this->response->addHeader('Link: <'. $auth_endpoint.'>; rel="authorization_endpoint"', false);
        $this->response->addHeader('Link: <'. $token_endpoint.'>; rel="token_endpoint"', false);
        $this->response->addHeader('Link: <'. $micropub_endpoint.'>; rel="micropub"', false);

		$data['webmention_handler'] = $webmention_handler;
		$data['authorization_endpoint'] = $auth_endpoint;
		$data['token_endpoint'] = $token_endpoint;
		$data['micropub_endpoint'] = $micropub_endpoint;

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();	 
		$data['metas'] = $this->document->getMetas();	 
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$data['name'] = $this->config->get('config_name');
		
		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$data['icon'] = '';
		}
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
						
				

		$status = true;
		
		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}
		
		
		$this->load->model('blog/mycard');
		
        $data['auth_page'] = $this->url->link('auth/login','c=contacts/me','');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');

        $data['google_analytics_id'] = GOOGLE_ANALYTICS_ID;

		$data['mydata_contact'] = array();

		foreach ($this->model_blog_mycard->getData($this->session->data['user_site'], 'contact') as $result) {
				$data['mydata_contact'][] = array(
					'url'    => str_replace('{}', $result['value'], $result['link_format']),
					'image'  => $result['image'],
					'value'  => str_replace('{}', $result['value'], $result['mobile_label']),
					'title'  => str_replace('{}', $result['value'], $result['title']),
					'rel'    => $result['rel'],
					'image'  => $result['field_display_image'],
					'target' => $result['target']);
    	}

		$data['mydata_elsewhere'] = array();

		foreach ($this->model_blog_mycard->getData($this->session->data['user_site'], 'elsewhere') as $result) {
				$data['mydata_elsewhere'][] = array(
					'url'    => str_replace('{}', $result['value'], $result['link_format']),
					'image'  => $result['image'],
					'value'  => str_replace('{}', $result['value'], $result['mobile_label']),
					'title'  => str_replace('{}', $result['value'], $result['title']),
					'rel'    => $result['rel'],
					'image'  => $result['field_display_image'],
					'target' => $result['target']);
    	}

		
		$this->load->model('blog/post');
		$data['recent_posts'] = array();

		foreach ($this->model_blog_post->getRecentPosts(10) as $result) {
            if(empty($result['title'])){
                if($result['post_type'] == 'photo'){
                    $result['title']='photo';
                } else {
                    $result['title'] = substr(strip_tags(html_entity_decode($result['body'])), 0, 30). '...';
                }
            }
            
            $data['recent_posts'][] = $result;
    	}

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->load->model('blog/author');

        $data['author'] = $this->model_blog_author->getAuthor(1);

		$this->load->model('blog/post');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		
		$data['posts'] = array();

		foreach ($this->model_blog_post->getRecentPosts() as $result) {
			$categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_post->getLikeCountForPost($result['post_id']);
			$data['posts'][] = array_merge($result, array(
			    'body_html' => html_entity_decode($result['body'])));
		}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/contacts/me.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/contacts/me.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/contacts/me.tpl', $data));
		}
	}
}
?>
