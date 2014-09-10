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

}
?>
