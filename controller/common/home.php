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
        if($this->session->data['mp-config']){
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }

		foreach ($this->model_blog_post->getPostsByTypes(['article'], 20, $skip) as $post) {
			$author = $this->model_blog_author->getAuthor($post['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($post['post_id']);
			$like_count = $this->model_blog_post->getLikeCountForPost($post['post_id']);

            $extra_data_array = array(
			    'body_html' => html_entity_decode($post['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
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

		$data['side_posts'] = array();

		foreach ($this->model_blog_post->getPostsByTypes(['photo','note','rsvp','like','bookmark','checkin'], 20, $skip) as $post) {
			$author = $this->model_blog_author->getAuthor($post['author_id']);
			$categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
			$comment_count = $this->model_blog_comment->getCommentCountForPost($post['post_id']);
			$like_count = $this->model_blog_post->getLikeCountForPost($post['post_id']);

			$extra_data_array = array(
			    'body_html' => html_entity_decode(isset($post['excerpt']) ? $post['excerpt']. '...' : $post['body']),
			    'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
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

            $data['side_posts'][] = array_merge($post, $extra_data_array);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
		}
	}
}
?>