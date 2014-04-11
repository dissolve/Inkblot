<?php
class ModelBlogLike extends Model {

	public function getGenericLikes($limit=100, $skip=0) {
        $data = $this->cache->get('likes.generic.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id IS NULL ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('likes.generic.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}
	public function getLikesForPost($post_id, $limit=100, $skip=0) {
        $data = $this->cache->get('likes.post.'.$post_id.'.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id." ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('likes.post.'.$post_id.'.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}

	public function getGenericLikeCount() {
        $data = $this->cache->get('likes.generic.count');
        if(!$data){
            $this->log->write("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id IS NULL");
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id IS NULL");
            $data = $query->row['total'];
            $this->cache->set('likes.generic.count', $data);
        }
	
		return $data;
	}
	public function getLikeCountForPost($post_id) {
        $data = $this->cache->get('likes.post.count.'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id);
            $data = $query->row['total'];
            $this->cache->set('likes.post.count.'.$post_id, $data);
        }
	
		return $data;
	}
}

