<?php
class ModelBlogShortener extends Model {
	public function getByData($data) {
        if(isset($data['eid'])){
            $this->load->model('blog/post');
            $id = $this->model_blog_post->sxg_to_num($data['eid']);
            return $this->model_blog_post->getPost($id);
        } else {
            return null;
        }
	}
    
    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null){
            $this->load->model('blog/post');
            $id = $this->model_blog_post->sxg_to_num($data['eid']);
            $this->model_blog_post->addWebmention($data, $webmention_id, $comment_data, $id);
    }
    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null){
            $this->load->model('blog/post');
            $id = $this->model_blog_post->sxg_to_num($data['eid']);
            $this->model_blog_post->editWebmention($data, $webmention_id, $comment_data, $id);
    }

}
?>
