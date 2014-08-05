<?php
class ControllerBlogPhoto extends Controller { 
	private $error = array();

	public function index() {
		$this->document->setTitle();
		$this->load->model('blog/photo');

		if($this->request->get['photo_id']){
				$photo = $this->model_blog_photo->getPhoto($this->request->get['photo_id']);
		    if($this->request->get['send_mention']){
			include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

			$client = new IndieWeb\MentionClient($photo['permalink'], '<a href="'.$photo['replyto'].'">ReplyTo</a>' . html_entity_decode($photo['body']));
			$client->debug(false);
			$sent = $client->sendSupportedMentions();
			$this->log->write($sent);
			$data['success'] = 'Webmentions Sent';

		    }

		    $photo['edit'] = $this->url->link('blog/photo/insert', '&id='.$photo['photo_id'] , ''); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		    $photo['body_html'] = html_entity_decode($photo['body']);
				$photo['send_mention'] = $this->url->link('blog/photo', 'send_mention=true&photo_id=' . $photo['photo_id'] . $url, '');

				$data['photo'] = $photo;

		    $data['breadcrumbs'] = array();

		    $data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard') //, 'token=' . $this->session->data['token'], 'SSL')
		    );

		    $data['breadcrumbs'][] = array(
			'text' => 'Photos',
			'href' => $this->url->link('blog/photo') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
		    );

		    $data['breadcrumbs'][] = array(
			'text' => $photo['title'],
			'href' => $this->url->link('blog/photo', '&id='.$photo['photo_id'] , '') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
		    );

		    $data['back'] = $this->url->link('blog/photo'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');

		    $data['header'] = $this->load->controller('common/header');
		    $data['menu'] = $this->load->controller('common/menu');
		    $data['footer'] = $this->load->controller('common/footer');

		    $this->response->setOutput($this->load->view('blog/photo_single.tpl', $data));

		} else {
		    $this->getList();
		}
	}

	public function insert() {
		$this->document->setTitle();

		$this->load->model('blog/photo');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$photo_id = $this->model_blog_photo->addPhoto($this->request->post);

			$this->session->data['success'] = 'success';

			$url = '';

			//$this->response->redirect($this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			$this->response->redirect($this->url->link('blog/photo'), 'photo_id='.$photo_id, '');
			// SEND WEBMENTIONS
			$photo = $this->model_blog_photo->getPhoto($photo_id);
			include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

			$client = new IndieWeb\MentionClient($photo['permalink'], '<a href="'.$photo['replyto'].'">ReplyTo</a>' . html_entity_decode($photo['body']));
			$client->debug(false);
			$sent = $client->sendSupportedMentions();
			// END SEND WEBMENTIONS
		}

		$this->getForm();
	}

	public function update() {

		$this->document->setTitle('Update Photo');

		$this->load->model('blog/photo');
        $this->error = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_photo->editPhoto($this->request->get['photo_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url, ''));
		}

		$this->getForm();
	}

	public function delete() {

		$this->document->setTitle('Delete Photo');

		$this->load->model('blog/photo');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $photo_id) {
				$this->model_blog_photo->deletePhoto($photo_id);
			}

			$this->session->data['success'] = 'Successfully Deleted Photo';

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

			$this->response->redirect($this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url, ''));
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
            'text' => 'Photos',
            'href' => $this->url->link('blog/photo') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

		$data['insert'] = $this->url->link('blog/photo/insert'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('blog/photo/delete'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['photos'] = array();

			$start = ($page - 1) * 20;//$this->config->get('config_limit_admin');
			$limit = 20;//$this->config->get('config_limit_admin');

		$photo_total = $this->model_blog_photo->getTotalPhotos();

		$results = $this->model_blog_photo->getPhotos($sort, $order, $limit, $start);

		foreach ($results as $result) {
			$data['photos'][] = array(
				'photo_id' => $result['photo_id'],
				'title'          => $result['title'],
				'image_file'          => $result['image_file'],
				'timestamp'      => $result['timestamp'],
				'permalink'      => $result['permalink'],
				'send_mention'   => $this->url->link('blog/photo', 'send_mention=true&photo_id=' . $result['photo_id'] . $url, ''),
				'view'           => $this->url->link('blog/photo', 'photo_id=' . $result['photo_id'] . $url, ''),
				'edit'           => $this->url->link('blog/photo/update', '&photo_id=' . $result['photo_id'] . $url, '')
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

		$data['sort_title'] = $this->url->link('blog/photo', 'token=' . $this->session->data['token'] . '&sort=title' . $url, '');
		$data['sort_timestamp'] = $this->url->link('blog/photo', 'token=' . $this->session->data['token'] . '&sort=timestamp' . $url, '');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $photo_total;
		$pagination->page = $page;
		$pagination->limit = 20;//$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url . '&page={page}', '');

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/photo_list.tpl', $data));
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

		$data['breadcrumbs'][] = array(
			'text' => 'New Photo',
			'href' => $this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url, '')
		);

        $this->load->model('blog/photo');
        $photo = NULL;

		if (!isset($this->request->get['photo_id'])) {
            $data['insert'] = $this->url->link('blog/photo/insert'); //, 'token=' . $this->session->data['token'] . $url, '');
		} else {
			$data['action'] = $this->url->link('blog/photo/update', 'token=' . $this->session->data['token'] . '&photo_id=' . $this->request->get['photo_id'] . $url, '');
			$photo = $this->model_blog_photo->getPhoto($this->request->get['photo_id']);
			$data['photo'] = $photo;
		}


		$data['cancel'] = $this->url->link('blog/photo', 'token=' . $this->session->data['token'] . $url, '');

		if (isset($this->request->get['photo_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$photo_info = $this->model_blog_photo->getPhoto($this->request->get['photo_id']);
		}

		$data['token'] = $this->session->data['token'];


		//$this->load->model('design/layout');

		//$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/photo_form.tpl', $data));
	}

	protected function validateForm() {
		//if (!$this->user->hasPermission('modify', 'blog/photo')) {
			//$this->error['warning'] = 'No Permission';
		//}
        $photo = $this->request->post['photo'];

        //if ((utf8_strlen($photo['title']) < 3) || (utf8_strlen($photo['title']) > 100)) {
            //$this->error['title'] = 'Min: 3 characters, Max: 100';
        //}

        //if ((utf8_strlen($photo['slug']) < 3) || (utf8_strlen($photo['slug']) > 100)) {
            //$this->error['slug'] = 'Min: 3 characters, Max: 100';
        //}

        if (utf8_strlen($photo['body']) < 3) {
            $this->error['body'] = 'Body Too Short';
        }

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = '';
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'blog/photo')) {
			$this->error['warning'] = '';
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $photo_id) {
			if ($this->config->get('config_account_id') == $photo_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_checkout_id') == $photo_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_affiliate_id') == $photo_id) {
				$this->error['warning'] = '';
			}

		}

		return !$this->error;
	}


}
