<?php
class ControllerBlogPost extends Controller {
    public function index()
    {
        if ($this->session->data['mp-config']) {
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }

        $this->document->setBodyClass('h-entry');

        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

        $this->load->model('blog/post');

        $post = $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);

        if(!$post){
            $this->response->redirect( $this->url->link('error/not_found'));
        }

        if ($this->session->data['is_owner']) {
            $data['is_owner'] = true;
        }


        // redirect if we don't have the correct URL
        if ($this->request->get['slug'] != $post['slug'] ) {
            $this->response->redirect($post['permalink']);
        }
        if ( explode('/', $this->request->server['REQUEST_URI'])[1] != $post['type'] ) {
            $this->response->redirect($post['permalink']);
        }

        $this->document->setSelfLink($post['permalink']);

        $this->load->model('blog/category');
        $this->load->model('blog/post');
        $this->load->model('blog/interaction');
        $this->load->model('blog/context');


        if (!empty($post['deleted_at']) && !$this->session->data['is_owner']) {
            $data['deleted'] = true;

            $this->document->setTitle('Deleted');
            $this->document->setDescription('Entry Deleted');
            header('HTTP/1.1 410 Gone');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            $data['postbody'] = preg_replace(
                '/\@([a-zA-Z0-9_]{1,15})/',
                '<a href="https://twitter.com/$1">@$1</a>',
                $this->load->controller('common/postbody', $post['id'])
            );

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/deleted')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/deleted', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/common/deleted', $data));
            }


        } else {
            if (!empty($post['deleted_at'])) {
                $data['deleted'] = true;
            }
            $author = array('url' => $this->url->link('') , 'name' => AUTHOR_NAME, 'image' => '/image/static/icon_128.jpg');
            $categories = $this->model_blog_category->getCategoriesForPost($post['id']);
            $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['id']);
            $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['id']);
            $repost_count = $this->model_blog_interaction->getInteractionCountForPost('repost', $post['id']);
            $fetch_comments = $this->model_blog_interaction->getInteractionsForPost('reply', $post['id']);
            $likes = $this->model_blog_interaction->getInteractionsForPost('like', $post['id']);
            $reposts = $this->model_blog_interaction->getInteractionsForPost('repost', $post['id']);
            $mentions = $this->model_blog_interaction->getInteractionsForPost('mention', $post['id']);
            $reacjis = $this->model_blog_interaction->getInteractionsForPost('reacji', $post['id']);

            $comments = array();
            if (!isset($this->session->data['user_site'])) {
                $comments = $fetch_comments;
            } else {
                foreach ($fetch_comments as $comm) {
                    $clean_comm = trim(str_replace(array('http://','https://'), array('',''), $comm['source_url']), '/');
                    $clean_user = trim(str_replace(array('http://','https://'), array('',''), $this->session->data['user_site']), '/');
                    $comm['actions'] = array();
                    if (strpos($clean_comm, $clean_user) === 0) {
                        if ($mpconfig['edit']) {
                            $comm['actions']['edit'] = array(
                                'title' => 'Edit',
                                'icon' => "<i class='fa fa-edit'></i>",
                                'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['edit']));
                        } else {
                            $comm['actions']['delete'] = array(
                                'title' => 'Edit',
                                'icon' => "<i class='fa fa-edit'></i>",
                                'link' => $this->url->link('micropub/client/editPost', 'url=' . $comm['source_url']));
                        }
                        if ($mpconfig['delete']) {
                            $comm['actions']['delete'] = array(
                                'title' => 'Delete',
                                'icon' => "<i class='fa fa-trash'></i>",
                                'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['delete']));
                        } else {
                            $comm['actions']['delete'] = array(
                                'title' => 'Delete',
                                'icon' => "<i class='fa fa-trash'></i>",
                                'link' => $this->url->link('micropub/client/deletePost', 'url=' . $comm['source_url']));
                        }
                    }
                    if ($mpconfig['repost']) {
                        $comm['actions']['repost'] = array(
                            'title' => 'Repost',
                            'icon' => "<i class='fa fa-retweet'></i>",
                            'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['repost']));
                    }
                    if ($mpconfig['reply']) {
                        $comm['actions']['reply'] = array(
                            'title' => 'Reply',
                            'icon' => "<i class='fa fa-reply'></i>",
                            'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['reply']));
                    }
                    if ($mpconfig['like']) {
                        $comm['actions']['like'] = array(
                            'title' => 'Like',
                            'icon' => "<i class='fa fa-heart'></i>",
                            'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['like']));
                    }
                    if ($mpconfig['bookmark']) {
                        $comm['actions']['bookmark'] = array(
                            'title' => 'Bookmark',
                            'icon' => "<i class='fa fa-bookmark'></i>",
                            'link' => str_replace('{url}', urlencode($comm['source_url']), $mpconfig['bookmark']));
                    }
                    $comments[] = $comm;
                }
            }

            $context = $this->model_blog_context->getAllContextForPost($post['id']);
            $reacji_array = array();
            foreach($reacjis as $r) {
                if(!isset($reacji_array[$r['content']])){
                    $reacji_array[$r['content']] = array();
                }
                $reacji_array[$r['content']][] = $r;
            }

            $data['post'] = array_merge($post, array(
                'body_html' => preg_replace(
                    '/\@([a-zA-Z0-9_]{1,15})/',
                    '<a href="https://twitter.com/$1">@$1</a>',
                    html_entity_decode($post['content'])
                ),
                'author' => $author,
                'categories' => $categories,
                'comment_count' => $comment_count,
                'comments' => $comments,
                'mentions' => $mentions,
                'like_count' => $like_count,
                'likes' => $likes,
                'reacjis' => $reacji_array,
                'repost_count' => $repost_count,
                'reposts' => $reposts,
                'context' => $context
                ));

            $data['post']['actions'] = array();


            if ($this->session->data['is_owner']) {
                if (!empty($data['deleted_at'])) {
                    $data['post']['actions']['undelete'] = array(
                        'title' => 'Undelete',
                        'icon' => "<i class='fa fa-undo'></i>",
                        'link' => $this->url->link('micropub/client/undeletePost', 'id=' . $post['id'], ''));
                } else {
                    $data['post']['actions']['edit'] = array(
                        'title' => 'Edit',
                        'icon' => "<i class='fa fa-edit'></i>",
                        'link' => $this->url->link('micropub/client/editPost', 'id=' . $post['id'], ''));
                    $data['post']['actions']['delete'] = array(
                        'title' => 'Delete',
                        'icon' => "<i class='fa fa-trash'></i>",
                        'link' => $this->url->link('micropub/client/deletePost', 'id=' . $post['id'], ''));
                }
            }

            if ($mpconfig['repost']) {
                $data['post']['actions']['repost'] = array(
                    'title' => 'Repost',
                    'icon' => "<i class='fa fa-share-square-o'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['repost']));
            }
            if ($mpconfig['reply']) {
                $data['post']['actions']['reply'] = array(
                    'title' => 'Reply',
                    'icon' => "<i class='fa fa-reply'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['reply']));
            }
            if ($mpconfig['like']) {
                $data['post']['actions']['like'] = array(
                    'title' => 'Like',
                    'icon' => "<i class='fa fa-heart'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['like']));
            }
            if ($mpconfig['bookmark']) {
                $data['post']['actions']['bookmark'] = array(
                    'title' => 'Bookmark',
                    'icon' => "<i class='fa fa-bookmark'></i>",
                    'link' => str_replace('{url}', urlencode($post['permalink']), $mpconfig['bookmark']));
            }


            $title = strip_tags($data['post']['title']);
            if (empty($title)) {
                $title = SITE_TITLE;
            }
            $body = strip_tags($data['post']['body_html']);
            $short_title = (strlen(html_entity_decode($title)) > 60 ? htmlentities(substr(html_entity_decode($title), 0, 57) . '...') : $title);
            $description = (strlen(html_entity_decode($body)) > 200 ? htmlentities(substr(html_entity_decode($body), 0, 197) . '...') : $body);

            $this->document->setTitle($title);
            $this->document->setDescription($description);

            $this->document->addMeta('twitter:card', 'summary');
            $this->document->addMeta('og:type', 'article');

            $this->document->addMeta('twitter:title', $short_title);
            $this->document->addMeta('og:title', $short_title);

            $this->document->addMeta('twitter:description', $description);
            $this->document->addMeta('og:description', $description);

            $this->document->addMeta('og:url', $data['post']['permalink'] );
            $this->document->addMeta('twitter:url', $data['post']['permalink'] );

            if(isset($data['post']['photo']) && !empty($data['post']['photo'])){

                $image_url = '';
                if(is_array($data['post']['photo'])){
                    $image_url = $data['post']['photo'][0]['url'];
                } else {
                    $image_url = $data['post']['photo']['url'];
                }
                
                if(strpos($image_url, 'http') !== 0 && strpos($image_url, '//') !== 0){
                    $image_url = HTTPS_SERVER . $image_url;
                }

                $this->document->addMeta('twitter:card', 'summary_large_image');
                $this->document->addMeta('twitter:image', $image_url);
                $this->document->addMeta('og:image', $image_url);
            }


            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            //$data['postbody'] = $this->load->controller('common/postbody', $post['id']);
            $data['postbody'] = preg_replace(
                '/\@([a-zA-Z0-9_]{1,15})/',
                '<a href="https://twitter.com/$1">@$1</a>',
                $this->load->controller('common/postbody', $post['id'])
            );

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/blog/post', $data));
            }
        } // end else not deleted
    }

    public function latest()
    {

        $type = $this->request->get['type'];

        $this->document->setTitle('Latest ' . ucfirst($type) . ' Stream');
        $data['title'] = 'Latest ' . ucfirst($type) . ' Stream';

        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setBodyClass('h-feed');

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->load->model('blog/post');
        $this->load->model('blog/interaction');
        $this->load->model('blog/category');

        $data['author'] = array(
            'url' => $this->url->link(''),
            'name' => AUTHOR_NAME,
            'image' => '/image/static/icon_128.jpg'
        );
        $data['posts'] = array();

        foreach ($this->model_blog_post->getRecentPostsByType($type) as $post) {
            $categories = $this->model_blog_category->getCategoriesForPost($post['id']);
            $author = array(
                'url' => $this->url->link(''),
                'name' => AUTHOR_NAME,
                'image' => '/image/static/icon_128.jpg'
            );
            $comment_count = $this->model_blog_interaction->getInteractionCountForPost('reply', $post['id']);
            $like_count = $this->model_blog_interaction->getInteractionCountForPost('like', $post['id']);
            $repost_count = $this->model_blog_interaction->getInteractionCountForPost('repost', $post['id']);


            $extra_data_array = array(
                'body_html' => preg_replace(
                    '/\@([a-zA-Z0-9_]{1,15})/',
                    '<a href="https://twitter.com/$1">@$1</a>',
                    html_entity_decode($post['content'])
                ),
                'author' => $author,
                'categories' => $categories,
                'comment_count' => $comment_count,
                'like_count' => $like_count,
                'repost_count' => $repost_count,
                'actions' => array());

            //$extra_data_array['postbody'] = $this->load->controller('common/postbody', $post['id']);


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

        if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post_list_'.$type)) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post_list_'.$type, $data));
        } elseif (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/blog/post_list')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/blog/post_list', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/blog/post_list', $data));
        }
    }

}
