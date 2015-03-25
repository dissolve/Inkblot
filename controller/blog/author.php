<?php  
class ControllerBlogAuthor extends Controller {
	public function index() {
		$this->response->redirect($this->url->link(''));
	}
}
?>
