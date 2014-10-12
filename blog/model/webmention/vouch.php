<?php
class ModelWebmentionVouch extends Model {
    public function recordReferer($referer){

        //start out with some basic tests to make sure we have something useful
        if(!isset($referer)){
            return;
        }
        $referer = trim($referer);
        if(empty($referer) || $referer == '-'){
            return;
        }

        $this->db->query("INSERT INTO " . DATABASE . ".referer_receive_queue SET url='".$this->db->escape($referer)."'");
    }

    public function processReferers(){
        $this->db->query("SELECT * FROM " . DATABASE . ".referer_receive_queue limit 1;";
        //parse out the domain to store this under
        $site_no_protocol = str_replace(array('http://', 'https://'),array('',''), $referer);
        $domain = preg_replace('/[#\?\/].*/','',$site_no_protocol);

        //look for existing record in DB for this domain

        //do i have 2 possible vouches
        
        //if not, curl site, find url, check if set no-follow
        
        //if valid store to DB
    }



}
?>
