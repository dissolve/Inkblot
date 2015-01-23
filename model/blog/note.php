<?php
class ModelBlogNote extends Model {

    public function newNote($data){
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
        if(isset($data['draft']) && ($data['draft'] == 1 || $data['draft'] == '1')){
            $draft= 1;
        }

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

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='note',
            `body` = '".$this->db->escape($data['body'])."',
            `title` = '',
            `slug` = '".$slug."',
            `syndication_extra` = '".$syndication_extra."',
            `author_id` = 1,
            `timestamp` = ".$timestamp.",
            `year` = ".(int)$year.",
            `month` = ".(int)$month.",
            `day` = ".(int)$day.",
            `draft` = ".(int)$draft.",
            `deleted` = 0,
            `daycount` = ".(int)$newcount .
            (isset($data['rsvp']) && !empty($data['rsvp']) ? ", rsvp='".$this->db->escape($data['rsvp'])."'" : "").
            (isset($data['location']) && !empty($data['location']) ? ", location='".$this->db->escape($data['location'])."'" : "").
            (isset($data['place_name']) && !empty($data['place_name']) ? ", place_name='".$this->db->escape($data['place_name'])."'" : "").
            (isset($data['replyto']) && !empty($data['replyto']) ? ", replyto='".$this->db->escape($data['replyto'])."'" : "");

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

    public function setSyndicationExtra($note_id, $syn_extra_val){
        $this->db->query("UPDATE ".DATABASE.".posts SET syndication_extra='".$this->db->escape($syn_extra_val) . "' WHERE post_id = ".(int)$note_id);
    }

    public function deleteNote($note_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=1 WHERE post_id = ".(int)$note_id;
        $this->db->query($sql);
    }

    public function undeleteNote($note_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=0 WHERE post_id = ".(int)$note_id;
        $this->db->query($sql);
    }

    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full notes

	public function getNote($note_id) {
        $this->load->model('blog/post');
        $note = $this->model_blog_post->getPost($note_id);
        $note['note_id'] = $note['post_id'];
        return $note;
	}

    public function getByData($data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getNoteByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }
	public function getByDayCount($year,$month, $day, $daycount) {
        return $this->getNoteByDayCount($year,$month, $day, $daycount);
    }

	public function getNoteByDayCount($year,$month, $day, $daycount) {
        $note_id = $this->cache->get('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$note_id){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $note_id = $query->row['post_id'];
            $this->cache->set('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $note_id);
        }

		return $this->getNote($note_id);
	}

	public function getRecentNotes($limit=10, $skip=0) {
        $data = $this->cache->get('notes.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            foreach($data as $note){
                $data_array[] = $this->getNote($note['post_id']);
            }
            $this->cache->set('notes.recent.'. $skip . '.' .$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getNotesByCategory($category_id, $limit=20, $skip=0) {
        $data = $this->cache->get('notes.category.'. $category_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='note' AND category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $note){
                $data_array[] = $this->getNote($note['post_id']);
            }
            $this->cache->set('notes.category.'.$category_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getNotesByAuthor($author_id, $limit=20, $skip=0) {
        $data = $this->cache->get('notes.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND author_id = '".(int)$author_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $note){
                $data_array[] = $this->getNote($note['post_id']);
            }
            $this->cache->set('notes.author.'.$author_id . '.'. $skip . '.'.  $limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getNotesByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('notes.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $note){
                $data_array[] = $this->getNote($note['post_id']);
            }
            $this->cache->set('notes.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
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
