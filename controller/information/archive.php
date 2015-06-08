<?php  
class ControllerInformationArchive extends Controller {
	public function index() {
        if($this->session->data['mp-config']){
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }

        $month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $month = $this->request->get['month'];
        $year = $this->request->get['year'];

		$this->document->setTitle('Posts for  '.$month_names[$month] .', '.$year);
		$data['title'] = 'Posts for '.$month_names[$month] .', '.$year;

		$this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setBodyClass('h-feed');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/post');
		$this->load->model('blog/interaction');
		$this->load->model('blog/category');

		$data['posts'] = array();

		foreach ($this->model_blog_post->getAnyPostsByArchive($year, $month) as $post) {
            $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
            $author = array('link' => $this->url->link('') , 'display_name' => AUTHOR_NAME);
            $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
            $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['post_id']);

            $extra_data_array = array(
                'body_html' => html_entity_decode($post['body']),
                'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
                'categories' => $categories,
                'comment_count' => $comment_count,
                'like_count' => $like_count,
                'actions' => array());

            if(isset($post['following_id']) && !empty($post['following_id'])){
                $this->load->model('contacts/following');
			    $extra_data_array['follow'] = $this->model_contacts_following->getFollowing($post['following_id']);
            }
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
	public function day() {

        $month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $day = $this->request->get['day'];
        $month = $this->request->get['month'];
        $year = $this->request->get['year'];

		$this->document->setTitle('Entries for  '.$month_names[$month] .' '.$day.', '.$year);
		$data['title'] = 'Entries for '.$month_names[$month] .' '.$day. ', '.$year;

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/post');
		$this->load->model('blog/interaction');
		$this->load->model('blog/category');


		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByDay($year, $month, $day) as $post) {
                $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
                $author = array('link' => $this->url->link('') , 'display_name' => AUTHOR_NAME);
                $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
                $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['post_id']);

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
