<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order

	public function getPost($post_id) {
        $post = $this->cache->get('post.'. $post_id);
        if(!$post){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = '". (int)$post_id . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/post', 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. '&' . 
                                                'slug=' . $post['slug'], ''),
                'commentlink' => $this->url->link('blog/post/comment', 'pid='. $post['post_id'], '')
            ));
            $this->cache->set('post.'. $post_id, $post);
        }
		return $post;
	}
	public function getRecentPosts($limit=10, $skip=0) {
        $data = $this->cache->get('posts.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
            $this->cache->set('posts.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPostsByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('posts.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
            $this->cache->set('posts.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPostsByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('posts.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
            $this->cache->set('posts.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getPostsByDate($start_date, $end_date, $limit=20, $skip=0) {
        $data = $this->cache->get('posts.date.'.$start_date.'.'.$end_date.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE timestamp >= '".$this->db->escape($start_date)."' AND timestamp < '".$this->db->escape($end_date)."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
            $this->cache->set('posts.date.'.$start_date.'.'.$end_date.'.'.$skip.'.'.$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

}
