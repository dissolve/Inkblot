<?php
class ModelCommonFixroute extends Model {

    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null)
    {
            $this->load->model('blog/interaction');
            $this->model_blog_interaction->addWebmention($data, $webmention_id, $comment_data, $post_id);
    }
    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null)
    {
            $this->load->model('blog/interaction');
            $this->model_blog_interaction->editWebmention($data, $webmention_id, $comment_data, $post_id);
    }

}
