<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full posts

	public function getPost($post_id) {
        $post = $this->cache->get('post.'. $post_id);
        if(!$post){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = '". (int)$post_id . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/'.$post['post_type'], 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. '&' . 
                                                'slug=' . $post['slug'], '')
            ));
            $this->cache->set('post.'. $post_id, $post);
        }
		return $post;
	}

	public function getPostByDayCount($year,$month, $day, $daycount) {
        $post = $this->cache->get('post.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$post){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $post = $query->row;
            $post = array_merge($post, array(
                'permalink' => $this->url->link('blog/'.$post['post_type'], 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. '&' . 
                                                'slug=' . $post['slug'], '')
            ));
            $this->cache->set('post.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $post);
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

	public function getPostsByArchive($year, $month, $limit=20, $skip=0) {
        $data = $this->cache->get('posts.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$data){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $data_array = array();
            foreach($data as $post){
                $data_array[] = $this->getPost($post['post_id']);
            }
            $this->cache->set('posts.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}


    public function addWebmention($data, $webmention_id, $comment_data){
        if(isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            $post = $this->getPostByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);

            switch($comment_data['type']) {
            case 'like':
                $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ", post_id = ".(int)$post['post_id']);
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
                    ", post_id = ".(int)$post['post_id'] .", approved=1");
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
                    ", post_id = ".(int)$post['post_id'] .", parse_timestamp = NOW(), approved=1");
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
