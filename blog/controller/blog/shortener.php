<?php  
class ControllerBlogShortener extends Controller {
	public function index() {

		$this->load->model('blog/post');

        $encoded_id = $this->request->get['eid'];

		$id = $this->model_blog_post->sxg_to_num($encoded_id);
		
		$post = $this->model_blog_post->getPost($id);

        if($post['post_id']) {
            $this->response->redirect($post['permalink']);
        } else {
            $this->response->redirect($this->url->link(''));
        }
	}

}
?>
