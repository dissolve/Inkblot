<?php   
class ControllerCommonDashboard extends Controller {   
	public function index() {



		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => '',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => '',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		//$data['token'] = $this->session->data['token'];

		// Total Orders


		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/dashboard.tpl', $data));
	}

}
