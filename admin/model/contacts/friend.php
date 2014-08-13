<?php
class ModelContactsFriend extends Model {
	public function addFriend($friend_url) {
		$this->db->query("INSERT INTO " . DATABASE . ".friends SET URL='".$this->db->escape($friend_url)."' LIMIT 1");
		return $this->db->getLastId();
	}
	public function addDataToFriend($friend_id, $field_name, $field_data) {
		
	}

}
?>
