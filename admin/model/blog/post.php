<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full posts

	public function getPost($post_id) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = '". (int)$post_id . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/post', 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. '&' . 
                                                'post_type='.$post['post_type']. '&' . 
                                                'slug=' . $post['slug'], '')
            ));
		return $post;
	}

	public function getPostByDayCount($year,$month, $day, $daycount) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/post', 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. '&' . 
                                                'post_type='.$post['post_type']. '&' . 
                                                'slug=' . $post['slug'], '')
            ));
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



}
