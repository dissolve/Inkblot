<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        unset($this->session->data['user_site']);
        unset($this->session->data['token']);
        unset($this->session->data['is_owner']);
        unset($this->session->data['mp-config']);
        $this->session->data['success'] = "Logged out";
        $this->response->redirect($this->url->link(''));
	}
}
?>
