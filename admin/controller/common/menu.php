<?php 
class ControllerCommonMenu extends Controller {
	public function index() {	
		
		
		if (!isset($this->request->get['token']) || !isset($this->session->data['token']) && ($this->request->get['token'] != $this->session->data['token'])) {
			$data['logged'] = false;
			
			$data['home'] = $this->url->link('common/dashboard', '', 'SSL');
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
		}
        $data['posts'] = $this->url->link('blog/post');
        $data['pages'] = $this->url->link('blog/page');
        $data['notes'] = $this->url->link('blog/note');
        $data['categories'] = $this->url->link('blog/category');
        $data['comments'] = $this->url->link('blog/comment');
		
		return $this->load->view('common/menu.tpl', $data);
	}
}
