<?php
class ModelWebmentionNotification extends Model {
    public function addEntry($endpoint, $subscription_id)
    {

        $query = $this->db->query("SELECT * FROM " . DATABASE . ".notifications WHERE subscription_id='" . $this->db->escape($subscription_id) . "'");
        $results = $query->row;
        if (empty($results)) {
            $this->db->query("INSERT INTO " . DATABASE . ".notifications SET 
                    endpoint='" . $this->db->escape($endpoint) . "', 
                    subscription_id='" . $this->db->escape($subscription_id) . "'");
        }
    }
    public function deleteEntry($subscription_id)
    {
        $this->db->query("DELETE FROM " . DATABASE . ".notifications WHERE subscription_id='" . $this->db->escape($subscription_id) . "'");
    }

    public function getEntries()
    {

        $query = $this->db->query("SELECT * FROM " . DATABASE . ".notifications");
        return $query->rows;
    }
}
