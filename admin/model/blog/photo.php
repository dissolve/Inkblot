<?php
require_once(DIR_APPLICATION . 'controller/helper/seo_url.php');

class ModelBlogPhoto extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full photos

	public function getPhoto($post_id) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE `post_type`='photo' AND post_id = '". (int)$post_id . "'");
        $post = $query->row;

        $post['photo_id'] = $post['post_id'];
        $post['permalink'] = preg_replace( '`/admin/`', '/',
            rewrite_url($this->url->link('blog/photo', 'year='.$post['year']. '&' . 
                                        'month='.$post['month']. '&' . 
                                        'day='.$post['day']. '&' . 
                                        'daycount='.$post['daycount']. '&' . 
                                        'slug=' . $post['slug'], '')), 1);

		return $post;
	}

	public function getPhotoByDayCount($year,$month, $day, $daycount) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE `post_type`='photo' AND year = '". (int)$year . "' AND
                                                                              month = '". (int)$month . "' AND
                                                                              day = '". (int)$day . "' AND
                                                                              daycount = '". (int)$daycount . "'");
        $post = $query->row;

        $post['photo_id'] = $post['post_id'];
        $post['permalink'] = preg_replace( '`/admin/`', '/',
            rewrite_url($this->url->link('blog/photo', 'year='.$post['year']. '&' . 
                                        'month='.$post['month']. '&' . 
                                        'day='.$post['day']. '&' . 
                                        'daycount='.$post['daycount']. '&' . 
                                        'slug=' . $post['slug'], '')), 1);

		return $post;
	}

	public function getRecentPhotos($limit=10, $skip=0) {
        $data_array = array();
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='photo' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        foreach($data as $post){
            $data_array[] = $this->getPhoto($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPhotosByCategory($category_id, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE `post_type`='photo' AND category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPhoto($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPhotosByAuthor($author_id, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='photo' AND author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPhoto($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPhotosByArchive($year, $month, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='photo' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPhoto($post['post_id']);
        }
	
		return $data_array;
	}

    public function getTotalPhotos(){
        $query = $this->db->query("SELECT count(post_id) as total FROM " . DATABASE . ".posts WHERE `post_type`='photo'");
        return $query->rows['total'];
    }

    public function getPhotos($sort='post_id', $order='DESC', $limit=20, $skip=0){
        if ($sort == 'photo_id'){
            $sort = 'post_id';
        }

        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='photo'
            ORDER BY ".$this->db->escape($sort)." ".$this->db->escape($order)."
            LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPhoto($post['post_id']);
        }
		return $data_array;
    }

    public function addPhoto($data){

        $year = date('Y');
        $month = date('n');
        $day = date('j');

        $post = $data['photo'];

        if(!isset($data['slug']) || empty($data['slug'])){
            $data['slug'] = '_';
        }

        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM ".DATABASE.".posts 
                WHERE `year` = '".$year."'
                    AND `month` = '".$month."' 
                    AND `day` = '".$day."';");

        $newcount = $query->row['newval'];

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='photo',
            `body` = '".$this->db->escape($post['body'])."',
            `title` = '".$this->db->escape($post['title'])."',
            `slug` = '".$this->db->escape($post['slug'])."',
            `author_id` = 1,
            `timestamp` = NOW(),
            `year` = '".$year."',
            `month` = '".$month."',
            `day` = '".$day."',
            `daycount` = ".$newcount .
            (isset($post['replyto']) && !empty($post['replyto']) ? ", replyto='".$this->db->escape($post['replyto'])."'" : "");

        $query = $this->db->query($sql);

        $id = $this->db->getLastId();
	
		return $id;
    }

    public function editPhoto($post_id, $data){

        $post = $data['photo'];

        $sql = "UPDATE " . DATABASE . ".posts SET 
            `body` = '".$this->db->escape($post['body'])."',
            `title` = '".$this->db->escape($post['title'])."',
            `slug` = '".$this->db->escape($post['slug'])."', 
            `replyto` = ". (isset($post['replyto']) && !empty($post['replyto']) ? "'".$this->db->escape($post['replyto'])."'" : "NULL") .
            " WHERE post_id = " . (int)$post_id;

        $this->db->query($sql);
    }


}
