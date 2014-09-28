<?php
class ModelBlogWmqueue extends Model {

	public function addEntry($post_id) {
        $this->db->query("INSERT INTO " . DATABASE . ".mention_send_queue SET post_id=". (int)$post_id);
	}

    public function getNext() {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".mention_send_queue ORDER BY queue_id DESC LIMIT 1;");
        $data = $query->row;
        if($data['queue_id']){
            $this->db->query("DELETE FROM " . DATABASE . ".mention_send_queue WHERE queue_id = ".(int)$data['queue_id']);

            return $data['post_id'];
        } else {
            return null;
        }
	}
}

