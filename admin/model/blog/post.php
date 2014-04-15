<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full posts

	public function getPost($post_id) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = '". (int)$post_id . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/post', 'id='.$post['post_id'], '')));
		return $post;
	}

	public function getPostByDayCount($year,$month, $day, $daycount) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/post', 'id='.$post['post_id'], '')));
		return $post;
	}

	public function getRecentPosts($limit=10, $skip=0) {
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
	
		return $data_array;
	}

	public function getPostsByCategory($category_id, $limit=20, $skip=0) {
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
	
		return $data_array;
	}

	public function getPostsByAuthor($author_id, $limit=20, $skip=0) {
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
	
		return $data_array;
	}

	public function getPostsByArchive($year, $month, $limit=20, $skip=0) {
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
	
		return $data_array;
	}

    public function addPost($data){

        $year = date('Y');
        $month = date('n');
        $day = date('j');

        $post = $data['post'];

        $this->log->write(print_r($data,true));


        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM ".DATABASE.".posts 
                WHERE `year` = '".$year."'
                    AND `month` = '".$month."' 
                    AND `day` = '".$day."';");

        $newcount = $query->row['newval'];

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='post',
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
        $this->log->write($sql);
        $query = $this->db->query($sql);
        $this->log->write(print_r($query,true));

        $id = $this->db->getLastId();
	
		return $id;
    }



}
