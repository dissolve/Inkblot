<?php
class ModelBlogMycard extends Model {
	public function getData($friend_site = NULL) {
        
        $friends = '0';
        if($friend_site){
            $friend_site = str_replace('http://','', $friend_site);
            $friend_site = str_replace('https://','', $friend_site);
            
            $query = $this->db->query("SELECT friend_id from ".DATABASE.".friends 
                                        WHERE main_url='".$this->db->escape($friend_site)."'
                                        OR main_url='http://".$this->db->escape($friend_site)."'
                                        OR main_url='https://".$this->db->escape($friend_site)."'");
            $friend_id = $query->row['friend_id'];
            $friends = $friends . ','.$friend_id;
        }

		$data = $this->cache->get('mydata.user.'.$friends);
		if(!$data){
            $query = $this->db->query("SELECT mydata.*, field_types.* 
                            FROM ".DATABASE.".friend_group 
							JOIN ".DATABASE.".mydata_group USING(group_id) 
							JOIN ".DATABASE.".mydata USING(data_id) 
							JOIN ".DATABASE.".field_types USING(field_type_id) 
						WHERE friend_id IN (".$friends.") 
						ORDER BY mydata.sorting ASC");
		    $data = $query->rows;
		    $this->cache->set('mydata.user.'.$friends, $data);
		}
	
		return $data;
	}

}
?>
