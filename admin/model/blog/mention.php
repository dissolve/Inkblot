<?php
class ModelBlogMention extends Model {

	public function getRecentMentions($limit=10, $skip=0) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE approved != 0 ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
	
		return $data;
	}
	public function getMentionsForPost($post_id, $limit=10, $skip=0) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE approved != 0 AND post_id = ".(int)$post_id." ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
	
		return $data;
	}
	public function getMentionCountForPost($post_id) {
            $query = $this->db->query("SELECT COUNT(*) as total FROM " . DATABASE . ".mentions WHERE approved != 0 AND post_id = ".(int)$post_id);
            $data = $query->row['total'];
	
		return $data;
	}
}

