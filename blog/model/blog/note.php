<?php
class ModelBlogNote extends Model {

    public function newNote($data){

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

        $slug = '_';
        if(isset($data['slug']) && !empty($data['slug'])){
            $slug = $this->db->escape($data['slug']);
        }

        $sql = "INSERT INTO " . DATABASE . ".posts SET `post_type`='note',
            `body` = '".$this->db->escape($data['body'])."',
            `title` = '',
            `slug` = '".$slug."',
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
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full notes

	public function getNote($note_id) {
        $note = $this->cache->get('note.'. $note_id);
        if(!$note){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_type='note' AND post_id = '". (int)$note_id . "'");
            $note = $query->row;
            $note = array_merge($note, array(
                'note_id' => $note['post_id'],
                'permalink' => $this->url->link('blog/note', 'year='.$note['year']. '&' . 
                                                'month='.$note['month']. '&' . 
                                                'day='.$note['day']. '&' . 
                                                'daycount='.$note['daycount']. '&' . 
                                                'slug=' . $note['slug'], '')
            ));
            $this->cache->set('note.'. $note_id, $note);
        }
		return $note;
	}

	public function getNoteByDayCount($year,$month, $day, $daycount) {
        $note = $this->cache->get('note.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$note){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_type='note' AND year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $note = $query->row;
            $note = array_merge($note, array(
                'note_id' => $note['post_id'],
                'permalink' => $this->url->link('blog/note', 'year='.$note['year']. '&' . 
                                                'month='.$note['month']. '&' . 
                                                'day='.$note['day']. '&' . 
                                                'daycount='.$note['daycount']. '&' . 
                                                'slug=' . $note['slug'], '')
            ));
            $this->cache->set('note.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $note);
        }
		return $note;
	}

	public function getRecentNotes($limit=10, $skip=0) {
        $data = $this->cache->get('notes.recent.'. $skip . '.'.  $limit);
        if(!$data){
            $data_array = array();
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
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
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE post_type='note' AND category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
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
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
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
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type='note' AND `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
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


    public function addWebmention($data, $webmention_id, $comment_data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            $note = $this->getNoteByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);

            switch($comment_data['type']) {
            case 'like':
                $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$note['note_id']);
                $like_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('likes');
                break;

            case 'reply':
                $this->db->query("INSERT INTO ". DATABASE.".comments SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$comment_data['text']."'" : "") .
                    ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$comment_data['name']."'" : "") .
                    ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$comment_data['published']."'" : ", `timestamp`=NOW()") .
                    ", post_id = ".(int)$note['note_id'] .", approved=1");
                $comment_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_comment_id = '".(int)$comment_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('comments');
                break;

            case 'rsvp':
            case 'repost':
            case 'mention':
            default:
                $this->db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$note['note_id'] .", parse_timestamp = NOW(), approved=1");
                $mention_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('mentions');
                break;
            }
        } else {
            throw new Exception('Cannot look up record');
            //throwing an exception will go back to calling script and run the generic add
        }
    }

}
