<?php  
class ControllerBlogVideo extends Controller {
	public function index() {
        if($this->session->data['mp-config']){
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

		$this->load->model('blog/post');
		
		$post = $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);

        // redirect if we don't have the correct URL
        if($this->request->get['slug'] != $post['slug'] ) {
            $this->response->redirect($post['permalink']);
        }

		$this->load->model('blog/author');
		$this->load->model('blog/category');
		$this->load->model('blog/post');
		$this->load->model('blog/interaction');
		$this->load->model('blog/context');

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
	    $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
	    $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['post_id']);
	    $fetch_comments = $this->model_blog_interaction->getInteractionsForPost('reply', $post['post_id']);
	    $likes = $this->model_blog_interaction->getInteractionsForPost('like', $post['post_id']);
	    $mentions = $this->model_blog_interaction->getInteractionsForPost('mention', $post['post_id']);

            $comments = array();
            if(!isset($this->session->data['user_site'])){
                $comments = $fetch_comments;
            } else {
                foreach($fetch_comments as $comm){
                    $clean_comm = trim(str_replace(array('http://','https://'),array('',''), $comm['source_url']), '/');
                    $clean_user = trim(str_replace(array('http://','https://'),array('',''), $this->session->data['user_site']), '/');
                    $comm['actions'] = array();
                    if(strpos($clean_comm,$clean_user) === 0){
                        if($mpconfig['edit']){
                            $comm['actions']['edit'] = array('title' => 'Edit', 'icon' => "<i class='fa fa-edit'></i>", 'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['edit']));
                        } else {
                            $comm['actions']['delete'] = array('title' => 'Edit', 'icon' => "<i class='fa fa-edit'></i>", 'link' => $this->url->link('micropub/client/editPost', 'url='.$comm['source_url']));
                        }
                        if($mpconfig['delete']){
                            $comm['actions']['delete'] = array('title' => 'Delete', 'icon' => "<i class='fa fa-trash'></i>", 'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['delete']));
                        } else {
                            $comm['actions']['delete'] = array('title' => 'Delete', 'icon' => "<i class='fa fa-trash'></i>", 'link' => $this->url->link('micropub/client/deletePost', 'url='.$comm['source_url']));
                        }
                    }
                    if($mpconfig['repost']){
                        $comm['actions']['repost'] = array('title' => 'Repost', 'icon' => "<i class='fa fa-share-square-o'></i>", 'link'=> str_replace('{url}', urlencode($comm['source_url']), $mpconfig['repost']));
                    }
                    if($mpconfig['reply']){
                        $comm['actions']['reply'] = array('title' => 'Reply', 'icon' => "<i class='fa fa-reply'></i>", 'link'=> str_replace('{url}', urlencode($comm['source_url']), $mpconfig['reply']));
                    }
                    if($mpconfig['like']){
                        $comm['actions']['like'] = array('title' => 'Like', 'icon' => "<i class='fa fa-heart'></i>", 'link'=> str_replace('{url}', urlencode($comm['source_url']), $mpconfig['like']));
                    }
                    if($mpconfig['bookmark']){
                        $comm['actions']['bookmark'] = array('title' => 'Bookmark', 'icon' => "<i class='fa fa-bookmark'></i>", 'link'=> str_replace('{url}', urlencode($comm['source_url']), $mpconfig['bookmark']));
                    }
                    $comments[] = $comm;
                }
            }

            $context = $this->model_blog_context->getAllContextForPost($post['post_id']);

            $data['post'] = array_merge($post, array(
                'body_html' => html_entity_decode($post['body']),
                'image_file' => $post['image_file'],
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

            if(strlen($data['post']['body']. $post['permashortcitation']) < 140 ){
                $data['post']['body'] .= "\n\n".$data['post']['permashortcitation'];
                $data['post']['body_html'] .= "<br> <br>".$data['post']['permashortcitation'];
            }
            $data['post']['actions'] = array();


            if($this->session->data['is_owner']){
                if($data['deleted']){
                    $data['post']['actions']['undelete'] =array('title' => 'Undelete', 'icon' => "<i class='fa fa-undo'></i>", 'link' => $this->url->link('micropub/client/undeletePost', 'id='.$post['post_id'],''));
                } else {
                    $data['post']['actions']['edit'] = array('title' => 'Edit', 'icon' => "<i class='fa fa-edit'></i>", 'link' => $this->url->link('micropub/client/editPost', 'id='.$post['post_id'],''));
                    $data['post']['actions']['delete'] = array('title' => 'Delete', 'icon' => "<i class='fa fa-trash'></i>", 'link' => $this->url->link('micropub/client/deletePost', 'id='.$post['post_id'],''));
                }
            }

            if($mpconfig['repost']){
                $data['post']['actions']['repost'] = array('title' => 'Repost', 'icon' => "<i class='fa fa-share-square-o'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
            }
            if($mpconfig['reply']){
                $data['post']['actions']['reply'] = array('title' => 'Reply', 'icon' => "<i class='fa fa-reply'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
            }
            if($mpconfig['like']){
                $data['post']['actions']['like'] = array('title' => 'Like', 'icon' => "<i class='fa fa-heart'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
            }
            if($mpconfig['bookmark']){
                $data['post']['actions']['bookmark'] = array('title' => 'Bookmark', 'icon' => "<i class='fa fa-bookmark'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
            }


            $title = strip_tags($data['post']['title']);
            $body = strip_tags($data['post']['body_html']);
            $short_title = (strlen(html_entity_decode($title)) > 30 ? htmlentities(substr(html_entity_decode($title), 0, 27). '...') : $title);
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

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/video.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/video.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/blog/video.tpl', $data));
            }
        } // end else not deleted
	}

	public function latest() {

		$this->document->setTitle('Latest Videos');
		$data['title'] = 'Latest Videos';

		$this->document->setDescription($this->config->get('config_meta_description'));

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/author');
		$this->load->model('blog/post');
		$this->load->model('blog/interaction');
		$this->load->model('blog/category');

		$data['posts'] = array();
		
		foreach ($this->model_blog_post->getRecentPostsByType('video') as $post) {
                $categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
                $author = $this->model_blog_author->getAuthor($post['author_id']);
	        $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
	        $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['post_id']);

                $extra_data_array = array(
                    'body_html' => html_entity_decode($post['body']),
                    'author' => $author,
                    'author_image' => '/image/static/icon_128.jpg',
                    'categories' => $categories,
                    'comment_count' => $comment_count,
                    'like_count' => $like_count,
                    'actions' => array());


                if($this->session->data['is_owner']){
                    if($post['deleted'] == 1){
                        $extra_data_array['actions']['undelete'] = array('title' => 'Undelete', 'icon' => "<i class='fa fa-undo'></i>", 'link' => $this->url->link('micropub/client/undeletePost', 'id='.$post['post_id'],''));
                    } else {
                        $extra_data_array['actions']['edit'] = array('title' => 'Edit', 'icon' => "<i class='fa fa-edit'></i>", 'link' => $this->url->link('micropub/client/editPost', 'id='.$post['post_id'],''));
                        $extra_data_array['actions']['delete'] = array('title' => 'Delete', 'icon' => "<i class='fa fa-trash'></i>", 'link' => $this->url->link('micropub/client/deletePost', 'id='.$post['post_id'],''));
                    }
                }
                if($mpconfig['repost']){
                    $extra_data_array['actions']['repost'] = array('title' => 'Repost', 'icon' => "<i class='fa fa-share-square-o'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
                }
                if($mpconfig['reply']){
                    $extra_data_array['actions']['reply'] = array('title' => 'Reply', 'icon' => "<i class='fa fa-reply'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
                }
                if($mpconfig['like']){
                    $extra_data_array['actions']['like'] = array('title' => 'Like', 'icon' => "<i class='fa fa-heart'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
                }
                if($mpconfig['bookmark']){
                    $extra_data_array['actions']['bookmark'] = array('title' => 'Bookmark', 'icon' => "<i class='fa fa-bookmark'></i>", 'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
                }

                $data['posts'][] = array_merge($post, $extra_data_array);
    	}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post_list.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post_list.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/post_list.tpl', $data));
		}
	}

}
?>
