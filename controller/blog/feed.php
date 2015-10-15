<?php  
class ControllerBlogFeed extends Controller {
	public function index() {

        // how many feed entries to display
        define(FEED_LIMIT, 100);

        $data['title'] = 'Main Feed';
        $short_title = SITE_TITLE . ' | Main Feed';
        $description = "OpenBlog site: ".SITE_TITLE;

		$this->document->setTitle($short_title);
		$this->document->setDescription($description);
        $this->document->setBodyClass('h-feed');

        $this->document->addMeta('twitter:card', 'summary');
        $this->document->addMeta('twitter:title', $short_title);
        $this->document->addMeta('twitter:description', $description);
        $this->document->addMeta('twitter:image', '/image/static/icon_200.jpg');

        $this->document->addMeta('og:type', 'website');
        $this->document->addMeta('og:title', $short_title);
        $this->document->addMeta('og:description', $description);
        $this->document->addMeta('og:image', '/image/static/icon_200.jpg');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('blog/post');
		$this->load->model('blog/category');
		$this->load->model('blog/interaction');

        $data['author'] = array(
            'link' => $this->url->link(''),
            'display_name' => AUTHOR_NAME,
            'image' => '/image/static/icon_128.jpg'
        );
		$data['posts'] = array();

        $skip=0;
        if(isset($this->request->get['skip'])){
            $skip = $this->request->get['skip'];
        }
        if($skip < 0){
            $skip = 0;
        }
        if($this->session->data['mp-config']){
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }


        $data['feed_url'] = $this->url->link('blog/feed');

		//foreach ($this->model_blog_post->getPostsByTypes(['article'], 20, $skip) as $post) {
		foreach ($this->model_blog_post->getRecentPosts(FEED_LIMIT, $skip) as $post) {
            $author = array(
                'link' => $this->url->link(''),
                'display_name' => AUTHOR_NAME,
                'image' => '/image/static/icon_128.jpg'
            );
			$categories = $this->model_blog_category->getCategoriesForPost($post['post_id']);
			$comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['post_id']);
			$like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['post_id']);

            $extra_data_array = array(
                'body_html' => preg_replace(
                    '/\@([a-zA-Z0-9_]{1,15})/',
                    '<a href="https://twitter.com/$1">@$1</a>',
                     html_entity_decode( isset($post['excerpt']) ? $post['excerpt'] : $post['body']) ),
			    'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
			    'categories' => $categories,
			    'comment_count' => $comment_count,
                'like_count' => $like_count,
                'actions' => array());
            if(isset($post['excerpt'])){
			    $extra_data_array['excerpt_html'] = html_entity_decode($post['excerpt']);
            }
            if(isset($post['following_id']) && !empty($post['following_id'])){
                $this->load->model('contacts/following');
			    $extra_data_array['following'] = $this->model_contacts_following->getFollowing($post['following_id']);
            }

            $this->data['is_owner'] = $this->session->data['is_owner'];
            if($this->session->data['is_owner']){
                if($post['deleted'] == 1){
                    $extra_data_array['actions']['undelete'] = array(
                        'title' => 'Undelete', 
                        'icon'  => "<i class='fa fa-undo'></i>", 
                        'link'  => $this->url->link('micropub/client/undeletePost', 'id='.$post['post_id'],'')
                    );
                } else {
                    $extra_data_array['actions']['edit'] = array(
                        'title' => 'Edit', 
                        'icon'  => "<i class='fa fa-edit'></i>", 
                        'link'  => $this->url->link('micropub/client/editPost', 'id='.$post['post_id'],''));
                    $extra_data_array['actions']['delete'] = array(
                        'title' => 'Delete', 
                        'icon' => "<i class='fa fa-trash'></i>", 
                        'link' => $this->url->link('micropub/client/deletePost', 'id='.$post['post_id'],''));
                }
            }
            if($mpconfig['repost']){
                $extra_data_array['actions']['repost'] = array(
                    'title' => 'Repost', 
                    'icon' => "<i class='fa fa-share-square-o'></i>", 
                    'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
            }
            if($mpconfig['reply']){
                $extra_data_array['actions']['reply'] = array(
                    'title' => 'Reply', 
                    'icon' => "<i class='fa fa-reply'></i>", 
                    'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
            }
            if($mpconfig['like']){
                $extra_data_array['actions']['like'] = array(
                    'title' => 'Like', 
                    'icon' => "<i class='fa fa-heart'></i>", 
                    'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
            }
            if($mpconfig['bookmark']){
                $extra_data_array['actions']['bookmark'] = array(
                    'title' => 'Bookmark', 
                    'icon' => "<i class='fa fa-bookmark'></i>", 
                    'link'=> str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
            }

            $data['posts'][] = array_merge($post, $extra_data_array);
		} // end foreach

        if($skip > FEED_LIMIT){
            $data['prev_page'] = $this->url->link('blog/feed', 'skip=' . ($skip - FEED_LIMIT) );
        } elseif($skip > 0){
            $data['prev_page'] = $this->url->link('blog/feed', 'skip=0');
        }
        if(count($data['posts']) == FEED_LIMIT){
            $data['next_page'] = $this->url->link('blog/feed', 'skip=' . ($skip + FEED_LIMIT) );
        }
        

        $data['author_image'] = '/image/static/icon_200.jpg';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/feed.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/feed.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/blog/feed.tpl', $data));
		}
	}
}
?>
