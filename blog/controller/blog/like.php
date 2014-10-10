<?php  
class ControllerBlogLike extends Controller {
	public function index() {

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

		$this->load->model('blog/like');
		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/comment');
		$this->load->model('blog/post');
		$this->load->model('blog/mention');
		$this->load->model('blog/context');
		
		$post = $this->model_blog_like->getLikeByDayCount($year, $month, $day, $daycount);
        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
        }

        if(intval($post['deleted']) == 1 && !$this->session->data['is_owner']) {
            $data['deleted'] = true;

            $this->document->setTitle('Deleted');
            $this->document->setDescription('Entry Deleted');
            header('HTTP/1.1 410 Gone');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/deleted.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/deleted.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/common/deleted.tpl', $data));
            }


        } else {
            if(intval($post['deleted']) == 1) {
                $data['deleted'] = true;
            }
            $author = $this->model_blog_author->getAuthor($post['author_id']);
            $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
            $comment_count = $this->model_blog_comment->getCommentCountForPost($post['post_id']);
            $fetch_comments = $this->model_blog_comment->getCommentsForPost($post['post_id']);
            $comments = array();
            if(!isset($this->session->data['user_site'])){
                $comments = $fetch_comments;
            } else {
                foreach($fetch_comments as $comm){
                    $clean_comm = trim(str_replace(array('http://','https://'),array('',''), $comm['source_url']), '/');
                    $clean_user = trim(str_replace(array('http://','https://'),array('',''), $this->session->data['user_site']), '/');
                    if(strpos($clean_comm,$clean_user) === 0){
                        $comm['editlink'] = $this->url->link('micropub/client/editPost', 'url='.$comm['source_url']);
                        $comm['deletelink'] = $this->url->link('micropub/client/deletePost', 'url='.$comm['source_url']);
                    }
                    $comments[] = $comm;
                }
            }

            $mentions = $this->model_blog_mention->getMentionsForPost($post['post_id']);
            $like_count = $this->model_blog_post->getLikeCountForPost($post['post_id']);
            $likes = $this->model_blog_post->getLikesForPost($post['post_id']);
            $context = $this->model_blog_context->getAllContextForPost($post['post_id']);

            $data['post'] = array_merge($post, array(
                'body_html' => html_entity_decode($post['body']),
                'author' => $author,
                'author_image' => '/image/static/icon_128.jpg',
                'categories' => $categories,
                'comment_count' => $comment_count,
                'comments' => $comments,
                'mentions' => $mentions,
                'like_count' => $like_count,
                'likes' => $likes,
                'context' => $context
                ));


            if($this->session->data['is_owner']){
                $data['post']['repostlink'] = $this->url->link('micropub/client', 'id='.$data['post']['post_id'],'');
                if(!$data['deleted']){
                    $data['post']['editlink'] = $this->url->link('micropub/client/editPost', 'id='.$data['post']['post_id'],'');
                    $data['post']['deletelink'] = $this->url->link('micropub/client/deletePost', 'id='.$data['post']['post_id'],'');
                } elseif ($this->session->data['is_owner'] && $data['deleted']){
                    $data['post']['undeletelink'] = $this->url->link('micropub/client/undeletePost', 'id='.$data['post']['post_id'],'');
                }
            }


            $title = strip_tags($data['post']['title']);
            $body = strip_tags($data['post']['body_html']);
            $short_title = (strlen(html_entity_decode($title)) > 60 ? htmlentities(substr(html_entity_decode($title), 0, 57). '...') : $title);
            $description = (strlen(html_entity_decode($body)) > 200 ? htmlentities(substr(html_entity_decode($body), 0, 197). '...') : $body);

            $this->document->setTitle($title);
            $this->document->setDescription($description);

            $this->document->addMeta('twitter:card', 'summary');
            $this->document->addMeta('twitter:title', $short_title);
            $this->document->addMeta('twitter:description', $description);
            $this->document->addMeta('twitter:image', '/image/static/icon_200.jpg');

            $this->document->addMeta('og:type', 'article');
            $this->document->addMeta('og:title', $short_title);
            $this->document->addMeta('og:description', $description);
            $this->document->addMeta('og:image', '/image/static/icon_200.jpg');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/like.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/like.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/blog/like.tpl', $data));
            }
        } // end else not deleted
	}

}
?>
