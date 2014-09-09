<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        unset($this->session->data['user_site']);
        unset($this->session->data['token']);
        $this->session->data['success'] = "Logged out";
        $this->response->redirect($this->url->link(''));
	}
}
?>
