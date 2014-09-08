<?php
class ModelBlogMycard extends Model {
	public function getData($contact_site = NULL, $classification='all') {
        
        $contacts = '0';
        if($contact_site){
            $contact_site = str_replace('http://','', $contact_site);
            $contact_site = str_replace('https://','', $contact_site);
            
            $query = $this->db->query("SELECT contact_id from ".DATABASE.".contacts 
                                        WHERE main_url='".$this->db->escape($contact_site)."'
                                        OR main_url='http://".$this->db->escape($contact_site)."'
                                        OR main_url='https://".$this->db->escape($contact_site)."'");
            $contact_id = $query->row['contact_id'];
            $contacts = $contacts . ','.(int)$contact_id;
        }

		$data = $this->cache->get('mydata.user.'.$classification.'.'.$contacts);
		if(!$data){
            $query = $this->db->query("SELECT mydata.*, field_types.* 
                            FROM ".DATABASE.".contact_group 
							JOIN ".DATABASE.".mydata_group USING(group_id) 
							JOIN ".DATABASE.".mydata USING(data_id) 
							JOIN ".DATABASE.".field_types USING(field_type_id) 
						WHERE contact_id IN (".$contacts.") 
                        ".($classification == "all" ? "" :" AND classification = '".$this->db->escape($classification)."' ")."
						ORDER BY mydata.sorting ASC");
		    $data = $query->rows;
		    $this->cache->set('mydata.user.'.$classification.'.'.$contacts, $data);
		}
	
		return $data;
	}

}
?>
