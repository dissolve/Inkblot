<?php  
class ControllerBlogPhoto extends Controller {
	public function index() {

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

		$this->load->model('blog/photo');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/like');
		$this->load->model('blog/mention');
		
		$photo = $this->model_blog_photo->getPhotoByDayCount($year, $month, $day, $daycount);
        $author = $this->model_blog_author->getAuthor($photo['author_id']);
        $categories = $this->model_blog_category->getCategoriesForPost($photo['photo_id']);
        $comment_count = $this->model_blog_comment->getCommentCountForPost($photo['photo_id']);
        $comments = $this->model_blog_comment->getCommentsForPost($photo['photo_id']);
        $mentions = $this->model_blog_mention->getMentionsForPost($photo['photo_id']);
        $like_count = $this->model_blog_like->getLikeCountForPost($photo['photo_id']);
        $likes = $this->model_blog_like->getLikesForPost($photo['photo_id']);

        $data['photo'] = array_merge($photo, array(
            'body_html' => html_entity_decode($photo['body']),
            'image_file' => $photo['image_file'],
            'author' => $author,
            'categories' => $categories,
            'comment_count' => $comment_count,
            'comments' => $comments,
            'mentions' => $mentions,
            'like_count' => $like_count,
            'likes' => $likes
            ));

        if(empty($data['photo']['title'])){
            $data['photo']['title'] = htmlentities(substr(html_entity_decode(strip_tags($data['photo']['body_html'])), 0, 27). '...');
        }

        $title = strip_tags($data['photo']['title']);
        $body = strip_tags($data['photo']['body_html']);
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
		$this->document->addMeta('og:image', '/image/static/icon_128.jpg');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/photo.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/photo.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/photo.tpl', $data));
		}
	}

}
?>
