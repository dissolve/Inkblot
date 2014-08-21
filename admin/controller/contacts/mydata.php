<?php
class ControllerContactsMydata extends Controller { 
    private $error = array();

    public function index() {
	$this->document->setTitle();
	$this->load->model('contacts/mydata');

	if($this->request->get['data_id']){
	    $mydata = $this->model_contacts_mydata->getNote($this->request->get['data_id']);
	    if($this->request->get['send_mention']){
		include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

		$client = new IndieWeb\MentionClient($mydata['permalink'], '<a href="'.$mydata['replyto'].'">ReplyTo</a>' . html_entity_decode($mydata['body']));
		$client->debug(false);
		$sent = $client->sendSupportedMentions();
		$this->log->write($sent);
		$data['success'] = 'Webmentions Sent';

	    }

	    $mydata['edit'] = $this->url->link('contacts/mydata/insert', '&id='.$mydata['data_id'] , ''); //, 'token=' . $this->session->data['token'] . $url, 'SSL');
	    $mydata['body_html'] = html_entity_decode($mydata['body']);
		$mydata['send_mention'] = $this->url->link('contacts/mydata', 'send_mention=true&data_id=' . $mydata['data_id'] , '');

		$data['mydata'] = $mydata;

	    $data['breadcrumbs'] = array();

	    $data['breadcrumbs'][] = array(
		'text' => 'Home',
		'href' => $this->url->link('common/dashboard') //, 'token=' . $this->session->data['token'], 'SSL')
	    );

	    $data['breadcrumbs'][] = array(
		'text' => 'Notes',
		'href' => $this->url->link('contacts/mydata') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
	    );

	    $data['breadcrumbs'][] = array(
		'text' => $mydata['title'],
		'href' => $this->url->link('contacts/mydata', '&id='.$mydata['data_id'] , '') //, 'token=' . $this->session->data['token'] . $url, 'SSL')
	    );

	    $data['back'] = $this->url->link('contacts/mydata'); //, 'token=' . $this->session->data['token'] . $url, 'SSL');

	    $data['header'] = $this->load->controller('common/header');
	    $data['menu'] = $this->load->controller('common/menu');
	    $data['footer'] = $this->load->controller('common/footer');

	    $this->response->setOutput($this->load->view('contacts/mydata_single.tpl', $data));

	} else {
	    $this->getList();
	}
    }

    public function insert() {
	$this->document->setTitle();

	$this->load->model('contacts/mydata');

	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	    $data_id = $this->model_contacts_mydata->addNote($this->request->post);

	    $this->session->data['success'] = 'success';

	    $this->response->redirect($this->url->link('contacts/mydata'), 'data_id='.$data_id, '');
	    // SEND WEBMENTIONS
	    $mydata = $this->model_contacts_mydata->getNote($data_id);
	    include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';

	    $client = new IndieWeb\MentionClient($mydata['permalink'], '<a href="'.$mydata['replyto'].'">ReplyTo</a>' . html_entity_decode($mydata['body']));
	    $client->debug(false);
	    $sent = $client->sendSupportedMentions();
	    // END SEND WEBMENTIONS
	}

	$this->getForm();
    }

    public function update() {

	$this->document->setTitle('Update Note');

	$this->load->model('contacts/mydata');
	$this->error = array();

	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	    $this->model_contacts_mydata->editNote($this->request->get['data_id'], $this->request->post);

	    $this->session->data['success'] = 'Successfully Updated';

	    $this->response->redirect($this->url->link('contacts/mydata', 'token=' . $this->session->data['token'], ''));
	}

	$this->getForm();
    }

    public function delete() {

	$this->document->setTitle('Delete Note');

	$this->load->model('contacts/mydata');

	if (isset($this->request->post['selected']) && $this->validateDelete()) {
	    foreach ($this->request->post['selected'] as $data_id) {
		$this->model_contacts_mydata->deleteNote($data_id);
	    }

	    $this->session->data['success'] = 'Successfully Deleted Note';

	    $this->response->redirect($this->url->link('contacts/mydata', 'token=' . $this->session->data['token'], ''));
	}

	$this->getList();
    }

    protected function getList() {
	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
	    'text' => 'Home',
	    'href' => $this->url->link('common/dashboard')
	);

	$data['breadcrumbs'][] = array(
	    'text' => 'My Data',
	    'href' => $this->url->link('contacts/mydata')
	);

	$data['insert'] = $this->url->link('contacts/mydata/insert');
	$data['delete'] = $this->url->link('contacts/mydata/delete');

	$data['mydata'] = array();


	$data_entries = $this->model_contacts_mydata->getAllData();

	foreach ($data_entries as $result) {
	    $groups = $this->model_contacts_mydata->getGroupData($result['data_id']);

	    $data['mydata'][] = array(
		'data_id' 	 => $result['data_id'],
		'target'         => $result['target'],
		'rel'            => $result['rel'],
		'sorting'        => $result['sorting'],
		'value'          => $result['value'],
		'field_label'    => $result['field_label'],
		'field_display_image'    => $result['field_display_image'],
		'link_format'    => $result['link_format'],
		'is_link'        => $result['is_link'],
		'classes'        => $result['classes'],
		'groups'         => $groups,
		'edit'           => $this->url->link('contacts/mydata/update', '&data_id=' . $result['data_id'], '')
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



	$data['header'] = $this->load->controller('common/header');
	$data['menu'] = $this->load->controller('common/menu');
	$data['footer'] = $this->load->controller('common/footer');

	$this->response->setOutput($this->load->view('contacts/mydata_list.tpl', $data));
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


	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
	    'text' => 'Home',
	    'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], '')
	);

	$data['breadcrumbs'][] = array(
	    'text' => 'New Note',
	    'href' => $this->url->link('contacts/mydata', 'token=' . $this->session->data['token'] , '')
	);

	$this->load->model('contacts/mydata');
	$mydata = NULL;

	if (!isset($this->request->get['data_id'])) {
	    $data['insert'] = $this->url->link('contacts/mydata/insert'); //, 'token=' . $this->session->data['token'] . $url, '');
	} else {
	    $data['action'] = $this->url->link('contacts/mydata/update', 'token=' . $this->session->data['token'] . '&data_id=' . $this->request->get['data_id'], '');
	    $mydata = $this->model_contacts_mydata->getNote($this->request->get['data_id']);
	    $data['mydata'] = $mydata;
	}


	$data['cancel'] = $this->url->link('contacts/mydata', 'token=' . $this->session->data['token'], '');

	if (isset($this->request->get['data_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
	    $mydata_info = $this->model_contacts_mydata->getNote($this->request->get['data_id']);
	}

	$data['token'] = $this->session->data['token'];


	//$this->load->model('design/layout');

	//$data['layouts'] = $this->model_design_layout->getLayouts();

	$data['header'] = $this->load->controller('common/header');
	$data['menu'] = $this->load->controller('common/menu');
	$data['footer'] = $this->load->controller('common/footer');

	$this->response->setOutput($this->load->view('contacts/mydata_form.tpl', $data));
    }

    protected function validateForm() {
	//if (!$this->user->hasPermission('modify', 'contacts/mydata')) {
	    //$this->error['warning'] = 'No Permission';
	//}
	$mydata = $this->request->post['mydata'];

	//if ((utf8_strlen($mydata['title']) < 3) || (utf8_strlen($mydata['title']) > 100)) {
	    //$this->error['title'] = 'Min: 3 characters, Max: 100';
	//}

	//if ((utf8_strlen($mydata['slug']) < 3) || (utf8_strlen($mydata['slug']) > 100)) {
	    //$this->error['slug'] = 'Min: 3 characters, Max: 100';
	//}

	if (utf8_strlen($mydata['body']) < 3) {
	    $this->error['body'] = 'Body Too Short';
	}

	if ($this->error && !isset($this->error['warning'])) {
	    $this->error['warning'] = '';
	}

	return !$this->error;
    }

    protected function validateDelete() {
	if (!$this->user->hasPermission('modify', 'contacts/mydata')) {
	    $this->error['warning'] = '';
	}

	$this->load->model('setting/store');

	foreach ($this->request->post['selected'] as $data_id) {
	    if ($this->config->get('config_account_id') == $data_id) {
		$this->error['warning'] = '';
	    }

	    if ($this->config->get('config_checkout_id') == $data_id) {
		$this->error['warning'] = '';
	    }

	    if ($this->config->get('config_affiliate_id') == $data_id) {
		$this->error['warning'] = '';
	    }

	}

	return !$this->error;
    }


}
