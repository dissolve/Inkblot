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
        $data['posts'] = $this->url->link('blog/posts');
        $data['pages'] = $this->url->link('blog/pages');
        $data['notes'] = $this->url->link('blog/notes');
        $data['categories'] = $this->url->link('blog/categories');
        $data['comments'] = $this->url->link('blog/comments');
		
		return $this->load->view('common/menu.tpl', $data);
	}
}
