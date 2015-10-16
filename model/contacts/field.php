<?php
class ModelContactsFieldType extends Model {
    public function addFieldType($data)
    {
        if (isset($data['label'])) {
            $is_link = 1;
            if (isset($data['is_link'])) {
                $is_link = $data['is_link'];
            }

            //TODO
            $this->db->query("INSERT INTO " . DATABASE . ".field_type 
					SET field_label='" . $this->db->escape($data['label']) . "',
				    " . (isset($data['image']) ? "field_display_image='" . $this->db->escape($data['image']) . "'," : "") . "   
				    " . (isset($data['classes']) ? "classes='" . $this->db->escape($data['classes']) . "'," : "") . "   
				    link_format=" . (isset($data['link_format']) ? "'" . $this->db->escape($data['link_format']) . "'" : "'{}'") . ",   
					is_link=" . (int)$is_link);
            return $this->db->getLastId();
        } else {
            return null;
        }
    }
    public function getFieldTypes()
    {
        $results = $this->db->query("SELECT * FROM " . DATABASE . ".field_types");
        return $results->rows;
    }

}
