<?php   
class ControllerErrorNotFound extends Controller {
	public function index() {		
		
		$this->document->setTitle(PAGE_TITLE);
		
		
		if (isset($this->request->get['route'])) {
			$data = $this->request->get;
			
			unset($data['_route_']);
			
			$route = $data['route'];
			
			unset($data['route']);
			
			$url = '';
			
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}	
			
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$connection = 'SSL';
			} else {
				$connection = 'NONSSL';
			}
											
		}
		
		$this->data['heading_title'] = PAGE_TITLE;
		
		
		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
		
		$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
		} else {
			$this->template = 'default/template/error/not_found.tpl';
		}
		
		$this->children = array(
			'common/footer',
			'common/header'
		);
		
		$this->response->setOutput($this->render());
  	}
}
?>
