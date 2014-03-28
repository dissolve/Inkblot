<?php  
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->load->model('blog/post');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		
		$data['posts'] = array();

		foreach ($this->model_blog_post->getRecentPosts(5) as $result) {
                $author = $this->model_blog_author->getAuthor($result['author_id']);
                $categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
                $data['posts'][] = array_merge($result, array(
                    'author' => $author,
                    'categories' => $categories
                    ));
    	}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
		}
	}
}
?>
