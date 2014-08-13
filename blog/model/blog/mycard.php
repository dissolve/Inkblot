<?php
class ModelBlogMycard extends Model {
	public function getData($user_name) {
		$data = $this->cache->get('mydata.user.'.$user_name);
		if(!$data){
		    $query = $this->db->query("SELECT mydata.* FROM ".DATABASE.".mydata 
							JOIN ".DATABASE.".data_group USING(data_id) 
							JOIN ".DATABASE.".group_friend USING(group_id) 
							JOIN ".DATABASE.".friends USING(friend_id) 
						WHERE friend.name = ".$user_name." 
						ORDER BY sorting DESC");
		    $data = $query->rows;
		    $this->cache->set('mydata.user.'.$user_name, $data);
		}
	
		return $data;
	}

}
?>
