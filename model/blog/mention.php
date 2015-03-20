<?php
class ModelBlogMention extends Model {

	public function getRecentMentions($limit=10, $skip=0) {
        $data = $this->cache->get('mentions.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE is_tag=0 AND approved != 0 ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('mentions.recent.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}
	public function getRecentTags($limit=10, $skip=0) {
        $data = $this->cache->get('mentions.tags.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE is_tag=1 AND approved != 0 ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('mentions.tags.recent.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}
	public function getMentionsForPost($post_id, $limit=10, $skip=0) {
        $data = $this->cache->get('mentions.post.'.$post_id.'.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE approved != 0 AND post_id = ".(int)$post_id." ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('mentions.post.'.$post_id.'.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}
}

