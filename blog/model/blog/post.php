<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller since these functions just fetch the IDs, not the full posts
    
    public function num_to_sxg($n) {
         $s = "";
          $m = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ_abcdefghijkmnopqrstuvwxyz";
          if ($n===null || $n===0) { return 0; }
          while ($n>0) {
                 $d = $n % 60;
                    $s = $m[$d].$s;
                    $n = ($n-$d)/60;
                     }
          return $s;
    }

    public function sxg_to_num($s) {
         $n = 0;
          $j = strlen($s);
          for ($i=0;$i<$j;$i++) { // iterate from first to last char of $s
                 $c = ord($s[$i]); //  put current ASCII of char into $c  
                    if ($c>=48 && $c<=57) { $c=$c-48; }
                    else if ($c>=65 && $c<=72) { $c-=55; }
                    else if ($c==73 || $c==108) { $c=1; } // typo capital I, lowercase l to 1
                    else if ($c>=74 && $c<=78) { $c-=56; }
                    else if ($c==79) { $c=0; } // error correct typo capital O to 0
                    else if ($c>=80 && $c<=90) { $c-=57; }
                    else if ($c==95) { $c=34; } // underscore
                    else if ($c>=97 && $c<=107) { $c-=62; }
                    else if ($c>=109 && $c<=122) { $c-=63; }
                    else { $c = 0; } // treat all other noise as 0
                    $n = 60*$n + $c;
                  }
           return $n;
    }

	public function getPost($post_id) {
        $post = $this->cache->get('post.'. $post_id);
        if(!$post){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = '". (int)$post_id . "'");
            $post = $query->row;
	        $syndications = $this->getSyndications($post['post_id']);
            $post = array_merge($post, array(
                'syndications' => $syndications,
                'permalink' => $this->url->link('blog/'.$post['post_type'], 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. 
                                                ($post['slug'] ? '&'.'slug='.$post['slug'] : ''), ''),
                'shortlink' => $this->short_url->link('blog/shortener', 'eid='.$this->num_to_sxg($post['post_id']), '')
            ));
            $this->cache->set('post.'. $post_id, $post);
        }
		return $post;
	}

	public function getByDayCount($year, $month, $day, $daycount) {
	    return getPostByDayCount($year, $month, $day, $daycount);
    }
	public function getPostByDayCount($year,$month, $day, $daycount) {
        $post_id = $this->cache->get('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount);
        if(!$post_id){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE year = '". (int)$year . "' AND
                                                                                  month = '". (int)$month . "' AND
                                                                                  day = '". (int)$day . "' AND
                                                                                  daycount = '". (int)$daycount . "'");
            $post_id = $query->row['post_id'];
            $this->cache->set('post_id.'. $year.'.'.$month.'.'.$day.'.'.$daycount, $post_id);
        }

		return $this->getPost($post_id);
	}

	public function getRecentPosts($limit=10, $skip=0) {
		$post_id_array = $this->cache->get('posts.recent.'. $skip . '.'.  $limit);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.recent.'. $skip . '.' .$limit, $post_id_array);
		}
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByTypes($type_list = ['article'], $limit=20, $skip=0) {
		$post_id_array = $this->cache->get('posts.type.'. implode('.',$type_list) . '.'. $skip . '.'.  $limit);
		if(!$post_id_array){
			// todo need to map this->db->escape
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type IN ('".implode("','",$type_list)."') ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.type.'.implode('.',$type_list) . '.'. $skip . '.'.  $limit, $post_id_array);
		}

        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByCategory($category_id, $limit=20, $skip=0) {
		$post_id_array = $this->cache->get('posts.category.'. $category_id . '.'. $skip . '.'.  $limit);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE category_id = '".(int)$category_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.category.'.$category_id . '.'. $skip . '.'.  $limit, $post_id_array);
		}
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByAuthor($author_id, $limit=20, $skip=0) {
        $post_id_array = $this->cache->get('posts.author.'. $author_id . '.'. $skip . '.'.  $limit);
        if(!$post_id_array){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE author_id = '".(int)$author_id."' ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $post_id_array = $query->rows;
            $this->cache->set('posts.author.'.$author_id . '.'. $skip . '.'.  $limit, $post_id_array);
        }
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
		return $data_array;
	}

	public function getPostsByArchive($year, $month, $limit=20, $skip=0) {
        $post_id_array = $this->cache->get('posts.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$post_id_array){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `year` = '".(int)$year."' AND `month` = '".(int)$month."'  ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $post_id_array = $query->rows;
            $this->cache->set('posts.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit, $post_id_array);
        }
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
		return $data_array;
	}

	public function getSyndications($post_id) {
        $query = $this->db->query("SELECT * FROM ".DATABASE.".post_syndication JOIN ".DATABASE.".syndication_site USING(syndication_site_id) WHERE post_id = ".(int)$post_id);

        return $query->rows;
	}

	public function addSyndication($post_id, $syndication_url) {
        if(!empty($syndication_url)){
            $syndication_url = trim($syndication_url);
            //figure out what site this is.
            $sites_query = $this->db->query("SELECT * FROM " . DATABASE . ".syndication_site ");
            $sites = $sites_query->rows;

            $syn_site_id = 0;
            foreach($sites as $site){
                if(strpos($syndication_url, $site['site_url_match']) === 0){
                    $syn_site_id = $site['syndication_site_id'];
                    break;
                }
            }

            // add site to DB
            $query = $this->db->query("INSERT INTO ".DATABASE.".post_syndication SET post_id = ".(int)$post_id.", syndication_site_id=".(int)$syn_site_id.", syndication_url = '".$this->db->escape($syndication_url)."'");
        }
	}

    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null){
        if(isset($comment_data['published']) && !empty($comment_data['published'])){
            // do our best to conver to local time
            date_default_timezone_set(LOCALTIMEZONE);
            $date = new DateTime($comment_data['published']);
            $now = new DateTime;
            $tz = $now->getTimezone();
            $date->setTimezone($tz);
            $comment_data['published'] = $date->format('Y-m-d H:i:s')."\n";
        }

        if($post_id || (isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount']))) {
            $post = null;
            if($post_id){
                $post= $this->getPost($post_id);
            } else {
                $post = $this->getPostByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
            }

            switch($comment_data['type']) {
            case 'like':
                $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                    ", post_id = ".(int)$post['post_id']);
                $like_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('likes');
                break;

            case 'reply':
                $this->db->query("INSERT INTO ". DATABASE.".comments SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                    ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$this->db->escape($comment_data['text'])."'" : "") .
                    ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$this->db->escape($comment_data['name'])."'" : "") .
                    ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$this->db->escape($comment_data['published'])."'" : ", `timestamp`=NOW()") .
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
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
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

    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null){
        $query = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_id = ".(int)$webmention_id." LIMIT 1");
        $webmention = $query->row;
        $resulting_comment_id = (int)$webmention['resulting_comment_id'];
        $resulting_mention_id = (int)$webmention['resulting_mention_id'];
        $resulting_like_id = (int)$webmention['resulting_like_id'];

        if(isset($comment_data['published']) && !empty($comment_data['published'])){
            // do our best to conver to local time
            date_default_timezone_set(LOCALTIMEZONE);
            $date = new DateTime($comment_data['published']);
            $now = new DateTime;
            $tz = $now->getTimezone();
            $date->setTimezone($tz);
            $comment_data['published'] = $date->format('Y-m-d H:i:s')."\n";
        }

        if($post_id || (isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount']))) {
            $post = null;
            if($post_id){
                $post= $this->getPost($post_id);
            } else {
                $post = $this->getPostByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
            }

            switch($comment_data['type']) {
            case 'like':
                if($resulting_like_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".likes WHERE like_id = ". (int)$resulting_like_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                }
                if($resulting_mention_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".mentions WHERE mention_id = ". (int)$resulting_mention_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('mentions');
                }
                if($resulting_comment_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".comments WHERE comment_id = ". (int)$resulting_comment_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_comment_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('comments');
                }

                $this->db->query("INSERT INTO ". DATABASE.".likes SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                    ", post_id = ".(int)$post['post_id']);
                $like_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = '".(int)$like_id."', webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('likes');
                break;

            case 'reply':
                if($resulting_mention_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".mentions WHERE mention_id = ". (int)$resulting_mention_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('mentions');
                }
                if($resulting_like_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".likes WHERE like_id = ". (int)$resulting_like_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('likes');
                }
                if($resulting_comment_id > 0) {
                    $this->db->query("UPDATE ". DATABASE.".comments SET source_url = '".$comment_data['url']."'".
                        ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                        ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$this->db->escape($comment_data['text'])."'" : "") .
                        ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$this->db->escape($comment_data['name'])."'" : "") .
                        ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$this->db->escape($comment_data['published'])."'" : ", `timestamp`=NOW()") .
                        ", post_id = ".(int)$post['post_id'] .", approved=1 WHERE comment_id = ".(int)$resulting_comment_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                } else {
                    $this->db->query("INSERT INTO ". DATABASE.".comments SET source_url = '".$comment_data['url']."'".
                        ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                        ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                        ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$this->db->escape($comment_data['text'])."'" : "") .
                        ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$this->db->escape($comment_data['name'])."'" : "") .
                        ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$this->db->escape($comment_data['published'])."'" : ", `timestamp`=NOW()") .
                        ", post_id = ".(int)$post['post_id'] .", approved=1");
                    $comment_id = $this->db->getLastId();
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_comment_id = '".(int)$comment_id."', webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                }
                $this->cache->delete('comments');
                break;

            case 'rsvp':
            case 'repost':
            case 'mention':
            default:
                if($resulting_mention_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".mentions WHERE mention_id = ". (int)$resulting_mention_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('mentions');
                }
                if($resulting_like_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".likes WHERE like_id = ". (int)$resulting_like_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_like_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('likes');
                }
                if($resulting_comment_id > 0) {
                    $this->db->query("DELETE FROM ". DATABASE.".comments WHERE comment_id = ". (int)$resulting_comment_id);
                    $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_comment_id = null, webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                    $this->cache->delete('comments');
                }

                $this->db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$this->db->escape($comment_data['author']['name'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$this->db->escape($comment_data['author']['url'])."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$this->db->escape($comment_data['author']['photo'])."'" : "") .
                    ", post_id = ".(int)$post['post_id'] .", parse_timestamp = NOW(), approved=1");
                $mention_id = $this->db->getLastId();
                $this->db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'Updated' WHERE webmention_id = ". (int)$webmention_id);
                $this->cache->delete('mentions');
                break;
            }
        } else {
            throw new Exception('Cannot look up record');
            //throwing an exception will go back to calling script and run the generic add
        }
    }

}
