<?php
class ModelBlogPost extends Model {


    //TODO: add a boolean flag to for ASC, so change the sort order
    //TODO: limit and skip should probably be moved to the controller
    //       since these functions just fetch the IDs, not the full posts

    public function numToSxg($n)
    {
         $s = "";
          $m = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ_abcdefghijkmnopqrstuvwxyz";
        if ($n === null || $n === 0) {
            return 0;
        }
        while ($n > 0) {
               $d = $n % 60;
                  $s = $m[$d] . $s;
                  $n = ($n - $d) / 60;
        }
          return $s;
    }

    public function sxgToNum($s)
    {
         $n = 0;
          $j = strlen($s);
        for ($i = 0; $i < $j; $i++) { // iterate from first to last char of $s
               $c = ord($s[$i]); //  put current ASCII of char into $c
            if ($c >= 48 && $c <= 57) {
                $c = $c - 48;
            } else if ($c >= 65 && $c <= 72) {
                $c -= 55;
            } else if ($c == 73 || $c == 108) {
                  $c = 1;
            } // typo capital I, lowercase l to 1
                    else if ($c >= 74 && $c <= 78) {
                        $c -= 56;
                    } else if ($c == 79) {
                        $c = 0;
                    } // error correct typo capital O to 0
                    else if ($c >= 80 && $c <= 90) {
                        $c -= 57;
                    } else if ($c == 95) {
                        $c = 34;
                    } // underscore
                    else if ($c >= 97 && $c <= 107) {
                        $c -= 62;
                    } else if ($c >= 109 && $c <= 122) {
                        $c -= 63;
                    } else {
                        $c = 0;
                    } // treat all other noise as 0
                    $n = 60 * $n + $c;
        }
           return $n;
    }

    public function editPost($data)
    {
        //$this->log->write('called editPost');
        //$this->log->write(print_r($data,true));
        if (isset($data['post_id'])) {
            $set_data = array();

            if (isset($data['name']) && !empty($data['name'])) {
                $set_data[] = "title ='" . $this->db->escape($data['name']) . "'";
            } else {
                $set_data[] = "title =''";
            }

            if (isset($data['summary']) && !empty($data['summary'])) {
                $set_data[] = "summary ='" . $this->db->escape($data['summary']) . "'";
            } else {
                $set_data[] = "summary =''";
            }

            if (isset($data['content']) && !empty($data['content'])) {
                $set_data[] = "content ='" . $this->db->escape($data['content']) . "'";
            } else {
                $set_data[] = "content =''";
            }

            if (isset($data['location']) && !empty($data['location'])) {
                $set_data[] = "location ='" . $this->db->escape($data['location']) . "'";
            } else {
                $set_data[] = "location =''";
            }

            if (isset($data['place_name']) && !empty($data['place_name'])) {
                $set_data[] = "place_name ='" . $this->db->escape($data['place_name']) . "'";
            } else {
                $set_data[] = "place_name =''";
            }

            if (isset($data['like-of']) && !empty($data['like-of'])) {
                $set_data[] = "bookmark_like_url ='" . $this->db->escape($data['like-of']) . "'";
            } else {
                $set_data[] = "bookmark_like_url =''";
            }

            if (isset($data['bookmark']) && !empty($data['bookmark'])) {
                $set_data[] = "bookmark_like_url ='" . $this->db->escape($data['bookmark']) . "'";
            } else {
                $set_data[] = "bookmark_like_url =''";
            }

            //$this->log->write(print_r($set_data,true));
            //todo category
            //todo syndicate-to

            $sql = "UPDATE " . DATABASE . ".posts SET " . implode(' , ', $set_data) . " WHERE post_id=" . (int)$data['post_id'];
            $this->db->query($sql);
            $this->cache->delete('post.' . $data['post_id']);
        }
    }
    public function newPost($type, $data)
    {
        $this->log->write(print_r($data, true));

        if (isset($data['published']) && !empty($data['published'])) {
            $year = date('Y', strtotime($data['published']));
            $month = date('n', strtotime($data['published']));
            $day = date('j', strtotime($data['published']));
            $timestamp = "'" . $this->db->escape($data['published']) . "'";
        } else {
            $year = date('Y');
            $month = date('n');
            $day = date('j');
            $timestamp = "NOW()";
        }

        $draft = 0;
        if (isset($data['draft']) && ($data['draft'] == 1 || $data['draft'] == '1')) {
            $draft = 1;
        }

        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM " . DATABASE . ".posts 
                WHERE `year` = '" . $year . "'
                    AND `month` = '" . $month . "' 
                    AND `day` = '" . $day . "';");

        $newcount = $query->row['newval'];



        $sql = "INSERT INTO " . DATABASE . ".posts " .
            " SET `post_type`='" . $this->db->escape($type) . "', " .
            " `timestamp` = " . $timestamp . ", " .
            " `year` = " . (int)$year . ", " .
            " `month` = " . (int)$month . ", " .
            " `day` = " . (int)$day . ", " .
            " `draft` = " . (int)$draft . ", " .
            " `deleted` = 0, " .
            " `content` = '" . (isset($data['content']) && !empty($data['content']) ? $this->db->escape($data['content']) : "") . "', " .
            " `summary` = '" . (isset($data['summary']) && !empty($data['summary']) ? $this->db->escape($data['summary']) : "") . "', " .
            " `slug` = '" . (isset($data['slug']) && !empty($data['slug']) ? $this->db->escape($data['slug']) : "") . "', " .

            " `syndication_extra` = '" . (
                isset($data['syndication_extra']) && !empty($data['syndication_extra'])
                ? $this->db->escape($data['syndication_extra'])
                : "") .
            "', " .

            " `daycount` = " . (int)$newcount .

            (isset($data['artist']) && !empty($data['artist'])
                ? ", artist='" . $this->db->escape($data['artist']) . "'"
                : "") . //for "listens"
            (isset($data['bookmark_like_url']) && !empty($data['bookmark_like_url'])
            ? ", bookmark_like_url='" . $this->db->escape($data['bookmark_like_url']) . "'"
                : "") .
            (isset($data['tag_category']) && !empty($data['tag_category'])
                ? ", tag_category='" . $this->db->escape($data['tag_category']) . "'"
                : "") .
            (isset($data['tag_person']) && !empty($data['tag_person'])
                ? ", tag_person='" . $this->db->escape($data['tag_person']) . "'"
                : "") .
            (isset($data['tag_url']) && !empty($data['tag_url'])
                ? ", tag_url='" . $this->db->escape($data['tag_url']) . "'"
                : "") .
            (isset($data['tag_shape']) && !empty($data['tag_shape'])
                ? ", tag_shape='" . $this->db->escape($data['tag_shape']) . "'"
                : "") .
            (isset($data['tag_coords']) && !empty($data['tag_coords'])
                ? ", tag_coords='" . $this->db->escape($data['tag_coords']) . "'"
                : "") .

            (isset($data['name']) && !empty($data['name'])
                ? ", title='" . $this->db->escape($data['name']) . "'"
                : "") .
            (isset($data['rsvp']) && !empty($data['rsvp'])
                ? ", rsvp='" . $this->db->escape($data['rsvp']) . "'"
                : "") .
            (isset($data['location']) && !empty($data['location'])
                ? ", location='" . $this->db->escape($data['location']) . "'"
                : "") .
            (isset($data['place_name']) && !empty($data['place_name'])
                ? ", place_name='" . $this->db->escape($data['place_name']) . "'"
                : "") .
            (isset($data['replyto']) && !empty($data['replyto'])
                ? ", replyto='" . $this->db->escape($data['replyto']) . "'"
                : "") .
            (isset($data['created_by']) && !empty($data['created_by'])
                ? ", created_by='" . $this->db->escape($data['created_by']) . "'"
                : "") .
            (isset($data['following_id']) && !empty($data['following_id'])
                ? ", following_id='" . (int)$data['following_id'] . "'"
                : "");

        //$this->log->write($sql);
        $query = $this->db->query($sql);

        $post_id = $this->db->getLastId();

        if (isset($data['category']) && !empty($data['category'])) {
            $categories = explode(',', $data['category']);
            foreach ($categories as $cat) {
                $this->load->model('blog/category');
                $category = $this->model_blog_category->getCategoryByName($cat, true);
                $this->db->query(
                    "INSERT INTO " . DATABASE . ".categories_posts " .
                    " SET category_id=" . (int)$category['category_id'] . ", " .
                    " post_id = " . (int)$post_id
                );
            }
        }

        foreach(array('photo', 'audio', 'video') as $media_type){

            if(isset($data[$media_type]) && !empty($data[$media_type])){
                $this->addMediaToPost($post_id, $media_type, $data[$media_type]);
            }
        } //end foreach media_type

        return $post_id;
    }

    public function addMediaToPost($post_id, $media_type, $media_data){
        if(empty($media_data)){
            return;
        }
        if($this->isHash($media_data)){
            $this->log->write('debug1');
            $this->db->query(
                "INSERT INTO " . DATABASE . ".media " .
                " SET path='" . $this->db->escape($media_data['value']) . "'" .
                " , type='".$media_type."'" .
                (isset($media_data['alt']) ? ', alt="'.$this->db->escape($media_data['alt']).'"' : '')
            );
            $media_id = $this->db->getLastId();
            $this->db->query(
                "INSERT INTO " . DATABASE . ".media_posts " .
                " SET media_id =". (int)$media_id .
                " , post_id =". (int)$post_id 
            );
        } elseif(is_array($media_data)){
            $this->log->write('debug2');
            foreach($media_data as $media_obj){
                $this->addMediaToPost($post_id, $media_type, $media_obj);
            } 
        } else {

            $this->log->write('debug3');
            $this->db->query(
                "INSERT INTO " . DATABASE . ".media " .
                " SET path='" . $this->db->escape($media_data) . "'" .
                " , type='".$media_type."'" 
            );
            $media_id = $this->db->getLastId();
            $this->db->query(
                "INSERT INTO " . DATABASE . ".media_posts " .
                " SET media_id =". (int)$media_id .
                " , post_id =". (int)$post_id 
            );
        }
    }

    public function setSyndicationExtra($post_id, $syn_extra_val)
    {
        $this->db->query("UPDATE " . DATABASE . ".posts " .
            " SET syndication_extra='" . $this->db->escape($syn_extra_val) . "' " .
            " WHERE post_id = " . (int)$post_id);
    }

    public function deletePost($post_id)
    {
        $sql = "UPDATE " . DATABASE . ".posts " .
            " SET `deleted`=1 " .
            " WHERE post_id = " . (int)$post_id;
        $this->db->query($sql);
        $this->cache->delete('post.' . $post_id);
        $this->cache->delete('posts');
    }

    public function undeletePost($post_id)
    {
        $sql = "UPDATE " . DATABASE . ".posts " .
            " SET `deleted`=0 " .
            " WHERE post_id = " . (int)$post_id;
        $this->db->query($sql);
        $this->cache->delete('post.' . $post_id);
        $this->cache->delete('posts');
    }

    public function getPostAsMf2($post_id) {
        $internal_post = $this->getPost($post_id);
        $mf2_post = array(
            'type' => array('h-entry'),
            'properties' => array()
        );
        foreach($internal_post as $key => $value){
            if(!empty($value)){
                switch($key){
                    case 'bookmark_like_url';
                        $mf2_post['properties']['like-of'] = $value;
                        break;
                    case 'permalink';
                        $mf2_post['properties']['url'] = $value;
                        break;
                    case 'timestamp';
                        $mf2_post['properties']['created'] = $value;
                        break;

                    case 'slug':
                    case 'summary':
                    case 'content':
                    case 'name':
                        break;

                    case 'draft':
                    case 'artist':
                    case 'deleted':


                        /*
                'tag_category='
                'tag_person='
                'tag_url='
                'tag_shape='
                'tag_coords='
                'rsvp='
                'location='
                'place_name='
                'replyto='
                'created_by='
                'following_id='
                         */
                        break;
                    default:
                        break;

                }
                if(is_array($value)){
                    $mf2_post['properties'][$key] = $value;
                } else {
                    $mf2_post['properties'][$key] = array($value);
                }
            }

        }

        $fields_supported = array('name', 'like-of', 'bookmark', 'category', 'description');


        foreach($fields_supported as $field_name){

            if(isset($post[$field_name])){
                if(is_array($post[$field_name])){
                    $mf2_post['properties'][$field_name] = $post[$field_name];
                } else {
                    $mf2_post['properties'][$field_name] = array($post[$field_name]);
                }
            }
        }


        return $mf2_post;

    }

    public function getPost($post_id)
    {
        $post = $this->cache->get('post.' . $post_id);
        if (!$post) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".posts " .
                " WHERE post_id = " . (int)$post_id
            );
            $post = $query->row;
            if(empty($post)){
                return null;
            }
            $syndications = $this->getSyndications($post['post_id']);
            $shortlink = $this->short_url->link(
                'common/shortener',
                'eid=' . $this->numToSxg($post['post_id']),
                ''
            );
            unset($post['photo']);
            unset($post['audio']);
            unset($post['video']);
            $photo = $this->getMediaForPost($post_id, 'photo');
            if(!empty($photo)){
                $post['photo'] = $photo;
            }
            $video = $this->getMediaForPost($post_id, 'video');
            if(!empty($video)){
                $post['video'] = $video;
            }
            $audio = $this->getMediaForPost($post_id, 'audio');
            if(!empty($audio)){
                $post['audio'] = $audio;
            }

            //$citation = '(' . trim(str_replace(array('http://','https://'),array('',''), HTTP_SHORT), '/').
            //' '. trim(str_replace(array(HTTP_SHORT,HTTPS_SHORT),array('',''), $shortlink),'/')  .')';
            $post = array_merge($post, array(
                'syndications' => $syndications,
                'permalink' => $this->url->link(
                    'blog/post',
                    'post_type=' . $post['post_type'] . '&' .
                    'year=' . $post['year'] . '&' .
                    'month=' . $post['month'] . '&' .
                    'day=' . $post['day'] . '&' .
                    'daycount=' . $post['daycount'] .
                    ($post['slug'] ? '&' . 'slug=' . $post['slug'] : ''),
                    ''
                ),
                'public' => true,
                'shortlink' => $shortlink//,
                //'permashortcitation' => $citation
            ));

            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".post_access " .
                " WHERE post_id = " . (int)$post_id
            );
            $acls = $query->rows;

            if(!empty($acls)){
                $post['public'] = false;
                $post['access'] = $acls;
            }

            $post['name'] = $post['title'];

            //if ($post['post_type'] == 'article' && preg_match('/<hr \/>/', $post['content'])) {
                //$post['summary'] = preg_replace('/<hr \/>.*/s', '', $post['content']);
                //$post['content'] = preg_replace('/<hr \/>/', '', $post['content']);
            //}
            date_default_timezone_set(LOCALTIMEZONE);
            $post['timestamp'] = date("c", strtotime($post['timestamp']));
            if ($post['post_type'] == 'bookmark') {
                $post['bookmark'] = $post['bookmark_like_url'];
                $post['description'] = $post['content'];
            } elseif ($post['post_type'] == 'like') {
                $post['like-of'] = $post['bookmark_like_url'];
            }
            $this->cache->set('post.' . $post_id, $post);
        }
        if ($this->session->data['is_owner']) {
            return $post;

        } elseif(!$post['public']){
            if(!isset($this->session->data['person_id'])){
                return null;
            }
            $logged_in_person_id = $this->session->data['person_id'];

            $allow_view = false;
            $this->log->write('debug 1 ' .  print_r($post['access'], true));
            $this->log->write($logged_in_person_id);
            foreach($post['access'] as $access_entry){
                $this->log->write( $access_entry['person_id'] . '??'. $logged_in_person_id);
                if($access_entry['person_id'] == $logged_in_person_id){
                    $this->log->write('good');
                    $allow_view = true;
                }
            }
            if(!$allow_view){
                return null;
            }
        }
        return $post;
    }

    public function getPostByData($data)
    {
        if (isset($data['year']) && isset($data['month']) && isset($data['day']) && isset($data['daycount'])) {
            return $this->getPostByDayCount($data['year'], $data['month'], $data['day'], $data['daycount']);
        } else {
            return null;
        }
    }

    public function getPostByDayCount($year, $month, $day, $daycount)
    {
        $post_id = $this->cache->get('post_id.' . $year . '.' . $month . '.' . $day . '.' . $daycount);
        if (!$post_id) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE year = '" . (int)$year . "' " .
                " AND month = '" . (int)$month . "' " .
                " AND day = '" . (int)$day . "' " .
                " AND daycount = '" . (int)$daycount . "'"
            );
            $post_id = $query->row['post_id'];
            $this->cache->set('post_id.' . $year . '.' . $month . '.' . $day . '.' . $daycount, $post_id);
        }

        return $this->getPost($post_id);
    }

    public function getRecentPosts($limit = 10, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.recent.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.recent.' . $skip . '.' . $limit, $post_id_array);
        }

            $data_array = array();
        foreach ($post_id_array as $post) {
                $found_post = $this->getPost($post['post_id']);
                if($found_post){
                    $data_array[] = $found_post;
                }
        }

        return $data_array;
    }

    public function getRecentDrafts($limit = 20, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.drafts.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE deleted=0 " .
                " AND draft=1 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.drafts.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }

    public function getPostsByTypes($type_list = ['article'], $limit = 20, $skip = 0)
    {
        if ($this->session->data['is_owner']) {
            $post_id_array = $this->cache->get('posts.type.owner.' . implode('.', $type_list) . '.' . $skip . '.' . $limit);
            if (!$post_id_array) {
                // todo need to map this->db->escape
                $query = $this->db->query(
                    "SELECT post_id FROM " . DATABASE . ".posts " .
                    " WHERE post_type IN ('" . implode("','", $type_list) . "') " .
                    " AND draft=0 " .
                    " ORDER BY timestamp DESC " .
                    " LIMIT " . (int)$skip . ", " . (int)$limit
                );
                $post_id_array = $query->rows;
                $this->cache->set('posts.type.owner.' . implode('.', $type_list) . '.' . $skip . '.' . $limit, $post_id_array);
            }
        } else {
            $post_id_array = $this->cache->get('posts.type.' . implode('.', $type_list) . '.' . $skip . '.' . $limit);
            if (!$post_id_array) {
                // todo need to map this->db->escape
                $query = $this->db->query(
                    "SELECT post_id FROM " . DATABASE . ".posts " .
                    " WHERE post_type IN ('" . implode("','", $type_list) . "') " .
                    " AND deleted=0 " .
                    " AND draft=0 " .
                    " ORDER BY timestamp DESC " .
                    " LIMIT " . (int)$skip . ", " . (int)$limit
                );
                $post_id_array = $query->rows;
                $this->cache->set('posts.type.' . implode('.', $type_list) . '.' . $skip . '.' . $limit, $post_id_array);
            }

        }
        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }

    public function addToCategory($post_id, $category_name)
    {
        $this->load->model('blog/category');
        $category = $this->model_blog_category->getCategoryByName($category_name, true);
        $this->db->query(
            "INSERT INTO " . DATABASE . ".categories_posts " .
            " SET category_id=" . (int)$category['category_id'] . ", " .
            " post_id = " . (int)$post_id
        );
    }


    public function removeFromAllCategories($post_id)
    {
        $this->db->query(
            "DELETE FROM " . DATABASE . ".categories_posts " .
            " WHERE post_id = " . (int)$post_id
        );
        //todo find and remove empty categories
    }

    public function getPostsByCategory($category_id, $limit = 20, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.category.' . $category_id . '.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id FROM " . DATABASE . ".posts " .
                " JOIN " . DATABASE . ".categories_posts USING(post_id) " .
                " WHERE category_id = '" . (int)$category_id . "' " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.category.' . $category_id . '.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }

    public function getRecentPostsByType($type, $limit = 20, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.' . $type . '.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE post_type = '" . $this->db->escape($type) . "' " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.' . $type . '.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }

    public function getPostsByDay($year, $month, $day)
    {
        $post_id_array = $this->cache->get('posts.day.' . $year . '.' . $month . '.' . $day);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE category_id = '" . (int)$category_id . "' " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND `day` = " . (int)$day . " " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC"
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.day.' . $year . '.' . $month . '.' . $day, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }


    public function getPostsByArchive($type, $year, $month, $limit = 20, $skip = 0)
    {
        $post_id_array = $this->cache->get($type . '.date.' . $year . '.' . $month . '.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE post_type='" . $this->db->escape($type) . "' " .
                " AND `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set($type . '.date.' . $year . '.' . $month . '.' . $skip . '.' . $limit, $post_id_array);

        }
        
        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }

        return $data_array;
    }
    public function getAnyPostsByArchive($year, $month, $limit = null, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.date.' . $year . '.' . $month . '.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT post_id " .
                " FROM " . DATABASE . ".posts " .
                " WHERE `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND deleted=0 " .
                " AND draft=0 " .
                " ORDER BY timestamp DESC " .
                ($limit ? " LIMIT " . (int)$skip . ", " . (int)$limit : '')
            );
            $post_id_array = $query->rows;
            if (!$limit) {
                $limit = 'all';
            }
            $this->cache->set('posts.date.' . $year . '.' . $month . '.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['post_id']);
            if($found_post){
                $data_array[] = $found_post;
            }
        }
        return $data_array;
    }

    public function getSyndications($post_id)
    {

        $data = $this->cache->get('syndications.post.' . $post_id);
        if (!$data) {
            $query = $this->db->query(
                "SELECT * " .
                "FROM " . DATABASE . ".post_syndication " .
                " JOIN " . DATABASE . ".syndication_site USING(syndication_site_id) " .
                " WHERE post_id = " . (int)$post_id
            );

            $data = $query->rows;
            $this->cache->set('syndications.post.' . $post_id, $data);
        }
        return $data;
    }

    public function addSyndication($post_id, $syndication_url)
    {
        if (!empty($syndication_url)) {
            $syndication_url = trim($syndication_url);
            //figure out what site this is.
            $sites_query = $this->db->query(
                "SELECT * " .
                "FROM " . DATABASE . ".syndication_site "
            );
            $sites = $sites_query->rows;

            $syn_site_id = 0;
            foreach ($sites as $site) {
                if (strpos($syndication_url, $site['site_url_match']) === 0) {
                    $syn_site_id = $site['syndication_site_id'];
                    break;
                }
            }

            // add site to DB
            $query = $this->db->query(
                "INSERT INTO " . DATABASE . ".post_syndication " .
                " SET post_id = " . (int)$post_id . ", " .
                " syndication_site_id=" . (int)$syn_site_id . ", " .
                " syndication_url = '" . $this->db->escape($syndication_url) . "'"
            );

            $this->cache->delete('post.' . $post_id);
        }
    }

    public function getMediaForPost($post_id, $media_type){
            $query = $this->db->query(
                "SELECT * " .
                "FROM " . DATABASE . ".media " .
                " JOIN " . DATABASE . ".media_posts USING(media_id) " .
                " WHERE post_id = " . (int)$post_id .
                " and type = '" . $this->db->escape($media_type) . "'" 
            );
            return $query->rows;
    }

    public function deleteProperty($post_id, $field_name){
        switch ($field_name) {
            case 'category':
                //TODO
                break;
            case 'photo':
            case 'video':
            case 'audio':
                $query = $this->db->query(
                    "SELECT * " .
                    "FROM " . DATABASE . ".media " .
                    " JOIN " . DATABASE . ".media_posts USING(media_id) " .
                    " WHERE post_id = " . (int)$post_id .
                    " and type = '" . $this->db->escape($field_name) . "'" 
                );

                $old_media_ids = array();
                foreach($query->rows as $row){
                    $old_media_ids[] = (int)$row['media_id'];
                }
                if(!empty($old_media_ids)){
                    $ids_joined = implode(',', $old_media_ids);
                    $this->db->query(
                        "DELETE " .
                        "FROM " . DATABASE . ".media_posts " .
                        " WHERE media_id IN (" . $ids_joined . ")"
                    );
                    $this->db->query(
                        "DELETE " .
                        "FROM " . DATABASE . ".media " .
                        " WHERE media_id IN (" . $ids_joined . ")"
                    );
                }
                break;

            default:
                //TODO
        }
    }

    public function removePropertyValue($post_id, $field_name, $value){
        switch ($field_name) {
            case 'category':
                //TODO
                break;
            case 'photo':
            case 'video':
            case 'audio':
                $query = $this->db->query(
                    "SELECT * " .
                    "FROM " . DATABASE . ".media " .
                    " JOIN " . DATABASE . ".media_posts USING(media_id) " .
                    " WHERE post_id = " . (int)$post_id .
                    " and type = '" . $this->db->escape($field_name) . "'" 
                );

                foreach($query->rows as $row){
                    if($row['path'] == $value){
                        $this->db->query(
                            "DELETE " .
                            "FROM " . DATABASE . ".media_posts " .
                            " WHERE media_id = " . (int)$row['media_id']
                        );
                        $this->db->query(
                            "DELETE " .
                            "FROM " . DATABASE . ".media " .
                            " WHERE media_id = " . (int)$row['media_id']
                        );
                    }
                }
                break;
            default:
                $this->deleteProperty($post_id, $field_name);
        }
    }

    public function addProperty($post_id, $field_name, $value){
        switch ($field_name) {
            case 'category':
                //TODO
                break;
            case 'photo':
            case 'video':
            case 'audio':
                if(!empty($value)){
                    $this->addMediaToPost($post_id, $field_name, $value);
                }

                break;

            default:
                //TODO
        }
    }
    public function setProperty($post_id, $field_name, $value){

        $this->deleteProperty($post_id, $field_name);
        $this->addProperty($post_id, $field_name, $value);
    }

    private function isHash(array $in)
    {
        return is_array($in) && count(array_filter(array_keys($in), 'is_string')) > 0;
    }


}
