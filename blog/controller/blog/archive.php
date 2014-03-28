<?php  
class ControllerBlogArchive extends Controller {
	public function index() {

        $month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $month = $this->request->get['month'];
        $year = $this->request->get['year'];

		$this->document->setTitle('Posts for  '.$month_names[$month] .', '.$year);
		$data['title'] = 'Posts for '.$month_names[$month] .', '.$year;

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->load->model('blog/author');
		$this->load->model('blog/post');
		$this->load->model('blog/category');


		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByArchive($year, $month) as $result) {
                $categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
                $author = $this->model_blog_author->getAuthor($result['author_id']);
                $data['posts'][] = array_merge($result, array(
                    'author' => $author,
                    'categories' => $categories
                    ));
    	}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/archive.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/archive.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/archive.tpl', $data));
		}
	}
}
?>
