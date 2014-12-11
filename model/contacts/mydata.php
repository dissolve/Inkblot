<?php
class ModelContactsMydata extends Model {

    public function getAllData(){
        $query = $this->db->query("SELECT * FROM mydata JOIN field_types USING(field_type_id)");
        return $query->rows;    
    }

    public function addData($type_id, $data) {
        $this->db->query("INSERT INTO " . DATABASE . ".mydata
            SET field_type_id=".(int)$type_id.",
            ".(isset($data['sorting']) ? "sorting=".(int)$data['sorting'])."," : "")."
            ".(isset($data['title']) ? "title='".$this->db->escape($data['title'])."'," : "")."
            ".(isset($data['rel']) ? "rel='".$this->db->escape($data['rel'])."'," : "")."
            ".(isset($data['target']) ? "target='".$this->db->escape($data['target'])."'," : "")."
	    value='".$this->db->escape($data['value'])."'");

        return $this->db->getLastId();
    }

    public function deleteData($mydata_id){
        $this->db->query("DELETE FROM mydata WHERE data_id = ".(int)$mydata_id);
    }

    public function getDataGroups($data_id){
        $query = $this->db->query("SELECT * FROM mydata_group JOIN groups USING(group_id) WHERE data_id=".(int)$data_id);
        return $query->rows;
    }


    public function addDataGroup($data_id, $group_id) {
        $this->db->query("INSERT INTO mydata_group SET data_id = ".(int)$data_id.", group_id=".(int)$group_id);
    }

    public function removeDataGroup($data_id, $group_id) {
        $this->db->query("DELETE FROM mydata_group WHERE data_id = ".(int)$data_id." AND group_id=".(int)$group_id);
    }

}
?>
