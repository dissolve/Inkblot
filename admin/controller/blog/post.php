<?php
class ControllerBlogPost extends Controller { 
	private $error = array();

	public function index() {
		$this->document->setTitle();
		$this->load->model('blog/post');

		if($this->request->get['post_id']){
			$post = $this->model_blog_post->getPost($this->request->get['post_id']);
			if($this->request->get['send_mention']){
				include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

				$client = new IndieWeb\MentionClient($post['shortlink'], '<a href="'.$post['replyto'].'">ReplyTo</a>' . html_entity_decode($post['body']));
				$client->debug(false);
				$sent = $client->sendSupportedMentions();
				$this->log->write($sent);
				$data['success'] = 'Webmentions Sent';
			}

			$post['edit'] = $this->url->link('blog/post/update', '&post_id='.$post['post_id'] , ''); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
			$post['body_html'] = html_entity_decode($post['body']);
			$post['send_mention'] = $this->url->link('blog/post', 'send_mention=true&post_id=' . $post['post_id'] . $url, '');

			$data['post'] = $post;

			$data['breadcrumbs'] = array();

		        $data['breadcrumbs'][] = array(
				'text' => 'Home',
				'href' => $this->url->link('common/dashboard') //, 'token=' . $this->session->data['token'], 'SSL')
		        );

		        $data['breadcrumbs'][] = array(
				'text' => 'Posts',
				'href' => $this->url->link('blog/post') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
		        );

		        $data['breadcrumbs'][] = array(
				'text' => $post['title'],
				'href' => $this->url->link('blog/post', '&post_id='.$post['post_id'] , '') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
		        );

		        $data['back'] = $this->url->link('blog/post'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');

		        $data['header'] = $this->load->controller('common/header');
		        $data['menu'] = $this->load->controller('common/menu');
		        $data['footer'] = $this->load->controller('common/footer');

		        $this->response->setOutput($this->load->view('blog/post_single.tpl', $data));
		} else {
			$this->getList();
		}
	}

	public function insert() {
		$this->document->setTitle();

		$this->load->model('blog/post');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['body'] .= '<a href="https://www.brid.gy/publish/twitter"></a><a href="https://www.brid.gy/publish/facebook"></a>';
            
			$post_id = $this->model_blog_post->addPost($this->request->post);

			$this->session->data['success'] = 'success';

			// SEND WEBMENTIONS
			$post = $this->model_blog_post->getPost($post_id);
			include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

            $client = new IndieWeb\MentionClient($post['shortlink'], '<a href="'.$post['replyto'].'">ReplyTo</a>' . html_entity_decode($post['body']));
			$client->debug(false);
			$sent = $client->sendSupportedMentions();
			// END SEND WEBMENTIONS

			//$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			$this->response->redirect($this->url->link('blog/post'), 'post_id='.$post_id, '');
		}

		$this->getForm();
	}

	public function update() {

		$this->document->setTitle('Update Post');

		$this->load->model('blog/post');
		$this->error = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_post->editPost($this->request->get['post_id'], $this->request->post);

			$this->session->data['success'] = 'Successfully Updated';

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, ''));
		}

		$this->getForm();
	}

	public function delete() {

		$this->document->setTitle('Delete Post');

		$this->load->model('blog/post');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $post_id) {
				$this->model_blog_post->deletePost($post_id);
			}

			$this->session->data['success'] = 'Successfully Deleted Post';

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, ''));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'post_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
		    'text' => 'Home',
		    'href' => $this->url->link('common/dashboard') //, 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
		    'text' => 'Posts',
		    'href' => $this->url->link('blog/post') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['insert'] = $this->url->link('blog/post/insert'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('blog/post/delete'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['posts'] = array();

			$start = ($page - 1) * 20;//$this->config->get('config_limit_admin');
			$limit = 20;//$this->config->get('config_limit_admin');

		$post_total = $this->model_blog_post->getTotalPosts();

		$results = $this->model_blog_post->getPosts($sort, $order, $limit, $start);

		foreach ($results as $result) {
			$data['posts'][] = array(
				'post_id' => $result['post_id'],
				'title'          => $result['title'],
				'timestamp'      => $result['timestamp'],
				'permalink'      => $result['shortlink'],
				'send_mention'   => $this->url->link('blog/post', 'send_mention=true&post_id=' . $result['post_id'] . $url, ''),
				'view'           => $this->url->link('blog/post', '&post_id=' . $result['post_id'] . $url, ''),
				'edit'           => $this->url->link('blog/post/update', '&post_id=' . $result['post_id'] . $url, '')
			);
		}	



		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . '&sort=title' . $url, '');
		$data['sort_timestamp'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . '&sort=timestamp' . $url, '');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $post_total;
		$pagination->page = $page;
		$pagination->limit = 20;//$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url . '&page={page}', '');

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/post_list.tpl', $data));
	}

	protected function getForm() {

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = '';
		}	

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], '')
		);

		if (!isset($this->request->get['post_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => 'New Post',
				'href' => $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, '')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => 'Edit Post',
				'href' => $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, '')
			);
		}

		$this->load->model('blog/post');
		$post = NULL;

		if (!isset($this->request->get['post_id'])) {
			$data['insert'] = $this->url->link('blog/post/insert'); //, 'token=' . $this->session->data['token'] . $url, '');
		} else {
			$data['action'] = $this->url->link('blog/post/update', 'token=' . $this->session->data['token'] . '&post_id=' . $this->request->get['post_id'] . $url, '');
			$post = $this->model_blog_post->getPost($this->request->get['post_id']);
			$data['post'] = $post;
		}


		$data['cancel'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, '');

		if (isset($this->request->get['post_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$post_info = $this->model_blog_post->getPost($this->request->get['post_id']);
		}

		$data['token'] = $this->session->data['token'];


		//$this->load->model('design/layout');

		//$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/post_form.tpl', $data));
	}

	protected function validateForm() {
		//if (!$this->user->hasPermission('modify', 'blog/post')) {
			//$this->error['warning'] = 'No Permission';
		//}
		$post = $this->request->post['post'];

		if ((utf8_strlen($post['title']) < 3) || (utf8_strlen($post['title']) > 100)) {
		    $this->error['title'] = 'Min: 3 characters, Max: 100';
		}

		if ((utf8_strlen($post['slug']) < 3) || (utf8_strlen($post['slug']) > 100)) {
		    $this->error['slug'] = 'Min: 3 characters, Max: 100';
		}

		if (utf8_strlen($post['body']) < 3) {
		    $this->error['body'] = 'Body Too Short';
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = '';
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'blog/post')) {
			$this->error['warning'] = '';
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $post_id) {
			if ($this->config->get('config_account_id') == $post_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_checkout_id') == $post_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_affiliate_id') == $post_id) {
				$this->error['warning'] = '';
			}

		}

		return !$this->error;
	}
}
