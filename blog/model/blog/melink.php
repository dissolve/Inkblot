<?php
class ModelBlogMelink extends Model {
	public function getLinks() {
        $data = $this->cache->get('melinks');
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".melinks ORDER BY sorting DESC");
            $data = $query->rows;
            $this->cache->set('melinks', $data);
        }
	
		return $data;
	}

}
?>
