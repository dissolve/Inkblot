<?php  
class ControllerBlogShortener extends Controller {
	public function index() {

        $id = $this->request->get['eid'];

		$this->load->model('blog/note');
		
		$post = $this->model_blog_post->getPost($id);

        $this->response->redirect($post['permalink']);
	}

}
?>
