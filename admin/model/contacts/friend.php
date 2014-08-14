<?php
class ModelContactsFriend extends Model {
	public function addFriend($friend_url) {
		$this->db->query("INSERT INTO " . DATABASE . ".friends SET main_url='".$this->db->escape($friend_url)."' LIMIT 1");
		return $this->db->getLastId();
	}

	public function getFriend($friend_id) {
		$results = $this->db->query("SELECT * FROM friends WHERE friend_id = ".(int)$friend_id);
		return $results->row;
	}

	public function getFriendGroups($friend_id) {
		$results = $this->db->query("SELECT groups.* FROM groups JOIN frined_group USING(group_id) WHERE friend_id = ".(int)$friend_id);
		return $results->rows;
	}

	public function getFriendData($friend_id) {
		$results = $this->db->query("SELECT * FROM friend_field JOIN field_types USING(field_type_id) WHERE friend_id = ".(int)$friend_id);
		return $results->rows;
	}
	
	public function getFriendIdByUrl($friend_url) {
		$friend_url = str_replace('http://','', $friend_url);
		$friend_url = str_replace('https://','', $friend_url);

		$query = $this->db->query("SELECT friend_id from ".DATABASE.".friends 
					WHERE main_url='".$this->db->escape($friend_url)."'
                                        OR main_url='http://".$this->db->escape($friend_url)."'
                                        OR main_url='https://".$this->db->escape($friend_url)."'");
		return $query->row['friend_id'];
	}

	public function addDataToFriend($friend_id, $field_type_id, $field_value) {
		$this->db->query("INSERT INTO friend_field 
					SET friend_id = ".(int)$friend_id.",
					    field_type_id = ".(int)$field_type_id.",
					    value='".$this->db->query($field_value)."'");
	}

}
?>
