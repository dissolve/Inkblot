<?php
class ModelBlogComment extends Model {

	public function getCommentsForPost($post_id, $limit=10, $skip=0) {
        $data = $this->cache->get('comments.post.'.$post_id.'.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT comments.*, webmentions.vouch_url FROM " . DATABASE . ".comments LEFT JOIN ".DATABASE.".webmentions ON comments.comment_id = webmentions.resulting_comment_id WHERE post_id = ".(int)$post_id." AND approved = 1 ORDER BY timestamp ASC LIMIT ". (int)$skip . ", " . (int)$limit);

            $data = $query->rows;
            $this->cache->set('comments.post.'.$post_id.'.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}

	public function getCommentCountForPost($post_id) {
        $data = $this->cache->get('comments.post.count.'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".comments WHERE post_id = ".(int)$post_id ." AND approved = 1");
            $data = $query->row['total'];
            $this->cache->set('comments.post.count.'.$post_id, $data);
        }
	
		return $data;
	}
}

