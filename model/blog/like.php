<?php
class ModelBlogLike extends Model {

    public function newLike($data){
        $year = date('Y');
        $month = date('n');
        $day = date('j');

        $draft= 0;

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

        $slug = 'like';

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='like',
            `body` = '',
            `title` = '',
            `slug` = '".$slug."',
            `syndication_extra` = '".$syndication_extra."',
            `author_id` = 1,
            `timestamp` = NOW(),
            `year` = ".(int)$year.",
            `month` = ".(int)$month.",
            `day` = ".(int)$day.",
            `draft` = ".(int)$draft.",
            `bookmark_like_url` = '".$this->db->escape($data['like'])."',
            `deleted` = 0,
            `daycount` = ".(int)$newcount;

        $query = $this->db->query($sql);

        $id = $this->db->getLastId();
        
        return $id;
    }

    public function deleteLike($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=1 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

    public function undeleteLike($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=0 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full likes

	public function getLike($post_id) {
        $this->load->model('blog/post');
        $post = $this->model_blog_post->getPost($post_id);
        $post['post_id'] = $post['post_id'];
        return $post;
	}

    public function getByData($data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getLikeByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }
	public function getByDayCount($year,$month, $day, $daycount) {
        return $this->getLikeByDayCount($year,$month, $day, $daycount);
    }

	public function getLikeByDayCount($year,$month, $day, $daycount) {
        $this->load->model('blog/post');
        return $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);
	}

	public function getRecentLikes($limit=10, $skip=0) {
        $data = $this->cache->get('likes.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='like' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $like){
                $data_array[] = $this->getLike($like['post_id']);
            }
            $this->cache->set('likes.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getLikesByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('likes.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='like' AND category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $like){
                $data_array[] = $this->getLike($like['post_id']);
            }
            $this->cache->set('likes.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getLikesByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('likes.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='like' AND author_id = '".(int)$author_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $like){
                $data_array[] = $this->getLike($like['post_id']);
            }
            $this->cache->set('likes.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getLikesByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('likes.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='like' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $like){
                $data_array[] = $this->getLike($like['post_id']);
            }
            $this->cache->set('likes.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
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
