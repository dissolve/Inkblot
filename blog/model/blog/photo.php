<?php
class ModelBlogPhoto extends Model {

    public function newPhoto($data){

        $year = date('Y');
        $month = date('n');
        $day = date('j');

        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM ".DATABASE.".posts 
                WHERE `year` = '".$year."'
                    AND `month` = '".$month."' 
                    AND `day` = '".$day."';");

        $newcount = $query->row['newval'];

        $syndication_extra = '';
        if(isset($data['syndication_extra']) && !empty($data['syndication_extra'])){
            $syndication_extra = $this->db->escape($data['syndication_extra']);
        }

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='photo',
            `body` = '".$this->db->escape($data['body'])."',
            `title` = '".$this->db->escape($data['title'])."',
            `syndication_extra` = '".$syndication_extra."',
            `image_file` = '".$this->db->escape($data['image_file'])."',
            `author_id` = 1,
            `timestamp` = NOW(),
            `year` = '".$year."',
            `month` = '".$month."',
            `day` = '".$day."',
            `daycount` = ".$newcount .
            (isset($data['replyto']) && !empty($data['replyto']) ? ", replyto='".$this->db->escape($data['replyto'])."'" : "");

        $query = $this->db->query($sql);

        $id = $this->db->getLastId();
        
        return $id;
	
    }

    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full photos

	public function getPhoto($photo_id) {
        $this->load->model('blog/post');
        $photo = $this->model_blog_post->getPost($photo_id);
        $photo['photo_id'] = $photo['post_id'];
        return $photo;
	}

    public function getByData($data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getPhotoByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }

	public function getByDayCount($year,$month, $day, $daycount) {
	    return $this->getPhotoByDayCount($year,$month, $day, $daycount);
    }
	public function getPhotoByDayCount($year,$month, $day, $daycount) {
        $photo_id = $this->cache->get('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$photo_id){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='photo' AND year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $photo_id = $query->row['post_id'];
            $this->cache->set('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $photo_id);
        }

		return $this->getPhoto($photo_id);
	}

	public function getRecentPhotos($limit=10, $skip=0) {
        $data = $this->cache->get('photos.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='photo' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $photo){
                $data_array[] = $this->getPhoto($photo['post_id']);
            }
            $this->cache->set('photos.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPhotosByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('photos.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='photo' AND category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $photo){
                $data_array[] = $this->getPhoto($photo['post_id']);
            }
            $this->cache->set('photos.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPhotosByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('photos.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='photo' AND author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $photo){
                $data_array[] = $this->getPhoto($photo['post_id']);
            }
            $this->cache->set('photos.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPhotosByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('photos.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='photo' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $photo){
                $data_array[] = $this->getPhoto($photo['post_id']);
            }
            $this->cache->set('photos.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}


    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null){
            $this->load->model('blog/post');
            $this->model_blog_post->addWebmention($data, $webmention_id, $comment_data, $post_id);
    }
    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null){
            $this->load->model('blog/post');
            $this->model_blog_post->editWebmention($data, $webmention_id, $comment_data, $post_id);
    }

}
