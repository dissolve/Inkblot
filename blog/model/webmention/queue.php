<?php
class ModelWebmentionQueue extends Model {
	public function addEntry($source, $target, $vouch=null) {
        //find it this is an update
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE source_url='".$this->db->escape($source)."' AND target_url='".$this->db->escape($target)."'");
        $results = $query->row;
        if(empty($results)){
            $this->db->query("INSERT INTO " . DATABASE . ".webmentions SET source_url='".$this->db->escape($source)."', target_url='".$this->db->escape($target)."', `timestamp` = NOW(), webmention_status_code='202', webmention_status='queued'". ($vouch ? .",vouch_url='".$this->db->escape($vouch_url)."'": ""));
            $id = $this->db->getLastId();
            return $id;
        } else {
            //this is an update or delete
            $this->db->query("UPDATE " . DATABASE . ".webmentions set webmention_status_code='202', webmention_status = 'queued' WHERE webmention_id = '".(int)$results['webmention_id']."'");
            return $results['webmention_id'];

        }
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
