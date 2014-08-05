<?php 
class ControllerCommonMenu extends Controller {
	public function index() {	
		
		
		if (!$this->user->isLogged()){
			$data['logged'] = false;
			
			$data['home'] = $this->url->link('common/dashboard', '', '');
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], '');
		}
		$data['posts'] = $this->url->link('blog/post');
		$data['notes'] = $this->url->link('blog/note');
		$data['photos'] = $this->url->link('blog/photo');

		$data['pages'] = $this->url->link('blog/page');
		$data['categories'] = $this->url->link('blog/category');
		$data['comments'] = $this->url->link('blog/comment');
		
		return $this->load->view('common/menu.tpl', $data);
	}
}
