<?php  
class ControllerCommonFooter extends Controller {
	public function index() {
		
		$this->load->model('blog/mycard');
		
        $data['client_id'] = $this->url->link('');
        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');
        $data['login'] = $this->url->link('auth/login');

        $data['google_analytics_id'] = GOOGLE_ANALYTICS_ID;
        $data['sitesearch'] = trim(str_replace(array('http://','https://'),array('',''), HTTP_SERVER), '/');

        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
            $data['newlink'] = $this->url->link('micropub/client', '', '');
            $data['webaction'] = $this->url->link('micropub/receive', 'q=indie-config&handler=%s', '');
        }

		$data['mylinks'] = array();

		foreach ($this->model_blog_mycard->getData($this->session->data['user_site']) as $result) {
				$data['mylinks'][] = array(
					'url'    => str_replace('{}', $result['value'], $result['link_format']),
					'image'  => $result['image'],
					'value'  => str_replace('{}', $result['value'], $result['field_label']),
					'title'  => str_replace('{}', $result['value'], $result['title']),
					'rel'    => $result['rel'],
					'target' => $result['target']);
    	}

        if($this->session->data['is_owner']){
            $this->load->model('blog/post');
            $data['recent_drafts'] = $this->model_blog_post->getRecentDrafts(10);
            $this->load->model('blog/interaction');
            $data['recent_interactions'] = $this->model_blog_interaction->getRecentInteractions(10);
        }
		
		$this->load->model('blog/interaction');
		$data['recent_mentions'] = $this->model_blog_interaction->getGenericInteractions('person-mention',10);
		$data['recent_tags'] = $this->model_blog_interaction->getGenericInteractions('tagged',10);
		$data['likes'] = $this->model_blog_interaction->getGenericInteractions('like');
		$data['like_count'] = $this->model_blog_interaction->getGenericInteractionCount('like');
		
		$this->load->model('blog/post');
		$data['recent_posts'] = array();
		foreach ($this->model_blog_post->getRecentPosts(10) as $result) {
            if(empty($result['title'])){
                if($result['post_type'] == 'photo'){
                    $result['title']='Photo';
                } elseif($result['post_type'] == 'checkin'){
                    if(isset($result['place_name'])){
                        $result['title'] = substr('Checkin At '. $result['place_name'], 0, 30). '...';
                    } else {
                        $result['title']='Checkin';
                    }
                } else {
                    $result['title'] = substr(strip_tags(html_entity_decode($result['body'])), 0, 30). '...';
                }
            }
            
            $data['recent_posts'][] = $result;
    	}

		$this->load->model('blog/archive');
		$data['archives'] = array();

		foreach ($this->model_blog_archive->getArchives() as $result) {
				$data['archives'][] = $result;
    	}

		$this->load->model('blog/category');
		$data['categories'] = array();

		foreach ($this->model_blog_category->getCategories() as $result) {
				$data['categories'][] = $result;
    	}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/footer.tpl', $data);
		} else {
			return $this->load->view('default/template/common/footer.tpl', $data);
		}
	}
	public function clean() {
		
		$this->load->model('blog/mycard');
		
        $data['client_id'] = $this->url->link('');
        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');
        $data['login'] = $this->url->link('auth/login');

        $data['google_analytics_id'] = GOOGLE_ANALYTICS_ID;
        $data['sitesearch'] = trim(str_replace(array('http://','https://'),array('',''), HTTP_SERVER), '/');

        if($this->session->data['is_owner']){
            $data['newlink'] = $this->url->link('micropub/client', '', '');
        }
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer_clean.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/footer_clean.tpl', $data);
		} else {
			return $this->load->view('default/template/common/footer_clean.tpl', $data);
		}
	}

	public function contacts() {
		
		$this->load->model('blog/mycard');
		
        $data['client_id'] = $this->url->link('');
        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');
        $data['login'] = $this->url->link('auth/login');

        $data['google_analytics_id'] = GOOGLE_ANALYTICS_ID;
        $data['sitesearch'] = trim(str_replace(array('http://','https://'),array('',''), HTTP_SERVER), '/');

        if($this->session->data['is_owner']){
            $data['newlink'] = $this->url->link('micropub/client', '', '');
        }
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer_contacts.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/footer_contacts.tpl', $data);
		} else {
			return $this->load->view('default/template/common/footer_contacts.tpl', $data);
		}
	}
}
?>
