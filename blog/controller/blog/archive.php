<?php  
class ControllerBlogArchive extends Controller {
	public function index() {

        $month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $month = $this->request->get['month'];
        $year = $this->request->get['year'];

		$this->document->setTitle('Articles for  '.$month_names[$month] .', '.$year);
		$data['title'] = 'Articles for '.$month_names[$month] .', '.$year;

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/author');
		$this->load->model('blog/article');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/like');


		$data['articles'] = array();

		foreach ($this->model_blog_article->getArticlesByArchive($year, $month) as $article) {
                $categories = $this->model_blog_category->getCategoriesForPost($article['article_id']);
                $author = $this->model_blog_author->getAuthor($article['author_id']);
                $comment_count = $this->model_blog_comment->getCommentCountForPost($article['article_id']);
                $like_count = $this->model_blog_like->getLikeCountForPost($article['article_id']);
                $data['articles'][] = array_merge($article, array(
                    'body_html' => html_entity_decode($article['body']),
                    'author' => $author,
                    'categories' => $categories,
                    'comment_count' => $comment_count,
                    'like_count' => $like_count
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
