<?php
class ControllerCommonHome extends Controller {
    public function index()
    {
        $short_title = SITE_TITLE;
        $description = "OpenBlog site: " . SITE_TITLE;

        $this->document->setTitle($short_title);
        $this->document->setDescription($description);

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


        $data['posts'] = array();

        $skip = 0;
        if (isset($this->request->get['skip'])) {
            $skip = $this->request->get['skip'];
        }
        if ($this->session->data['mp-config']) {
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }

        //foreach ($this->model_blog_post->getPostsByTypes(['article'], 20, $skip) as $post) {
        foreach ($this->model_blog_post->getRecentPosts(30, $skip) as $post) {
            $author = array('link' => $this->url->link('') , 'display_name' => AUTHOR_NAME);
            $categories = $this->model_blog_category->getCategoriesForPost($post['id']);
            $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['id']);
            $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['id']);

            $extra_data_array = array(
                'body_html' => html_entity_decode(isset($post['summary']) && !empty($post['summary']) ? $post['summary'] : $post['content']),
                'author' => $author,
                'author_image' => '/image/static/icon_200.jpg',
                'categories' => $categories,
                'comment_count' => $comment_count,
                'like_count' => $like_count,
                'actions' => array());
            if (isset($post['summary']) && !empty($post['summary'])) {
                $extra_data_array['summary_html'] = html_entity_decode($post['summary']);
            }
            if (isset($post['following_id']) && !empty($post['following_id'])) {
                $this->load->model('contacts/following');
                $extra_data_array['follow'] = $this->model_contacts_following->getFollowing($post['following_id']);
            }

            $this->data['is_owner'] = $this->session->data['is_owner'];
            if ($this->session->data['is_owner']) {
                if (!empty($post['deleted_at'])) {
                    $extra_data_array['actions']['undelete'] = array(
                        'title' => 'Undelete',
                        'icon' => "<i class='fa fa-undo'></i>",
                        'link' => $this->url->link('micropub/client/undeletePost', 'id=' . $post['id'], ''));
                } else {
                    $extra_data_array['actions']['edit'] = array(
                        'title' => 'Edit',
                        'icon' => "<i class='fa fa-edit'></i>",
                        'link' => $this->url->link('micropub/client/editPost', 'id=' . $post['id'], ''));
                    $extra_data_array['actions']['delete'] = array(
                        'title' => 'Delete',
                        'icon' => "<i class='fa fa-trash'></i>",
                        'link' => $this->url->link('micropub/client/deletePost', 'id=' . $post['id'], ''));
                }
            }
            if ($mpconfig['repost']) {
                $extra_data_array['actions']['repost'] = array(
                    'title' => 'Repost',
                    'icon' => "<i class='fa fa-share-square-o'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
            }
            if ($mpconfig['reply']) {
                $extra_data_array['actions']['reply'] = array(
                    'title' => 'Reply',
                    'icon' => "<i class='fa fa-reply'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
            }
            if ($mpconfig['like']) {
                $extra_data_array['actions']['like'] = array(
                    'title' => 'Like',
                    'icon' => "<i class='fa fa-heart'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
            }
            if ($mpconfig['bookmark']) {
                $extra_data_array['actions']['bookmark'] = array(
                    'title' => 'Bookmark',
                    'icon' => "<i class='fa fa-bookmark'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
            }

            $data['posts'][] = array_merge($post, $extra_data_array);
        }

        $data['author_image'] = '/image/static/icon_200.jpg';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/home.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/common/home.tpl', $data));
        }
    }
}
