<?php  
class ControllerCommonHome extends Controller {
	public function index() {
        $short_title = SITE_TITLE;
        $description = "OpenBlog site: ".SITE_TITLE;

		$this->document->setTitle($short_title);
		$this->document->setDescription($description);

        $this->document->addMeta('twitter:card', 'summary');
        $this->document->addMeta('twitter:title', $short_title);
        $this->document->addMeta('twitter:description', $description);
        $this->document->addMeta('twitter:image', '/image/static/icon_200.jpg');

        $this->document->addMeta('og:type', 'website');
        $this->document->addMeta('og:title', $short_title);
        $this->document->addMeta('og:description', $description);
        $this->document->addMeta('og:image', '/image/static/icon_200.jpg');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/post');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');

		
		$data['posts'] = array();

        $skip=0;
        if(isset($this->request->get['skip'])){
            $skip = $this->request->get['skip'];
        }

		foreach ($this->model_blog_post->getPostsByTypes(['article'], 20, $skip) as $result) {
			$author = $this->model_blog_author->getAuthor($result['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_post->getLikeCountForPost($result['post_id']);

            $extra_data_array = array(
			    'body_html' => html_entity_decode($result['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
			    'categories' => $categories,
			    'comment_count' => $comment_count,
                'like_count' => $like_count);

            if($this->session->data['is_owner']){
                $extra_data_array['editlink'] = $this->url->link('micropub/client/editPost', 'id='.$result['post_id'],'');
            }

            $data['posts'][] = array_merge($result, $extra_data_array);
		}

		$data['side_posts'] = array();

		foreach ($this->model_blog_post->getPostsByTypes(['photo','note'], 20, $skip) as $result) {
			$author = $this->model_blog_author->getAuthor($result['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($result['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_post->getLikeCountForPost($result['post_id']);

			$extra_data_array = array(
			    'body_html' => html_entity_decode(isset($result['excerpt']) ? $result['excerpt']. '...' : $result['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
			    'categories' => $categories,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count);

            if($this->session->data['is_owner']){
                $extra_data_array['editlink'] = $this->url->link('micropub/client/'.$result['post_type'], 'op=edit&id='.$result['post_id'],'');
            }

            $data['side_posts'][] = array_merge($result, $extra_data_array);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
		}
	}
}
?>
