<?php
class ControllerContactsFriends extends Controller { 
	private $error = array();

	public function index() {
		$this->document->setTitle();
		$this->load->model('blog/note');

        if($this->request->get['note_id']){
			$note = $this->model_blog_note->getNote($this->request->get['note_id']);
            if($this->request->get['send_mention']){
                include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

                $client = new IndieWeb\MentionClient($note['permalink'], '<a href="'.$note['replyto'].'">ReplyTo</a>' . html_entity_decode($note['body']));
                $client->debug(false);
                $sent = $client->sendSupportedMentions();
                $this->log->write($sent);
                $data['success'] = 'Webmentions Sent';

            }

            $note['edit'] = $this->url->link('blog/note/insert', '&id='.$note['note_id'] , ''); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
            $note['body_html'] = html_entity_decode($note['body']);
			$note['send_mention'] = $this->url->link('blog/note', 'send_mention=true&note_id=' . $note['note_id'] . $url, '');

			$data['note'] = $note;

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => 'Home',
                'href' => $this->url->link('common/dashboard') //, 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => 'Notes',
                'href' => $this->url->link('blog/note') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $note['title'],
                'href' => $this->url->link('blog/note', '&id='.$note['note_id'] , '') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

            $data['back'] = $this->url->link('blog/note'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');

            $data['header'] = $this->load->controller('common/header');
            $data['menu'] = $this->load->controller('common/menu');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('blog/note_single.tpl', $data));

        } else {
            $this->getList();
        }
	}

	public function insert() {
		$this->document->setTitle();

		$this->load->model('blog/note');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$note_id = $this->model_blog_note->addNote($this->request->post);

			$this->session->data['success'] = 'success';

			$url = '';

			//$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			$this->response->redirect($this->url->link('blog/note'), 'note_id='.$note_id, '');
			// SEND WEBMENTIONS
			$note = $this->model_blog_note->getNote($note_id);
			include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

			$client = new IndieWeb\MentionClient($note['permalink'], '<a href="'.$note['replyto'].'">ReplyTo</a>' . html_entity_decode($note['body']));
			$client->debug(false);
			$sent = $client->sendSupportedMentions();
			// END SEND WEBMENTIONS
		}

		$this->getForm();
	}

	public function update() {

		$this->document->setTitle('Update Note');

		$this->load->model('blog/note');
        $this->error = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_blog_note->editNote($this->request->get['note_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, ''));
		}

		$this->getForm();
	}

	public function delete() {

		$this->document->setTitle('Delete Note');

		$this->load->model('blog/note');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $note_id) {
				$this->model_blog_note->deleteNote($note_id);
			}

			$this->session->data['success'] = 'Successfully Deleted Note';

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

			$this->response->redirect($this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, ''));
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
            'text' => 'Notes',
            'href' => $this->url->link('blog/note') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

		$data['insert'] = $this->url->link('blog/note/insert'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('blog/note/delete'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['notes'] = array();

			$start = ($page - 1) * 20;//$this->config->get('config_limit_admin');
			$limit = 20;//$this->config->get('config_limit_admin');

		$note_total = $this->model_blog_note->getTotalNotes();

		$results = $this->model_blog_note->getNotes($sort, $order, $limit, $start);

		foreach ($results as $result) {
			$data['notes'][] = array(
				'note_id' => $result['note_id'],
				'title'          => $result['title'],
				'timestamp'      => $result['timestamp'],
				'permalink'      => $result['permalink'],
				'send_mention'   => $this->url->link('blog/note', 'send_mention=true&note_id=' . $result['note_id'] . $url, ''),
				'view'           => $this->url->link('blog/note', 'note_id=' . $result['note_id'] . $url, ''),
				'edit'           => $this->url->link('blog/note/update', '&note_id=' . $result['note_id'] . $url, '')
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

		$data['sort_title'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . '&sort=title' . $url, '');
		$data['sort_timestamp'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . '&sort=timestamp' . $url, '');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $note_total;
		$pagination->page = $page;
		$pagination->limit = 20;//$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url . '&page={page}', '');

		$data['pagination'] = $pagination->render();


		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/note_list.tpl', $data));
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
			'text' => 'New Note',
			'href' => $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, '')
		);

        $this->load->model('blog/note');
        $note = NULL;

		if (!isset($this->request->get['note_id'])) {
            $data['insert'] = $this->url->link('blog/note/insert'); //, 'token=' . $this->session->data['token'] . $url, '');
		} else {
			$data['action'] = $this->url->link('blog/note/update', 'token=' . $this->session->data['token'] . '&note_id=' . $this->request->get['note_id'] . $url, '');
			$note = $this->model_blog_note->getNote($this->request->get['note_id']);
			$data['note'] = $note;
		}


		$data['cancel'] = $this->url->link('blog/note', 'token=' . $this->session->data['token'] . $url, '');

		if (isset($this->request->get['note_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$note_info = $this->model_blog_note->getNote($this->request->get['note_id']);
		}

		$data['token'] = $this->session->data['token'];


		//$this->load->model('design/layout');

		//$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog/note_form.tpl', $data));
	}

	protected function validateForm() {
		//if (!$this->user->hasPermission('modify', 'blog/note')) {
			//$this->error['warning'] = 'No Permission';
		//}
        $note = $this->request->post['note'];

        //if ((utf8_strlen($note['title']) < 3) || (utf8_strlen($note['title']) > 100)) {
            //$this->error['title'] = 'Min: 3 characters, Max: 100';
        //}

        //if ((utf8_strlen($note['slug']) < 3) || (utf8_strlen($note['slug']) > 100)) {
            //$this->error['slug'] = 'Min: 3 characters, Max: 100';
        //}

        if (utf8_strlen($note['body']) < 3) {
            $this->error['body'] = 'Body Too Short';
        }

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = '';
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'blog/note')) {
			$this->error['warning'] = '';
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $note_id) {
			if ($this->config->get('config_account_id') == $note_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_checkout_id') == $note_id) {
				$this->error['warning'] = '';
			}

			if ($this->config->get('config_affiliate_id') == $note_id) {
				$this->error['warning'] = '';
			}

		}

		return !$this->error;
	}


}
