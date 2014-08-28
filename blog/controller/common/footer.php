<?php  
class ControllerCommonFooter extends Controller {
	public function index() {
		
		$this->load->model('blog/melink');
		$this->load->model('blog/mycard');
		
        $data['client_id'] = $this->url->link('');
        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');

        $data['google_analytics_id'] = GOOGLE_ANALYTICS_ID;

		$data['melinks'] = array();

		foreach ($this->model_blog_mycard->getData($this->session->data['user_site']) as $result) {
				$data['melinks'][] = array(
					'url'    => str_replace('{}', $result['value'], $result['link_format']),
					'image'  => $result['image'],
					'value'  => str_replace('{}', $result['value'], $result['field_label']),
					'title'  => str_replace('{}', $result['value'], $result['title']),
					'rel'    => $result['rel'],
					'target' => $result['target']);
    	}
        /*
		foreach ($this->model_blog_melink->getLinks() as $result) {
				$data['melinks'][] = array(
					'url' => $result['url'],
					'image' => $result['image'],
					'value' => $result['value'],
					'title' => $result['title'],
					'target' => $result['target']);
    	}
         */

		//$data['contact'] = $this->url->link('information/contact');
		//$data['return'] = $this->url->link('account/return/insert', '', 'SSL');
    	//$data['sitemap'] = $this->url->link('information/sitemap');
		
		$this->load->model('blog/mention');
		$data['recent_mentions'] = array();
		foreach ($this->model_blog_mention->getRecentMentions(10) as $result) {
				$data['recent_mentions'][] = $result;
    	}

		$this->load->model('blog/like');
		$data['likes'] = array();
		$data['like_count'] = $this->model_blog_like->getGenericLikeCount();
		foreach ($this->model_blog_like->getGenericLikes() as $result) {
				$data['likes'][] = $result;
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
}
?>
