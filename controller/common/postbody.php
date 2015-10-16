<?php
class ControllerCommonPostbody extends Controller {
    public function index($post_id)
    {
        $this->load->model('blog/post');

        $post = $this->model_blog_post->getPost($post_id);

        $post['body_html'] = html_entity_decode($post['body']);


        $data['post'] = $post;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/posttypes/' . $post['post_type'] . '.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/posttypes/' . $post['post_type'] . '.tpl', $data);
        } else {
            return $this->load->view('default/template/posttypes/note.tpl', $data);
        }
    }

}
