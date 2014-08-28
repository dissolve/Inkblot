<?php  
class ControllerBlogNote extends Controller {
	public function index() {

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

        if(empty($data['note']['title'])){
            $data['note']['title'] = htmlentities(substr(html_entity_decode(strip_tags($data['note']['body_html'])), 0, 27). '...');
        }

        $title = strip_tags($data['note']['title']);
        $body = strip_tags($data['note']['body_html']);
        $short_title = (strlen(html_entity_decode($title)) > 30 ? htmlentities(substr(html_entity_decode($title), 0, 27). '...') : $title);
        $description = (strlen(html_entity_decode($body)) > 200 ? htmlentities(substr(html_entity_decode($body), 0, 197). '...') : $body);

		$this->document->setTitle($title);
		$this->document->setDescription($description);

		$this->document->addMeta('twitter:card', 'summary');
		$this->document->addMeta('twitter:title', $short_title);
		$this->document->addMeta('twitter:description', $description);

		$this->document->addMeta('og:type', 'article');
		$this->document->addMeta('og:title', $short_title);
		$this->document->addMeta('og:description', $description);

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/note.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/note.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/note.tpl', $data));
		}
	}

}
?>
