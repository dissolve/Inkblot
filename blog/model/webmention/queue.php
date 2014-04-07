<?php
class ModelWebmentionQueue extends Model {
	public function addEntry($source, $target) {
        $this->db->query("INSERT INTO " . DATABASE . ".webmentions SET source_url='".$this->db->escape($source)."', target_url='".$this->db->escape($target)."', `timestamp` = NOW(), webmention_status_code='202', webmention_status='queued'");
        $id = $this->db->getLastId();
        return $id;
	}

	public function getEntry($id) {
        $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_id = '".(int)$id."'");
        return $res->row;
	}

	public function setCallback($id, $callback_url) {
        $res = $this->db->query("UPDATE " . DATABASE . ".webmentions set callback_url='".$this->db->escape($callback_url)."' WHERE webmention_id = '".(int)$id."'");
        return $res->row;
	}

}
?>
