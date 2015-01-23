<?php
class ModelBlogListen extends Model {

    public function newListen($data){
        if(isset($data['published'])) {
            $year = date('Y', strtotime($data['published']);
            $month = date('n', strtotime($data['published']);
            $day = date('j', strtotime($data['published']);
            $timestamp = "'" . $this->db->escape($data['published']) ."'";
        } else { 
            $year = date('Y');
            $month = date('n');
            $day = date('j');
            $timestamp = "NOW()";
        }

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

        $slug = '';
        if(isset($data['slug']) && !empty($data['slug'])){
            $slug = $this->db->escape($data['slug']);
        }

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='listen',
            `body` = '',
            `title` = '',
            `syndication_extra` = '".$syndication_extra."',
            `slug` = '".$slug."',
            `author_id` = 1,
            `timestamp` = ".$timestamp.",
            `year` = ".(int)$year.",
            `month` = ".(int)$month.",
            `day` = ".(int)$day.",
            `draft` = ".(int)$draft.",
            `bookmark_listen_url` = '".$this->db->escape($data['listen'])."',
            `deleted` = 0,
            `daycount` = ".(int)$newcount;

        $query = $this->db->query($sql);

        $id = $this->db->getLastId();
        
        return $id;
    }

    public function deleteListen($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=1 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

    public function undeleteListen($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=0 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full listens

	public function getListen($post_id) {
        $this->load->model('blog/post');
        $post = $this->model_blog_post->getPost($post_id);
        $post['post_id'] = $post['post_id'];
        return $post;
	}

    public function getByData($data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getListenByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }
	public function getByDayCount($year,$month, $day, $daycount) {
        return $this->getListenByDayCount($year,$month, $day, $daycount);
    }

	public function getListenByDayCount($year,$month, $day, $daycount) {
        $this->load->model('blog/post');
        return $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);
	}

	public function getRecentListens($limit=10, $skip=0) {
        $data = $this->cache->get('listens.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='listen' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $listen){
                $data_array[] = $this->getListen($listen['post_id']);
            }
            $this->cache->set('listens.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getListensByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('listens.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='listen' AND category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $listen){
                $data_array[] = $this->getListen($listen['post_id']);
            }
            $this->cache->set('listens.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getListensByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('listens.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='listen' AND author_id = '".(int)$author_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $listen){
                $data_array[] = $this->getListen($listen['post_id']);
            }
            $this->cache->set('listens.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getListensByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('listens.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='listen' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $listen){
                $data_array[] = $this->getListen($listen['post_id']);
            }
            $this->cache->set('listens.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
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
