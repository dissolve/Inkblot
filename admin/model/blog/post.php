<?php
require_once(DIR_APPLICATION . 'controller/helper/seo_url.php');

class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full posts

	public function getPost($post_id) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE `post_type`='article' AND post_id = '". (int)$post_id . "'");
        $post = $query->row;

        $post['permalink'] = preg_replace( '`/admin/`', '/',
            rewrite_url($this->url->link('blog/post', 'year='.$post['year']. '&' . 
                                        'month='.$post['month']. '&' . 
                                        'day='.$post['day']. '&' . 
                                        'daycount='.$post['daycount']. '&' . 
                                        'slug=' . $post['slug'], '')), 1);

		return $post;
	}

	public function getPostByDayCount($year,$month, $day, $daycount) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE `post_type`='article' AND year = '". (int)$year . "' AND
                                                                              month = '". (int)$month . "' AND
                                                                              day = '". (int)$day . "' AND
                                                                              daycount = '". (int)$daycount . "'");
        $post = $query->row;

        $post['permalink'] = preg_replace( '`/admin/`', '/',
            rewrite_url($this->url->link('blog/post', 'year='.$post['year']. '&' . 
                                        'month='.$post['month']. '&' . 
                                        'day='.$post['day']. '&' . 
                                        'daycount='.$post['daycount']. '&' . 
                                        'slug=' . $post['slug'], '')), 1);

		return $post;
	}

	public function getRecentPosts($limit=10, $skip=0) {
        $data_array = array();
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='article' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        foreach($data as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByCategory($category_id, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE `post_type`='article' AND category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByAuthor($author_id, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='article' AND author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByArchive($year, $month, $limit=20, $skip=0) {
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='article' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
        $data = $query->rows;
        $data_array = array();
        foreach($data as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

    public function getTotalPosts(){
        $query = $this->db->query("SELECT count(post_id) as total FROM " . DATABASE . ".posts WHERE `post_type`='article'");
        return $query->rows['total'];
    }

    public function getPosts($sort='post_id', $order='DESC', $limit=20, $skip=0){

        $this->log->write("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='article'
            ORDER BY ".$this->db->escape($sort)." ".$this->db->escape($order)."
            LIMIT ". (int)$skip . ", " . (int)$limit);
        $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `post_type`='article'
            ORDER BY ".$this->db->escape($sort)." ".$this->db->escape($order)."
            LIMIT ". (int)$skip . ", " . (int)$limit);
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

        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM ".DATABASE.".posts 
                WHERE `year` = '".$year."'
                    AND `month` = '".$month."' 
                    AND `day` = '".$day."';");

        $newcount = $query->row['newval'];

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='article',
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

    public function editPost($post_id, $data){

        $post = $data['post'];

        $sql = "UPDATE " . DATABASE . ".posts SET 
            `body` = '".$this->db->escape($post['body'])."',
            `title` = '".$this->db->escape($post['title'])."',
            `slug` = '".$this->db->escape($post['slug'])."', 
            `replyto` = ". (isset($post['replyto']) && !empty($post['replyto']) ? "'".$this->db->escape($post['replyto'])."'" : "NULL") .
            " WHERE post_id = " . (int)$post_id;

        $this->db->query($sql);
    }


}
