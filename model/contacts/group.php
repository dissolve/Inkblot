<?php
class ModelContactsGroup extends Model {

    public function addGroup($group_name)
    {

        $this->db->query(
            "INSERT INTO " . DATABASE . ".groups " .
            " SET name='" . $this->db->escape($group_name) . "'"
        );
        return $this->db->getLastId();
    }


    public function addContactToGroup($group_id, $contact_url)
    {
        $this->db->query(
            "INSERT INTO " . DATABASE . ".group_contact " .
            " SET group_id=" . (int)$group_id . ", " .
            " contact_id=" . (int)$contact_id
        );
    }

    public function getGroup($group_id)
    {
        $result = $this->db->query(
            "SELECT * " .
            " FROM " . DATABASE . ".groups " .
            " JOIN " . DATABASE . ".group_contact USING(group_id) " .
            " JOIN " . DATABASE . ".contacts " .
            " WHERE group_id = " . (int)$group_id
        );
        return $result->rows;
    }

    public function getGroups()
    {
        $result = $this->db->query(
            "SELECT * " .
            " FROM " . DATABASE . ".groups"
        );
        return $result->rows;
    }

}
