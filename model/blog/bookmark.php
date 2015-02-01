<?php
class ModelBlogBookmark extends Model {

    public function newBookmark($data){
        if(isset($data['published'])) {
            $year = date('Y', strtotime($data['published']));
            $month = date('n', strtotime($data['published']));
            $day = date('j', strtotime($data['published']));
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

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='bookmark',
            `body` = '".$this->db->escape($data['description'])."',
            `title` = '".$this->db->escape($data['name'])."',
            `slug` = '".$slug."',
            `syndication_extra` = '".$syndication_extra."',
            `author_id` = 1,
            `timestamp` = ".$timestamp.",
            `year` = ".(int)$year.",
            `month` = ".(int)$month.",
            `day` = ".(int)$day.",
            `draft` = ".(int)$draft.",
            `bookmark_like_url` = '".$this->db->escape($data['bookmark'])."',
            `deleted` = 0,
            `daycount` = ".(int)$newcount;

        $query = $this->db->query($sql);

        $id = $this->db->getLastId();

        if(isset($data['category']) && !empty($data['category'])){
            $categories = explode(',',$data['category']);
            foreach ($categories as $cat) {
                $trimmed_cat = trim($cat);
                $query = $this->db->query("SELECT category_id FROM ".DATABASE.".categories where name='".$this->db->escape($trimmed_cat)."'");
                $find_cat = $query->row;
                $cid = 0;
                if(empty($find_cat)){
                    $this->db->query("INSERT INTO ".DATABASE.".categories SET name='".$this->db->escape($trimmed_cat)."'");
                    $cid = $this->db->getLastId();

                } else {
                    $cid = $find_cat['category_id'];

                }
                $this->db->query("INSERT INTO ".DATABASE.".categories_posts SET category_id=".(int)$cid.", post_id = ".(int)$id);

            }
        }
        
        return $id;
    }

    public function deleteBookmark($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=1 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

    public function undeleteBookmark($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=0 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
    }

	public function getBookmark($post_id) {
        $this->load->model('blog/post');
        $post = $this->model_blog_post->getPost($post_id);
        $post['bookmark_id'] = $post['post_id'];
        return $post;
	}

    public function getByData($data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getBookmarkByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }
	public function getByDayCount($year,$month, $day, $daycount) {
        return $this->getBookmarkByDayCount($year,$month, $day, $daycount);
    }

	public function getBookmarkByDayCount($year,$month, $day, $daycount) {
        $this->load->model('blog/post');
        return $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);
	}

	public function getRecentBookmarks($limit=10, $skip=0) {
        $data = $this->cache->get('bookmarks.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='bookmark' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $bookmark){
                $data_array[] = $this->getBookmark($bookmark['post_id']);
            }
            $this->cache->set('bookmarks.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getBookmarksByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('bookmarks.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='bookmark' AND category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $bookmark){
                $data_array[] = $this->getBookmark($bookmark['post_id']);
            }
            $this->cache->set('bookmarks.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getBookmarksByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('bookmarks.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='bookmark' AND author_id = '".(int)$author_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $bookmark){
                $data_array[] = $this->getBookmark($bookmark['post_id']);
            }
            $this->cache->set('bookmarks.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getBookmarksByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('bookmarks.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='bookmark' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $bookmark){
                $data_array[] = $this->getBookmark($bookmark['post_id']);
            }
            $this->cache->set('bookmarks.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
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
