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

    public function editPost($data){
        //$this->log->write('called editPost');
        //$this->log->write(print_r($data,true));
        if(isset($data['post_id'])){
            $set_data = array();

            if(isset($data['title']) && !empty($data['title'])){
                $set_data[] = "title ='".$this->db->escape($data['title'])."'";
            } else {
                $set_data[] = "title =''";
            }

            if(isset($data['body']) && !empty($data['body'])){
                $set_data[] = "body ='".$this->db->escape($data['body'])."'";
            } else {
                $set_data[] = "body =''";
            }

            if(isset($data['location']) && !empty($data['location'])){
                $set_data[] = "location ='".$this->db->escape($data['location'])."'";
            } else {
                $set_data[] = "location =''";
            }

            if(isset($data['place_name']) && !empty($data['place_name'])){
                $set_data[] = "place_name ='".$this->db->escape($data['place_name'])."'";
            } else {
                $set_data[] = "place_name =''";
            }

            if(isset($data['like-of']) && !empty($data['like-of'])){
                $set_data[] = "bookmark_like_url ='".$this->db->escape($data['like-of'])."'";
            } else {
                $set_data[] = "bookmark_like_url =''";
            }

            if(isset($data['bookmark']) && !empty($data['bookmark'])){
                $set_data[] = "bookmark_like_url ='".$this->db->escape($data['bookmark'])."'";
            } else {
                $set_data[] = "bookmark_like_url =''";
            }

            //$this->log->write(print_r($set_data,true));
            //todo category
            //todo syndicate-to

            $sql = "UPDATE ".DATABASE.".posts SET ".implode(' , ', $set_data)." WHERE post_id=".(int)$data['post_id'];
            $this->db->query($sql);
            $this->cache->delete('post.'.$data['post_id']);
        }
    }

    public function setSyndicationExtra($post_id, $syn_extra_val){
        $this->db->query("UPDATE ".DATABASE.".posts SET syndication_extra='".$this->db->escape($syn_extra_val) . "' WHERE post_id = ".(int)$post_id);
    }

    public function deletePost($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=1 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
        $this->cache->delete('post.'. $post_id);
        $this->cache->delete('posts');
    }

    public function undeletePost($post_id){
        $sql = "UPDATE " . DATABASE . ".posts SET `deleted`=0 WHERE post_id = ".(int)$post_id;
        $this->db->query($sql);
        $this->cache->delete('post.'. $post_id);
        $this->cache->delete('posts');
    }

	public function getPost($post_id) {
        $post = $this->cache->get('post.'. $post_id);
        if(!$post){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".posts WHERE post_id = ". (int)$post_id);
            $post = $query->row;
	        $syndications = $this->getSyndications($post['post_id']);
            $shortlink = $this->short_url->link('blog/shortener', 'eid='.$this->num_to_sxg($post['post_id']), '');
            $citation = '(' . trim(str_replace(array('http://','https://'),array('',''), HTTP_SHORT), '/'). ' '. trim(str_replace(array(HTTP_SHORT,HTTPS_SHORT),array('',''), $shortlink),'/')  .')';
            $post = array_merge($post, array(
                'syndications' => $syndications,
                'permalink' => $this->url->link('blog/'.$post['post_type'], 'year='.$post['year']. '&' . 
                                                'month='.$post['month']. '&' . 
                                                'day='.$post['day']. '&' . 
                                                'daycount='.$post['daycount']. 
                                                ($post['slug'] ? '&'.'slug='.$post['slug'] : ''), ''),
                'shortlink' => $shortlink,
                'permashortcitation' => $citation
            ));
            if($post['post_type'] == 'article' && preg_match('/<hr \/>/', $post['body'])){
                $post['excerpt'] = preg_replace('/<hr \/>.*/s','',$post['body']);
                $post['body'] = preg_replace('/<hr \/>/','',$post['body']);
            }
            date_default_timezone_set(LOCALTIMEZONE);
            $post['timestamp'] =  date("c", strtotime( $post['timestamp']));
            if($post['post_type'] == 'bookmark'){
                $post['bookmark'] = $post['bookmark_like_url'];
                $post['name'] = $post['title'];
                $post['description'] = $post['body'];
            } elseif($post['post_type'] == 'like'){
                $post['like-of'] = $post['bookmark_like_url'];
            }
            $this->cache->set('post.'. $post_id, $post);
        }
		return $post;
	}

	public function getByDayCount($year, $month, $day, $daycount) {
	    return $this->getPostByDayCount($year, $month, $day, $daycount);
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
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.recent.'. $skip . '.' .$limit, $post_id_array);
		}
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getRecentDrafts( $limit=20, $skip=0) {
		$post_id_array = $this->cache->get('posts.drafts.'. $skip . '.'.  $limit);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE deleted=0 AND draft=1 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.drafts.'. $skip . '.'.  $limit, $post_id_array);
		}

        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByTypes($type_list = ['article'], $limit=20, $skip=0) {
        if($this->session->data['is_owner']){
            $post_id_array = $this->cache->get('posts.type.owner.'. implode('.',$type_list) . '.'. $skip . '.'.  $limit);
            if(!$post_id_array){
                // todo need to map this->db->escape
                $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type IN ('".implode("','",$type_list)."') AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
                $post_id_array = $query->rows;
                $this->cache->set('posts.type.owner.'.implode('.',$type_list) . '.'. $skip . '.'.  $limit, $post_id_array);
            }
        } else {
            $post_id_array = $this->cache->get('posts.type.'. implode('.',$type_list) . '.'. $skip . '.'.  $limit);
            if(!$post_id_array){
                // todo need to map this->db->escape
                $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type IN ('".implode("','",$type_list)."') AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
                $post_id_array = $query->rows;
                $this->cache->set('posts.type.'.implode('.',$type_list) . '.'. $skip . '.'.  $limit, $post_id_array);
            }

        }
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

    public function addToCategory($post_id, $category_name){
        $trimmed_cat = trim($category_name);
        $query = $this->db->query("SELECT category_id FROM ".DATABASE.".categories where name='".$this->db->escape($trimmed_cat)."'");
        $find_cat = $query->row;
        $cid = 0;
        if(empty($find_cat)){
            $this->db->query("INSERT INTO ".DATABASE.".categories SET name='".$this->db->escape($trimmed_cat)."'");
            $cid = $this->db->getLastId();
        } else {
            $cid = $find_cat['category_id'];
        }
        $this->db->query("INSERT INTO ".DATABASE.".categories_posts SET category_id=".(int)$cid.", post_id = ".(int)$post_id);
    }

    public function removeFromAllCategories($post_id){
        $this->db->query("DELETE FROM ".DATABASE.".categories_posts WHERE post_id = ".(int)$post_id);
        //todo find and remove empty categories
    }

	public function getPostsByCategory($category_id, $limit=20, $skip=0) {
		$post_id_array = $this->cache->get('posts.category.'. $category_id . '.'. $skip . '.'.  $limit);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts JOIN ".DATABASE.".categories_posts USING(post_id) WHERE category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.category.'.$category_id . '.'. $skip . '.'.  $limit, $post_id_array);
		}
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getRecentPostsByType($type, $limit=20, $skip=0) {
		$post_id_array = $this->cache->get('posts.'.$type.'.'. $skip . '.'.  $limit);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE post_type = '".$this->db->escape($type)."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.'.$type . '.'. $skip . '.'.  $limit, $post_id_array);
		}
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
	
		return $data_array;
	}

	public function getPostsByDay($year, $month, $day) {
		$post_id_array = $this->cache->get('posts.day.'. $year . '.'. $month . '.'.  $day);
		if(!$post_id_array){
		    $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE category_id = '".(int)$category_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND `day` = ".(int)$day." AND deleted=0 AND draft=0 ORDER BY timestamp DESC");
		    $post_id_array = $query->rows;
		    $this->cache->set('posts.day.'.$year . '.'. $month . '.'.  $day, $post_id_array);
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
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE author_id = '".(int)$author_id."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $post_id_array = $query->rows;
            $this->cache->set('posts.author.'.$author_id . '.'. $skip . '.'.  $limit, $post_id_array);
        }
	
        $data_array = array();
        foreach($post_id_array as $post){
            $data_array[] = $this->getPost($post['post_id']);
        }
		return $data_array;
	}

	public function getPostsByArchive($year, $month, $limit=NULL, $skip=0) {
        $post_id_array = $this->cache->get('posts.date.'.$year.'.'.$month.'.'.$skip.'.'.$limit);
        if(!$post_id_array){
            $query = $this->db->query("SELECT post_id FROM " . DATABASE . ".posts WHERE `year` = '".(int)$year."' AND `month` = '".(int)$month."' AND deleted=0 AND draft=0 ORDER BY timestamp DESC ". ($limit ? " LIMIT " . (int)$skip .  ", " . (int)$limit : ''));
            $post_id_array = $query->rows;
            if(!$limit){
                $limit = 'all';
            }
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

            $this->cache->delete('post.'. $post_id);
        }
	}

    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null){
        $this->load->model('webmention/vouch');

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
                $this->model_webmention_vouch->addWhitelistEntry($comment_data['url']);
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
                $this->model_webmention_vouch->addWhitelistEntry($comment_data['url']);
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
                $this->model_webmention_vouch->addWhitelistEntry($comment_data['url']);
                $this->cache->delete('mentions');
                break;
            }
        } else {
            throw new Exception('Cannot look up record');
            //throwing an exception will go back to calling script and run the generic add
        }
    }

    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null){
        $query = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_id = ".(int)$webmention_id." LIMIT 1");
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

    public function getGenericLikes($limit=100, $skip=0) {
        $data = $this->cache->get('likes.generic.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id IS NULL ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('likes.generic.'. $skip . '.' .$limit, $data);
        }
	return $data;
    }

    public function getLikesForPost($post_id, $limit=100, $skip=0) {
        $data = $this->cache->get('likes.post.'.$post_id.'.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id." ORDER BY like_id DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('likes.post.'.$post_id.'.'. $skip . '.' .$limit, $data);
        }
	return $data;
    }

    public function getGenericLikeCount() {
        $data = $this->cache->get('likes.generic.count');
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id IS NULL");
            $data = $query->row['total'];
            $this->cache->set('likes.generic.count', $data);
        }
	return $data;
    }

    public function getLikeCountForPost($post_id) {
        $data = $this->cache->get('likes.post.count.'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".likes WHERE post_id = ".(int)$post_id);
            $data = $query->row['total'];
            $this->cache->set('likes.post.count.'.$post_id, $data);
        }
	return $data;
    }


}
