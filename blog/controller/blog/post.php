<?php  
class ControllerBlogPost extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

		$this->load->model('blog/post');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		
		$post = $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);
        $author = $this->model_blog_author->getAuthor($post['author_id']);
        $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
        $data['post'] = array_merge($post, array(
            'author' => $author,
            'categories' => $categories
            ));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/post.tpl', $data));
		}
	}

	public function comment() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

        $post_id = $this->request->get['pid'];
		$this->load->model('blog/post');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		
		$post = $this->model_blog_post->getPost($post_id);
        $author = $this->model_blog_author->getAuthor($post['author_id']);
        $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
        $data['post'] = array_merge($post, array(
            'author' => $author,
            'categories' => $categories
            ));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/comment.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/comment.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/comment.tpl', $data));
		}
	}
}
?>
