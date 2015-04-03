<?php
class ModelWebmentionQueue extends Model {
	public function addEntry($source, $target, $vouch=null) {
        //$target = rtrim($target, '/') . '/';  // ALWAYS USER A TRAILING / 
        //find it this is an update
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE source_url='".$this->db->escape($source)."' AND target_url='".$this->db->escape($target)."'");
        $results = $query->row;
        if(empty($results)){
            $this->db->query("INSERT INTO " . DATABASE . ".webmentions SET source_url='".$this->db->escape($source)."', target_url='".$this->db->escape($target)."', `timestamp` = NOW(), webmention_status_code='202', webmention_status='queued'". ($vouch ? ", vouch_url='".$this->db->escape($vouch)."'": ""));
            $id = $this->db->getLastId();
            return $id;
        } else {
            //this is an update or delete
            $this->db->query("UPDATE " . DATABASE . ".webmentions set webmention_status_code='202', webmention_status = 'queued'". ($vouch ? ", vouch_url='".$this->db->escape($vouch)."'": "")." WHERE webmention_id = '".(int)$results['webmention_id']."'");
            return $results['webmention_id'];

        }
	}

	public function addUnvouchedEntry($source, $target) {
        //$target = rtrim($target, '/') . '/';  // ALWAYS USER A TRAILING / 
        //find it this is an update
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE source_url='".$this->db->escape($source)."' AND target_url='".$this->db->escape($target)."'");
        $results = $query->row;
        if(empty($results)){
            $this->db->query("INSERT INTO " . DATABASE . ".webmentions SET source_url='".$this->db->escape($source)."', target_url='".$this->db->escape($target)."', `timestamp` = NOW(), webmention_status_code='449', webmention_status='queued'");
            $id = $this->db->getLastId();
            return $id;

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

    public function getUnhandledWebmentions(){
        $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_status_code != 200 AND (admin_status != 'dismiss' OR admin_status is NULL)");
        return $res->rows;
    }

    public function dismiss($id){
        $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_id = '".(int)$id."'");
        if($res->row['webmention_status_code'] != 200){
            $this->db->query("UPDATE " . DATABASE . ".webmentions SET admin_status = 'dismiss' WHERE webmention_id = ".(int)$id);
        }
    }
    public function retry($id){
        $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_id = '".(int)$id."'");
        if($res->row['webmention_status_code'] != 200){
            $this->db->query("UPDATE " . DATABASE . ".webmentions SET webmention_status_code = 202, webmention_status='retry' WHERE webmention_id = ".(int)$id);
        }
    }
    public function whitelistAndRetry($id){
        $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_id = '".(int)$id."'");
        if($res->row['webmention_status_code'] != 200){
            $res = $this->db->query("SELECT * FROM " . DATABASE . ".webmentions WHERE webmention_id = '".(int)$id."'");

            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($res->row['source_url']);

            $this->db->query("UPDATE " . DATABASE . ".webmentions SET webmention_status_code = 202, webmention_status='retry' WHERE webmention_id = ".(int)$id);
        }
    }

}
?>
