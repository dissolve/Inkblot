<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        unset($this->session->data['user_site']);
        $this->session->data['success'] = "Logged out";
        $this->response->redirect($this->url->link(''));
	}
}
?>
