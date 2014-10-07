<?php  
class ControllerBlogArchive extends Controller {
	public function index() {

        $month_names = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $month = $this->request->get['month'];
        $year = $this->request->get['year'];

		$this->document->setTitle('Entries for  '.$month_names[$month] .', '.$year);
		$data['title'] = 'Entries for '.$month_names[$month] .', '.$year;

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/author');
		$this->load->model('blog/post');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/post');


		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByArchive($year, $month) as $post) {
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

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/archive.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/archive.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/archive.tpl', $data));
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

		$this->load->model('blog/author');
		$this->load->model('blog/post');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/post');


		$data['posts'] = array();

		foreach ($this->model_blog_post->getPostsByDay($year, $month, $day) as $post) {
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

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/archive.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/archive.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/archive.tpl', $data));
		}
	}
}
?>
