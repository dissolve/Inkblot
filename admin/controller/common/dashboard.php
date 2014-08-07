<?php   
class ControllerCommonDashboard extends Controller {   
	public function index() {



		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Home',
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], '')
		);

		$data['token'] = $this->session->data['token'];


		$data['new_post'] = $this->url->link('blog/post/insert');
		$data['new_note'] = $this->url->link('blog/note/insert');

		$this->load->model('blog/post');
		$this->load->model('blog/note');
		$this->load->model('blog/photo');
		$this->load->model('blog/comment');
		$this->load->model('blog/like');
		$this->load->model('blog/mention');

		$data['posts'] = array();

		foreach ($this->model_blog_post->getRecentPosts(5) as $result) {
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['post_id']);
			$like_count = $this->model_blog_like->getLikeCountForPost($result['post_id']);
			$mention_count = $this->model_blog_mention->getMentionCountForPost($result['post_id']);

			$data['posts'][] = array_merge($result, array(
			    'mention_count' => $mention_count,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count,
			    'view' => $this->url->link('blog/post', 'post_id='.$result['post_id'], '')
			    ));
		}

		$data['notes'] = array();

		foreach ($this->model_blog_note->getRecentNotes(5) as $result) {
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['note_id']);
			$like_count = $this->model_blog_like->getLikeCountForPost($result['note_id']);
			$mention_count = $this->model_blog_mention->getMentionCountForPost($result['note_id']);
			if(empty($result['title'])){
				$result['title'] = '[none]';
			}

			$data['notes'][] = array_merge($result, array(
			    'mention_count' => $mention_count,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count,
			    'view' => $this->url->link('blog/note', 'note_id='.$result['note_id'], '')
			    ));
		}

		$data['photos'] = array();

		foreach ($this->model_blog_photo->getRecentPhotos(5) as $result) {
			$comment_count = $this->model_blog_comment->getCommentCountForPost($result['photo_id']);
			$like_count = $this->model_blog_like->getLikeCountForPost($result['photo_id']);
			$mention_count = $this->model_blog_mention->getMentionCountForPost($result['photo_id']);
			if(empty($result['title'])){
				$result['title'] = '[none]';
			}

			$data['photos'][] = array_merge($result, array(
			    'mention_count' => $mention_count,
			    'comment_count' => $comment_count,
			    'like_count' => $like_count,
			    'view' => $this->url->link('blog/photo', 'photo_id='.$result['photo_id'], '')
			    ));
		}

		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/dashboard.tpl', $data));
	}

}
