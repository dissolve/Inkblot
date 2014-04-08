<?php
class ModelBlogMention extends Model {

	public function getRecentMentions($limit=10, $skip=0) {
        $data = $this->cache->get('mentions.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".mentions WHERE approved != 0 ORDER BY parse_timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('mentions.recent.'. $skip . '.' .$limit, $data);
        }
	
		return $data;
	}
}

