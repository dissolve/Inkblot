<?php
class ControllerBlogNote extends Controller { 
	private $error = array();

	public function index() {

		$this->document->setTitle();

		$this->load->model('blog/note');

		$this->getList();
	}

	public function insert() {
		$this->document->setTitle();

		$this->load->model('blog/note');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_note->addNote($this->request->post);

			$this->session->data['success'] = 'success';

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['note'])) {
				$url .= '&note=' . $this->request->get['note'];
			}

			$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('blog/note');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/note');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->blog->editNote($this->request->get['note_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['note'])) {
				$url .= '&note=' . $this->request->get['note'];
			}

			$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('blog/note');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('blog/note');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $note_id) {
				$this->model_blog_note->deleteNote($note_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['note'])) {
				$url .= '&note=' . $this->request->get['note'];
			}

			$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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

		if (isset($this->request->get['note'])) {
			$note = $this->request->get['note'];
		} else {
			$note = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['note'])) {
			$url .= '&note=' . $this->request->get['note'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['insert'] = $this->url->link('blog/note/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('blog/note/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['notes'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($note - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$note_total = $this->model_blog_note->getTotalNotes();

		$results = $this->model_blog_note->getNotes($filter_data);

		foreach ($results as $result) {
			$data['notes'][] = array(
				'note_id' => $result['note_id'],
				'title'          => $result['title'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('blog/note/update', 'token=' . $this->session->data['token'] . '&note_id=' . $result['notnote'] . $url, 'SSL')
			);
		}	

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');		

		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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

		if (isset($this->request->get['note'])) {
			$url .= '&note=' . $this->request->get['note'];
		}

		$data['sort_title'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . '&sort=id.title' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . '&sort=i.sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $note_total;
		$pagination->note = $note;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url . '&note={note}', 'SSL');

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/note_list.tpl', $data));
	}

	protected function getForm() {
        $data['lorum'] = 'lorum ipsum';

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

		if (isset($this->request->get['note'])) {
			$url .= '&note=' . $this->request->get['note'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'New note',
			'href' => $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['note_id'])) {
			$data['action'] = $this->url->link('blog/note/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('blog/note/update', 'token=' . $this->session->data['token'] . '&note_id=' . $this->request->get['note_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['note_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$note_info = $this->model_blog_note->getNote($this->request->get['note_id']);
		}

		$data['token'] = $this->session->data['token'];

		//$this->load->model('localisation/language');

		//$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['note_description'])) {
			$data['note_description'] = $this->request->post['note_description'];
		} elseif (isset($this->request->get['note_id'])) {
			$data['note_description'] = $this->model_blog_note->getNoteDescriptions($this->request->get['note_id']);
		} else {
			$data['note_description'] = array();
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['note_store'])) {
			$data['note_store'] = $this->request->post['note_store'];
		} elseif (isset($this->request->get['note_id'])) {
			$data['note_store'] = $this->model_blog_note->getNoteStores($this->request->get['note_id']);
		} else {
			$data['note_store'] = array(0);
		}		

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($note_info)) {
			$data['keyword'] = $note_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($note_info)) {
			$data['bottom'] = $note_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($note_info)) {
			$data['status'] = $note_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($note_info)) {
			$data['sort_order'] = $note_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		if (isset($this->request->post['note_layout'])) {
			$data['note_layout'] = $this->request->post['note_layout'];
		} elseif (isset($this->request->get['post_id'])) {
			$data['note_layout'] = $this->model_blog_note->getNoteLayouts($this->request->get['note_id']);
		} else {
			$data['note_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/note_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'blog/note')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['note_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'blog/note')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $note_id) {
			if ($this->config->get('config_account_id') == $note_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}

			if ($this->config->get('config_checkout_id') == $note_id) {
				$this->error['warning'] = $this->language->get('error_checkout');
			}

			if ($this->config->get('config_affiliate_id') == $note_id) {
				$this->error['warning'] = $this->language->get('error_affiliate');
			}

			$store_total = $this->model_setting_store->getTotalStoresByNoteId($note_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
		}

		return !$this->error;
	}
}
