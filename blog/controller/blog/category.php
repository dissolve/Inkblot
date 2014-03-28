<?php  
class ControllerBlogCategory extends Controller {
	public function index() {
		$this->load->model('blog/category');
        $category = $this->model_blog_category->getCategoryByName($this->request->get['name']);
        $category_id = $category['id'];

		$this->document->setTitle('Posts Filed Under '.$category['name']);
		$data['title'] = 'Posts Filed Under '.$category['name'];

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->load->model('blog/post');
		$this->load->model('blog/author');


		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByCategory($category_id) as $result) {
                $author = $this->model_blog_author->getAuthor($result['author_id']);
                $categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
                $data['posts'][] = array_merge($result, array(
                    'author' => $author,
                    'categories' => $categories
                    ));
    	}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/category.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/category.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/category.tpl', $data));
		}
	}
}
?>
