<?php  
class ControllerCommonFooter extends Controller {
	public function index() {
		
		$this->load->model('blog/melink');
		
		$data['melinks'] = array();

		foreach ($this->model_blog_melink->getLinks() as $result) {
				$data['melinks'][] = array(
					'url' => $result['url'],
					'image' => $result['image'],
					'value' => $result['value'],
					'title' => $result['title'],
					'target' => $result['target']);
    	}

		//$data['contact'] = $this->url->link('information/contact');
		//$data['return'] = $this->url->link('account/return/insert', '', 'SSL');
    	//$data['sitemap'] = $this->url->link('information/sitemap');
		
		$this->load->model('blog/post');
		$data['recent_posts'] = array();

		foreach ($this->model_blog_post->getRecentPosts(10) as $result) {
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
