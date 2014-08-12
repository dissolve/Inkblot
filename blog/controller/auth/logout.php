<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        unset($this->session->data['user_id']);
        $this->response->redirect($this->url->link(''));
	}
}
?>
