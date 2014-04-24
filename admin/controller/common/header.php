<?php 
class ControllerCommonHeader extends Controller {
	public function index() {
		$data['title'] = $this->document->getTitle(); 
			
		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}
		
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();	
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		
		
		if (!$this->user->isLogged()){
			$data['logged'] = '';
			
			$data['home'] = $this->url->link('common/dashboard', '', '');//'SSL');
		} else {
			$data['logged']  = $this->user->getUserName();

			$data['home'] = $this->url->link('common/dashboard', '','');// 'SSL');
			$data['logout'] = $this->url->link('common/logout', '', '');//'SSL');
			


			//$data['alerts'] = $customer_total + $product_total + $review_total + $return_total + $affiliate_total;
            $data['alerts'] = '';

		}
		
		$this->load->model('user/user');

		$this->load->model('tool/image');

		$user_info = $this->model_user_user->getUser($this->user->getId());

		if ($user_info) {
			$data['username'] = $user_info['firstname'] . ' ' . $user_info['lastname'];

			if (is_file(DIR_IMAGE . $user_info['image'])) {
				$data['image'] = $this->model_tool_image->resize($user_info['image'], 24, 24);
			} else {
				$data['image'] = '';
			}
		} else {
			$data['username'] = '';
			$data['image'] = '';
		}
					
		return $this->load->view('common/header.tpl', $data);
	}
}
