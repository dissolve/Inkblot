<?php  
class ControllerAuthLogout extends Controller {
	public function index() {
        $user = $this->session->data['user_site'];
        unset($this->session->data['syndication_'.$user]);
        unset($this->session->data['micropub_'.$user]);

        unset($this->session->data['user_site']);
        unset($this->session->data['token']);
        unset($this->session->data['scope']);
        unset($this->session->data['is_owner']);
        unset($this->session->data['mp-config']);
        $this->session->data['success'] = "Logged out";
        $this->response->redirect($this->url->link(''));
	}
}
?>
