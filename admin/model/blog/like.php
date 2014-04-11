<?php
class ModelBlogLike extends Model {

	public function getGenericLikes($limit=100, $skip=0) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id IS NULL ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
	
		return $data;
	}
	public function getLikesForPost($post_id, $limit=100, $skip=0) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id." ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
	
		return $data;
	}

	public function getGenericLikeCount() {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id IS NULL");
            $data = $query->row['total'];
	
		return $data;
	}
	public function getLikeCountForPost($post_id) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id);
            $data = $query->row['total'];
	
		return $data;
	}
}

