<?php  
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/post');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/like');
		
		$data['posts'] = array();

        $skip=0;
        if(isset($this->request->get['skip'])){
            $skip = $this->request->get['skip'];
        }

		foreach ($this->model_blog_post->getPostsByTypes(['article'], 20, $skip) as $result) {
			$author = $this->model_blog_author->getAuthor($result['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_like->getLikeCountForPost($result['post_id']);
			$data['posts'][] = array_merge($result, array(
			    'body_html' => html_entity_decode($result['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
			    'categories' => $categories,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count
			    ));
		}

		$data['side_posts'] = array();

		foreach ($this->model_blog_post->getPostsByTypes(['photo','note'], 20, $skip) as $result) {
			$author = $this->model_blog_author->getAuthor($result['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_like->getLikeCountForPost($result['post_id']);
			$data['side_posts'][] = array_merge($result, array(
			    'body_html' => html_entity_decode(isset($result['excerpt']) ? $result['excerpt']. '...' : $result['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
			    'categories' => $categories,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count
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
