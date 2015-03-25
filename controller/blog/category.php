<?php  
class ControllerBlogCategory extends Controller {
	public function index() {
        if($this->session->data['mp-config']){
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }
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
		$this->load->model('blog/interaction');

		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByCategory($category_id) as $post) {
            $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
            $author = $this->model_blog_author->getAuthor($post['author_id']);
            $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
            $like_count = $this->model_blog_interaction->getInteractionsForPost('like', $post['post_id']);

            $extra_data_array = array(
                'body_html' => html_entity_decode($post['body']),
                'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
                'categories' => $categories,
                'comment_count' => $comment_count,
                'like_count' => $like_count,
                'actions' => array());

            if($this->session->data['is_owner']){
                if($post['deleted'] == 1){
                    $extra_data_array['actions']['undelete'] = array('title' => 'Undelete', 'icon' => "<i class='fa fa-undo'></i>", 'link' => $this->url->link('micropub/client/undeletePost', 'id='.$post['post_id'],''));
                } else {
                    $extra_data_array['actions']['edit'] = array('title' => 'Edit', 'icon' => "<i class='fa fa-edit'></i>", 'link' => $this->url->link('micropub/client/editPost', 'id='.$post['post_id'],''));
                    $extra_data_array['actions']['delete'] = array('title' => 'Delete', 'icon' => "<i class='fa fa-trash'></i>", 'link' => $this->url->link('micropub/client/deletePost', 'id='.$post['post_id'],''));
                }
            }
            if($mpconfig['repost']){
                $extra_data_array['actions']['repost'] = array('title' => 'Repost', 'icon' => "<i class='fa fa-share-square-o'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
            }
            if($mpconfig['reply']){
                $extra_data_array['actions']['reply'] = array('title' => 'Reply', 'icon' => "<i class='fa fa-reply'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
            }
            if($mpconfig['like']){
                $extra_data_array['actions']['like'] = array('title' => 'Like', 'icon' => "<i class='fa fa-heart'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
            }
            if($mpconfig['bookmark']){
                $extra_data_array['actions']['bookmark'] = array('title' => 'Bookmark', 'icon' => "<i class='fa fa-bookmark'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
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
