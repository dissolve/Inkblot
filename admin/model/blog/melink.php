<?php
class ModelBlogMelink extends Model {
	public function getLinks() {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".melinks ORDER BY sorting DESC");
            $data = $query->rows;
	
		return $data;
	}

}
?>
