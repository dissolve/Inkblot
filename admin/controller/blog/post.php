<?php
class ControllerBlogPost extends Controller { 
	private $error = array();

	public function index() {
		$this->document->setTitle();
		$this->load->model('blog/post');

        if($this->request->get['id']){
			$post = $this->model_blog_post->getPost($this->request->get['id']);
            $post['edit'] = $this->url->link('blog/post/update', '&id='.$post['post_id'] , ''); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
            $post['body_html'] = html_entity_decode($post['body']);

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
                'href' => $this->url->link('blog/post', '&id='.$post['post_id'] , '') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
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

		if (($this->request->server['REQUEST_METHOD'] == 'POST')){ // && $this->validateForm()) {
			$this->model_blog_post->addPost($this->request->post);

			$this->session->data['success'] = 'success';

			$url = '';

			//$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			$this->response->redirect($this->url->link('blog/post/insert'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('blog/post');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/post');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->blog->editPost($this->request->get['post_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('blog/post');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/post');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $post_id) {
				$this->model_blog_post->deletePost($post_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
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
			'text' => '',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => '',
			'href' => $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['insert'] = $this->url->link('blog/post/insert'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('blog/post/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['posts'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		//$post_total = $this->model_blog_post->getTotalPosts();

		//$results = $this->model_blog_post->getPosts($filter_data);

		foreach ($results as $result) {
			$data['posts'][] = array(
				'post_id' => $result['post_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('blog/post/update', 'token=' . $this->session->data['token'] . '&post_id=' . $result['post_id'] . $url, 'SSL')
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

		$data['sort_title'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . '&sort=id.title' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');

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
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

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
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'New Post',
			'href' => $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['post_id'])) {
            $data['insert'] = $this->url->link('blog/post/insert'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('blog/post/update', 'token=' . $this->session->data['token'] . '&post_id=' . $this->request->get['post_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('blog/post', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['post_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$post_info = $this->model_blog_post->getPost($this->request->get['post_id']);
		}

		$data['token'] = $this->session->data['token'];

		//$this->load->model('localisation/language');

		//$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['post_description'])) {
			$data['post_description'] = $this->request->post['post_description'];
		} elseif (isset($this->request->get['post_id'])) {
			$data['post_description'] = $this->model_blog_post->getPostDescriptions($this->request->get['post_id']);
		} else {
			$data['post_description'] = array();
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['post_store'])) {
			$data['post_store'] = $this->request->post['post_store'];
		} elseif (isset($this->request->get['post_id'])) {
			$data['post_store'] = $this->model_blog_post->getPostStores($this->request->get['post_id']);
		} else {
			$data['post_store'] = array(0);
		}		

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($post_info)) {
			$data['keyword'] = $post_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($post_info)) {
			$data['bottom'] = $post_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($post_info)) {
			$data['status'] = $post_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($post_info)) {
			$data['sort_order'] = $post_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['post_layout'])) {
			$data['post_layout'] = $this->request->post['post_layout'];
		} elseif (isset($this->request->get['post_id'])) {
			$data['post_layout'] = $this->model_blog_post->getPostLayouts($this->request->get['post_id']);
		} else {
			$data['post_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/post_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'blog/post')) {
			$this->error['warning'] = 'No Permission';
		}

		//foreach ($this->request->post['post_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'] = '';
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'] = '';
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'] = '';
			}
		//}

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

			$store_total = $this->model_setting_store->getTotalStoresByPostId($post_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
		}

		return !$this->error;
	}
}
