<?php
class ModelStorageLogs extends Model {

    public function getFeedList() {
        $query = $this->db->query("SELECT DISTINCT feed_url FROM " . DATABASE . ".logs ");

        return $query->rows;
    }

    public function getFeed($feed_url, $limit = 100, $skip=0){

        $query = $this->db->query("SELECT * FROM " . DATABASE . ".logs WHERE feed_url = '". $this->db->escape($feed_url) ."' ORDER BY published DESC  LIMIT ". (int)$skip .",".(int)$limit);

        return $query->rows;
    }
    public function addLogEntry($feed_url, $message, $published, $author_name = '', $author_url = ''){
        $author_id = NULL;
        //todo if author_url set, look up and possibly create author record
        //
        $query = $this->db->query("INSERT INTO " . DATABASE . ".logs SET 
                feed_url = '". $this->db->escape($feed_url) ."',
                message = '". $this->db->escape($message) ."',
                published = '". $this->db->escape($published) ."',
                author_name = '". $this->db->escape($author_name) ."',
                author_id = ". $this->db->escape($author_id) );
            

    }

}

