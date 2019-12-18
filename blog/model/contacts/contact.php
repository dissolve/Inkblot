<?php
class ModelContactsContact extends Model {
    //todo remove "Main url" from the main contact structure.  not all will have a single main url
    public function addContact($name, $contact_url)
    {
        $this->db->query(
            "INSERT INTO " . DB_DATABASE . ".contacts " .
            " SET name='" . $this->db->escape($name) . "', " .
            " main_url='" . $this->db->escape($contact_url) . "' " .
            " LIMIT 1"
        );
        return $this->db->getLastId();
    }

    public function getContact($contact_id)
    {
        $results = $this->db->query("SELECT * " .
            " FROM " . DB_DATABASE . ".contacts " .
            " WHERE contact_id = " . (int)$contact_id);
        return $results->row;
    }

    public function getContactGroups($contact_id)
    {
        $results = $this->db->query(
            "SELECT groups.* " .
            " FROM " . DB_DATABASE . ".groups " .
            " JOIN " . DB_DATABASE . ".frined_group USING(group_id) " .
            " WHERE contact_id = " . (int)$contact_id
        );
        return $results->rows;
    }

    public function getContactData($contact_id)
    {
        $results = $this->db->query(
            "SELECT * " .
            " FROM " . DB_DATABASE . ".contact_field " .
            " JOIN " . DB_DATABASE . ".field_types USING(field_type_id) " .
            " WHERE contact_id = " . (int)$contact_id
        );
        return $results->rows;
    }

    public function getContactIdByUrl($contact_url)
    {
        $contact_url = str_replace('http://', '', $contact_url);
        $contact_url = str_replace('https://', '', $contact_url);

        $query = $this->db->query(
            "SELECT contact_id " .
            " FROM " . DB_DATABASE . ".contacts " .
            " WHERE main_url='" . $this->db->escape($contact_url) . "'" .
            " OR main_url='http://" . $this->db->escape($contact_url) . "'" .
            " OR main_url='https://" . $this->db->escape($contact_url) . "'"
        );
        return $query->row['contact_id'];
    }

    public function addDataToContact($contact_id, $field_type_id, $field_value)
    {
        $this->db->query(
            "INSERT INTO " . DB_DATABASE . ".contact_field " .
            " SET contact_id = " . (int)$contact_id . ", " .
            " field_type_id = " . (int)$field_type_id . ", " .
            " value='" . $this->db->escape($field_value) . "'"
        );
    }


    public function getTotalContacts()
    {
        $query = $this->db->query(
            "SELECT count(contact_id) as total " .
            " FROM " . DB_DATABASE . ".contacts"
        );
        return $query->rows['total'];
    }

    public function getContacts($limit = 20, $skip = 0)
    {

        $query = $this->db->query(
            "SELECT contact_id " .
            " FROM " . DB_DATABASE . ".contacts " .
            " WHERE contact_id != 0 " .
            " LIMIT " . (int)$skip . ", " . (int)$limit
        );
        $data = $query->rows;
        $data_array = array();
        foreach ($data as $contact) {
            $data_array[] = $this->getContact($contact['contact_id']);
        }
        return $data_array;
    }
}
