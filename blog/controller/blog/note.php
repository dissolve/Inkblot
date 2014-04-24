<?php  
class ControllerBlogNote extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

		$this->load->model('blog/note');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/like');
		$this->load->model('blog/mention');
		
		$note = $this->model_blog_note->getNoteByDayCount($year, $month, $day, $daycount);
        $author = $this->model_blog_author->getAuthor($note['author_id']);
        $categories = $this->model_blog_category->getCategoriesForPost($note['note_id']);
        $comment_count = $this->model_blog_comment->getCommentCountForPost($note['note_id']);
        $comments = $this->model_blog_comment->getCommentsForPost($note['note_id']);
        $mentions = $this->model_blog_mention->getMentionsForPost($note['note_id']);
        $like_count = $this->model_blog_like->getLikeCountForPost($note['note_id']);
        $likes = $this->model_blog_like->getLikesForPost($note['note_id']);

        $data['note'] = array_merge($note, array(
            'body_html' => html_entity_decode($note['body']),
            'author' => $author,
            'categories' => $categories,
            'comment_count' => $comment_count,
            'comments' => $comments,
            'mentions' => $mentions,
            'like_count' => $like_count,
            'likes' => $likes
            ));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/note.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/note.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/note.tpl', $data));
		}
	}

}
?>
