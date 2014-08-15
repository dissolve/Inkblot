<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        unset($this->session->data['user_site']);
        $this->response->redirect($this->url->link(''));
	}
}
?>
