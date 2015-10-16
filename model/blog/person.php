<?php
class ModelBlogPerson extends Model {


    /**
 * storePerson
     *  @arg data array the array of data describing a person assumed to contain
     *    name -the person's name
     *    url - the url of the person
     *    image - url of the image of the person to display
     *  @return int the person_id of the (possibly) newly created person
     *      returns null if error
     */
    public function storePerson($data)
    {

        if ( !isset($data['url']) && !isset($data['name']) && !isset($data['image']) ) {
            return null;
        }

        //todo: should clean up url??

        $query = $this->db->query(
            "SELECT person_id " .
            " FROM " . DATABASE . ".people " .
            " WHERE `url` = '" . $this->db->escape($data['url']) . "' ;"
        );

        if ($query->row) {
            return $query->row['person_id'];
        }
        //TODO: check for the url being an alternate value


        $this->db->query(
            "INSERT INTO " . DATABASE . ".people " .
            " SET `url`='" . $this->db->escape($data['url']) . "', " .
            " SET `name`='" . $this->db->escape($data['name']) . "', " .
            " SET `image`='" . $this->db->escape($data['image']) . "' "
        );

        $id = $this->db->getLastId();

    }

    public function getPerson($person_id)
    {
        $person = $this->cache->get('person.' . $person_id);
        if (!$person) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".people " .
                " WHERE person_id = " . (int)$person_id
            );
            $person = $query->row;
            $this->cache->set('person.' . $person_id, $person);
        }
        return $post;
    }

}
