<?php  
class ControllerBlogCategory extends Controller {
	public function index() {
		$this->load->model('blog/category');
        $category = $this->model_blog_category->getCategoryByName($this->request->get['name']);
        $category_id = $category['category_id'];

		$this->document->setTitle('Posts Filed Under '.$category['name']);
		$data['title'] = 'Posts Filed Under '.$category['name'];

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/author');
		$this->load->model('blog/post');
		$this->load->model('blog/comment');
		$this->load->model('blog/post');

		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByCategory($category_id) as $post) {
            $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
            $author = $this->model_blog_author->getAuthor($post['author_id']);
            $comment_count = $this->model_blog_comment->getCommentCountForPost($post['post_id']);
            $like_count = $this->model_blog_post->getLikeCountForPost($post['post_id']);

            $extra_data_array = array(
                'body_html' => html_entity_decode($post['body']),
                'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
                'categories' => $categories,
                'comment_count' => $comment_count,
                'like_count' => $like_count);

            if($this->session->data['is_owner']){
                $extra_data_array['repostlink'] = $this->url->link('micropub/client', 'id='.$result['post_id'],'');
                if($result['deleted'] == 1){
                    $extra_data_array['undeletelink'] = $this->url->link('micropub/client/undeletePost', 'id='.$result['post_id'],'');
                } else {
                    $extra_data_array['editlink'] = $this->url->link('micropub/client/editPost', 'id='.$result['post_id'],'');
                    $extra_data_array['deletelink'] = $this->url->link('micropub/client/deletePost', 'id='.$result['post_id'],'');
                }
            }

            $data['posts'][] = array_merge($post, $extra_data_array);
    	}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post_list.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post_list.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/post_list.tpl', $data));
		}
	}
}
?>
