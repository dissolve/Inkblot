<?php
class ModelContactsFriend extends Model {
	public function addField($field_data) {
		//TODO
		$this->db->query("INSERT INTO " . DATABASE . ".fields SET URL='".$this->db->escape($friend_url)."' LIMIT 1");
		return $this->db->getLastId();
	}
	public function getFields() {
		$results = $this->db->query("SELECT * FROM ".DATABASE.".fields");
		return $results->rows;
	}

}
?>
