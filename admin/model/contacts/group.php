<?php
class ModelContactsGroup extends Model {

	public function addGroup($group_name){
		
		$this->db->query("INSERT INTO " . DATABASE . ".groups SET name='".$this->db->escape($group_name)."'");
		return $this->db->getLastId();
	}


	public function addFriendToGroup($group_id, $friend_url) {
		$this->db->query("INSERT INTO " . DATABASE . ".group_friend SET group_id=".(int)$group_id.", friend_id=".(int)$friend_id);
	}

	public function getGroup($group_id){
		$result = $this->db->query("SELECT * FROM " . DATABASE . ".groups JOIN ".DATABASE.".group_friend USING(group_id) JOIN ".DATABASE.".friends WHERE group_id = ".(int)$group_id);
		return $result->rows;
	}

	public function getGroups(){
		$result = $this->db->query("SELECT * FROM " . DATABASE . ".groups");
		return $result->rows;
	}

}
?>
