<?php
class ModelBlogPerson extends Model {


    /**
     *  Save a person or recall an existing one
     *  @arg Data array the array of data describing a person assumed to contain
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
        $this->cache->delete('people');

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

            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".people_alternate_urls " .
                " WHERE person_id = " . (int)$person_id .
                " ORDER BY url" 
            );
            $person['alternates'] = $query->rows;

            $this->cache->set('person.' . $person_id, $person);
        }
        return $person;
    }

    public function getPeople()
    {
        $people = $this->cache->get('people');
        if (!$person) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".people " .
                " ORDER BY name" 
            );
            $people = $query->rows;
            foreach($people as &$person){
                $query = $this->db->query(
                    "SELECT * " .
                    " FROM " . DATABASE . ".people_alternate_urls " .
                    " WHERE person_id = " . (int)$person['person_id'] .
                    " ORDER BY url" 
                );
                $person['alternates'] = $query->rows;
            }
            $this->cache->set('people' , $people);
        }
        return $person;
    }

    public function joinPeople($main_person_id, $alternate_person_id)
    {
        $alt_person = $this->getPerson($alternate_person_id);
        $this->addAlternateUrl($main_person_id, $alt_person['url']);

        foreach($alt_person['alternates'] as $alt){
            $this->addAlternateUrl($main_person_id, $alt['url']);
        }

        //TODO update people in interactions, and interactions_second_level

        $this->deletePerson($alternate_person_id);

        $this->cache->delete('people');
    }

    public function addAlternateUrl($person_id, $alternate_url)
    {
        $this->db->query(
            "INSERT INTO " . DATABASE . ".people_alterate_urls " .
            " SET person_id = " . (int)$person_id . ", "
            " url = '".$this->db->escape($alterate_url)."' "
        );
        $this->cache->delete('person.' . $person_id);
    }

    private function deletePerson($person_id)
    {
        //TODO check if this person is associated to anything first before delete
        $this->db->query(
            "DELETE FROM " . DATABASE . ".people_alterate_urls " .
            " WHERE person_id = " . (int)$person_id 
        );
        $this->db->query(
            "DELETE FROM " . DATABASE . ".people " .
            " WHERE person_id = " . (int)$person_id 
        );
        $this->cache->delete('person.' . $person_id);
        return true;
    }


}
