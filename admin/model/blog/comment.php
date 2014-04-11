<?php
class ModelBlogComment extends Model {

	public function getCommentsForPost($post_id, $limit=10, $skip=0) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".comments WHERE post_id = ".(int)$post_id." AND approved = 1 ORDER BY timestamp ASC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
	
		return $data;
	}

	public function getCommentCountForPost($post_id) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".comments WHERE post_id = ".(int)$post_id ." AND approved = 1");
            $data = $query->row['total'];
	
		return $data;
	}
}

