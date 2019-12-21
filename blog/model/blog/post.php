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
        //TODO this entire function seems like a bad idea
        //$this->log->write('called editPost');
        //$this->log->write(print_r($data,true));
        if (isset($data['id'])) {
            $set_data = array();

            if (isset($data['name']) && !empty($data['name'])) {
                $set_data[] = "name ='" . $this->db->escape($data['name']) . "'";
            } else {
                $set_data[] = "name =''";
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
                $set_data[] = "`like-of` ='" . $this->db->escape($data['like-of']) . "'";
            } else {
                $set_data[] = "`like-of` =''";
            }

            if (isset($data['bookmark-of']) && !empty($data['bookmark-of'])) {
                $set_data[] = "`bookmark-of` ='" . $this->db->escape($data['bookmark-of']) . "'";
            } else {
                $set_data[] = "`bookmark-of` =''";
            }

            //$this->log->write(print_r($set_data,true));
            //todo category
            //todo syndicate-to

            $sql = "UPDATE " . DB_DATABASE . ".posts SET " . implode(' , ', $set_data) . " WHERE id=" . (int)$data['id'];
            $this->db->query($sql);
            $this->cache->delete('post.' . $data['id']);
        }
    }
    public function newPost($type, $data)
    {
        $this->log->write(print_r($data, true));

        if (isset($data['published']) && !empty($data['published'])) {
            $year = date('Y', strtotime($data['published']));
            $month = date('n', strtotime($data['published']));
            $day = date('j', strtotime($data['published']));
            $published = "'" . $this->db->escape($data['published']) . "'";
        } else {
            $year = date('Y');
            $month = date('n');
            $day = date('j');
            $published = "NOW()";
        }

        $draft = 0;
        if (isset($data['draft']) && ($data['draft'] == 1 || $data['draft'] == '1')) {
            $draft = 1;
        }

        $query = $this->db->query("
            SELECT COALESCE(MAX(daycount), 0) + 1 AS newval
                FROM " . DB_DATABASE . ".posts 
                WHERE `year` = '" . $year . "'
                    AND `month` = '" . $month . "' 
                    AND `day` = '" . $day . "';");

        $newcount = $query->row['newval'];


        $weight = null;

        if(isset($data['weight_value']) && !empty($data['weight_value'])){
            $weight_obj = array('num' => $data['weight_value']);
            if(isset($data['weight_unit']) && !empty($data['weight_unit'])){
                $weight_obj['unit'] = $data['weight_unit'];
            } else {
                $weight_obj['unit'] = 'lbs';
            }
            $weight = json_encode($weight_obj);
        } elseif(isset($data['weight']) && !empty($data['weight'])){
            $weight = $data['weight'];
        }

        $location = null;
        if( (isset($data['location']) && !empty($data['location']))
            || (isset($data['place_name']) && !empty($data['place_name']))) {

            $location_card_obj = array();

            if (isset($data['location']) && !empty($data['location']) ) {
                if (preg_match('/^geo:/', trim($post['location']))) {
                    $location_card_obj['adr'] = array('geo' => $data['location']);
                    $joined_loc = str_replace('geo:', '', $post['location']);
                    $latlng = explode($joined_loc, ',');

                    $location_card_obj['latitude'] = $latlng[0];
                    $location_card_obj['longitude'] = $latlng[1];

                } else {
                    //ideally import this if possible
                    $location_card_obj['url'] = $data['location'];
                }

            }
            if ( isset($data['place_name']) && !empty($data['place_name'])) {
                $location_card_obj['name'] = $data['place_name'];
            }

        }



        $sql = "INSERT INTO " . DB_DATABASE . ".posts " .
            " SET `type`='" . $this->db->escape($type) . "', " .
            " `published` = " . $published . ", " .
            " `year` = " . (int)$year . ", " .
            " `month` = " . (int)$month . ", " .
            " `day` = " . (int)$day . ", " .
            " `draft` = " . (int)$draft . ", " .
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
            (isset($data['like-of']) && !empty($data['like-of'])
            ? ", `like-of`='" . $this->db->escape($data['like-of']) . "'"
                : "") .
            (isset($data['bookmark-of']) && !empty($data['bookmark-of'])
            ? ", `bookmark-of`='" . $this->db->escape($data['bookmark-of']) . "'"
                : "") .

            (isset($data['name']) && !empty($data['name'])
                ? ", name='" . $this->db->escape($data['name']) . "'"
                : "") .
            (isset($data['rsvp']) && !empty($data['rsvp'])
                ? ", rsvp='" . $this->db->escape($data['rsvp']) . "'"
                : "") .

            ($location ? "location='" . $location  . "' " : "") .
            ($weight ? "weight='" . $weight  . "' " : "") .

            (isset($data['created_by']) && !empty($data['created_by'])
                ? ", created_by='" . $this->db->escape($data['created_by']) . "'"
                : "");

        //$this->log->write($sql);
        $query = $this->db->query($sql);

        $post_id = $this->db->getLastId();

        if (isset($data['in-reply-to']) && !empty($data['in-reply-to']))  {
            $replys = explode(',', $data['in-reply-to']);
            foreach($replys as $replyto){
                $this->db->query(
                    "INSERT INTO " . DB_DATABASE . ".post_reply_to " .
                    " SET url='" . $this->db->escape($replyto) . "' " .
                    " post_id = " . (int)$post_id
                );
            }

        }

        if (isset($data['category']) && !empty($data['category'])) {
            $categories = explode(',', $data['category']);
            foreach ($categories as $cat) {
                $this->load->model('blog/category');
                $category = $this->model_blog_category->getCategoryByName($cat, true);
                $this->db->query(
                    "INSERT INTO " . DB_DATABASE . ".category_post " .
                    " SET category_id=" . (int)$category['id'] . ", " .
                    " post_id = " . (int)$post_id
                );
            }
            $this->cache->delete('categories.post.' . $post_id);
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
            $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".media " .
                " SET path='" . $this->db->escape($media_data['value']) . "'" .
                " , type='".$media_type."'" .
                (isset($media_data['alt']) ? ', alt="'.$this->db->escape($media_data['alt']).'"' : '')
            );
            $media_id = $this->db->getLastId();
            $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".media_post " .
                " SET media_id =". (int)$media_id .
                " , post_id =". (int)$post_id 
            );
        } elseif(is_array($media_data)){
            foreach($media_data as $media_obj){
                $this->addMediaToPost($post_id, $media_type, $media_obj);
            } 
        } else {

            $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".media " .
                " SET path='" . $this->db->escape($media_data) . "'" .
                " , type='".$media_type."'" 
            );
            $media_id = $this->db->getLastId();
            $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".media_post " .
                " SET media_id =". (int)$media_id .
                " , post_id =". (int)$post_id 
            );
        }
    }

    public function setSyndicationExtra($post_id, $syn_extra_val)
    {
        $this->db->query("UPDATE " . DB_DATABASE . ".posts " .
            " SET syndication_extra='" . $this->db->escape($syn_extra_val) . "' " .
            " WHERE post_id = " . (int)$post_id);
    }

    public function deletePost($post_id)
    {
        $sql = "UPDATE " . DB_DATABASE . ".posts " .
            " SET `deleted_at`= NOW() " .
            " WHERE id = " . (int)$post_id;
        $this->db->query($sql);
        $this->cache->delete('post.' . $post_id);
        $this->cache->delete('posts');
    }

    public function undeletePost($post_id)
    {
        $sql = "UPDATE " . DB_DATABASE . ".posts " .
            " SET `deleted_at` = NULL " .
            " WHERE id = " . (int)$post_id;
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
                    case 'permalink';
                        $mf2_post['properties']['url'] = $value;
                        break;
                    case 'created';
                    case 'published';
                    case 'updated';
                        $mf2_post['properties']['created'] = $value;
                        break;

                    case 'in-reply-to':
                        $mf2_post['properties']['in-reply-to'] = explode(',', $value); //TODO this is not correct mf2 i don't think
                        break;
                    case 'weight':
                        $mf2_post['properties']['weight'] = json_encode($post['weight']); 
                    case 'slug':
                    case 'summary':
                    case 'content':
                    case 'name':
                    case 'like-of':
                    case 'bookmark-of':
                        break;

                    case 'draft':
                    case 'artist':
                    case 'deleted_at':


                        /*
                'rsvp'
                'created_by'
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

        $fields_supported = array('name', 'like-of', 'bookmark-of', 'category', 'description');


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
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE id = " . (int)$post_id
            );
            $post = $query->row;
            if(empty($post)){
                return null;
            }
            $syndications = $this->getSyndications($post_id);
            $shortlink = $this->short_url->link(
                'common/shortener',
                'eid=' . $this->numToSxg($post_id),
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
                    'type=' . $post['type'] . '&' .
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
                " FROM " . DB_DATABASE . ".post_reply_to " .
                " WHERE post_id = " . (int)$post_id
            );
            $reply_tos = array();
            foreach($query->rows as $row){
                $reply_tos[] = $row['url'];
            }
            $post['in-reply-to'] = implode(', ', $reply_tos);

            $post['weight'] = json_decode($post['weight'], true);
            $post['location'] = json_decode($post['location'], true);

            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DB_DATABASE . ".post_access " .
                " WHERE post_id = " . (int)$post_id
            );
            $acls = $query->rows;

            if(!empty($acls)){
                $post['public'] = false;
                $post['access'] = $acls;
            }

            //$post['name'] = $post['title'];

            //if ($post['type'] == 'article' && preg_match('/<hr \/>/', $post['content'])) {
                //$post['summary'] = preg_replace('/<hr \/>.*/s', '', $post['content']);
                //$post['content'] = preg_replace('/<hr \/>/', '', $post['content']);
            //}
            date_default_timezone_set(LOCALTIMEZONE);
            $post['published'] = date("c", strtotime($post['published']));
            if ($post['type'] == 'bookmark') {
                $post['description'] = $post['content'];
            }
            $this->cache->set('post.' . $post_id, $post);
        }
        if (isset($this->session->data['is_owner']) && $this->session->data['is_owner']) {
            return $post;

        } elseif(!$post['public']){
            if(!isset($this->session->data['person_id'])){
                return null;
            }
            $logged_in_person_id = $this->session->data['person_id'];

            $allow_view = false;
            //$this->log->write('debug 1 ' .  print_r($post['access'], true));
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE year = '" . (int)$year . "' " .
                " AND month = '" . (int)$month . "' " .
                " AND day = '" . (int)$day . "' " .
                " AND daycount = '" . (int)$daycount . "'"
            );
            $post_id = $query->row['id'];
            $this->cache->set('post_id.' . $year . '.' . $month . '.' . $day . '.' . $daycount, $post_id);
        }

        return $this->getPost($post_id);
    }

    public function getRecentPosts($limit = 10, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.recent.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.recent.' . $skip . '.' . $limit, $post_id_array);
        }

            $data_array = array();
        foreach ($post_id_array as $post) {
                $found_post = $this->getPost($post['id']);
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE deleted_at is null " .
                " AND draft=1 " .
                " ORDER BY published DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.drafts.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
                    "SELECT id FROM " . DB_DATABASE . ".posts " .
                    " WHERE `type` IN ('" . implode("','", $type_list) . "') " .
                    " AND draft=0 " .
                    " ORDER BY published DESC " .
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
                    "SELECT id FROM " . DB_DATABASE . ".posts " .
                    " WHERE `type` IN ('" . implode("','", $type_list) . "') " .
                    " AND deleted_at is null " .
                    " AND draft=0 " .
                    " ORDER BY published DESC " .
                    " LIMIT " . (int)$skip . ", " . (int)$limit
                );
                $post_id_array = $query->rows;
                $this->cache->set('posts.type.' . implode('.', $type_list) . '.' . $skip . '.' . $limit, $post_id_array);
            }

        }
        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
            "INSERT INTO " . DB_DATABASE . ".category_post " .
            " SET category_id=" . (int)$category['id'] . ", " .
            " post_id = " . (int)$post_id
        );
        $this->cache->delete('categories.post.' . $post_id);
    }


    public function removeFromAllCategories($post_id)
    {
        $this->db->query(
            "DELETE FROM " . DB_DATABASE . ".category_post " .
            " WHERE post_id = " . (int)$post_id
        );
        $this->cache->delete('categories.post.' . $post_id);
        //todo find and remove empty categories
    }

    public function getPostsByCategory($category_id, $limit = 20, $skip = 0)
    {
        $post_id_array = $this->cache->get('posts.category.' . $category_id . '.' . $skip . '.' . $limit);
        if (!$post_id_array) {
            $query = $this->db->query(
                "SELECT id FROM " . DB_DATABASE . ".posts " .
                " JOIN " . DB_DATABASE . ".category_post on category_post.post_id = posts.id " .
                " WHERE category_id = '" . (int)$category_id . "' " .
                " AND deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.category.' . $category_id . '.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE `type` = '" . $this->db->escape($type) . "' " .
                " AND deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.' . $type . '.' . $skip . '.' . $limit, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND `day` = " . (int)$day . " " .
                " AND deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC"
            );
            $post_id_array = $query->rows;
            $this->cache->set('posts.day.' . $year . '.' . $month . '.' . $day, $post_id_array);
        }

        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE `type`='" . $this->db->escape($type) . "' " .
                " AND `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC " .
                " LIMIT " . (int)$skip . ", " . (int)$limit
            );
            $post_id_array = $query->rows;
            $this->cache->set($type . '.date.' . $year . '.' . $month . '.' . $skip . '.' . $limit, $post_id_array);

        }
        
        $data_array = array();
        foreach ($post_id_array as $post) {
            $found_post = $this->getPost($post['id']);
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
                "SELECT id " .
                " FROM " . DB_DATABASE . ".posts " .
                " WHERE `year` = '" . (int)$year . "' " .
                " AND `month` = '" . (int)$month . "' " .
                " AND deleted_at is null " .
                " AND draft=0 " .
                " ORDER BY published DESC " .
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
            $found_post = $this->getPost($post['id']);
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
                "FROM " . DB_DATABASE . ".post_syndication " .
                " JOIN " . DB_DATABASE . ".syndication_sites ON post_syndication.syndication_site_id = syndication_sites.id" .
                " WHERE post_id = " . (int)$post_id
            );

            $data = $query->rows;
            $this->cache->set('syndications.post.' . $post_id, $data);
        }
        return $data;
    }

    public function addSyndication($post_id, $url)
    {
        if (!empty($url)) {
            $url = trim($url);
            //figure out what site this is.
            $sites_query = $this->db->query(
                "SELECT * " .
                "FROM " . DB_DATABASE . ".syndication_sites "
            );
            $sites = $sites_query->rows;

            $syn_site_id = 0;
            foreach ($sites as $site) {
                if (strpos($url, $site['url_match']) === 0) {
                    $syn_site_id = $site['id'];
                    break;
                }
            }

            // add site to DB
            $query = $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".post_syndication " .
                " SET post_id = " . (int)$post_id . ", " .
                " syndication_site_id=" . (int)$syn_site_id . ", " .
                " url = '" . $this->db->escape($url) . "'"
            );

            $this->cache->delete('post.' . $post_id);
        }
    }

    public function getMediaForPost($post_id, $media_type){
            $query = $this->db->query(
                "SELECT * " .
                "FROM " . DB_DATABASE . ".media " .
                " JOIN " . DB_DATABASE . ".media_post on media.id = media_post.media_id " .
                " WHERE post_id = " . (int)$post_id .
                " and type = '" . $this->db->escape($media_type) . "'" 
            );
            return $query->rows;
    }

    public function deleteProperty($post_id, $field_name){
        $this->log->write('called deleteProperty with ' . $post_id . ' ' . $field_name );
        switch ($field_name) {
            case 'category':
                $this->db->query(
                    "DELETE FROM " . DB_DATABASE . ".category_post " .
                    " WHERE post_id = " . (int)$post_id
                );
                $this->cache->delete('categories.post.' . $post_id);
                break;
            case 'photo':
            case 'video':
            case 'audio':
                $query = $this->db->query(
                    "SELECT * " .
                    "FROM " . DB_DATABASE . ".media " .
                    " JOIN " . DB_DATABASE . ".media_post on media.id = media_post.media_id " .
                    " WHERE post_id = " . (int)$post_id .
                    " and type = '" . $this->db->escape($field_name) . "'" 
                );

                $old_media_ids = array();
                foreach($query->rows as $row){
                    $old_media_ids[] = (int)$row['id'];
                }
                if(!empty($old_media_ids)){
                    $ids_joined = implode(',', $old_media_ids);
                    $this->db->query(
                        "DELETE " .
                        "FROM " . DB_DATABASE . ".media_post " .
                        " WHERE media_id IN (" . $ids_joined . ")"
                    );
                    $this->db->query(
                        "DELETE " .
                        "FROM " . DB_DATABASE . ".media " .
                        " WHERE media_id IN (" . $ids_joined . ")"
                    );
                }
                break;
            case 'in-reply-to':
                $this->db->query(
                    "DELETE " .
                    "FROM " . DB_DATABASE . ".post_reply_to " .
                    " WHERE post_id = " . (int)$post_id 
                );

                break;
            case 'like-of':
            case 'bookmark-of':
            case 'created':
            case 'published':
            case 'updated':
            case 'slug':
            case 'summary':
            case 'content':
            case 'name':
            case 'draft':
            case 'artist':
            case 'deleted_at':
            case 'rsvp':
            case 'location':
            case 'created_by':
            case 'weight':

                if(is_array($value)){
                    $value = $value[0];
                }
                $this->log->write(
                    "UPDATE " . DB_DATABASE . ".posts " .
                    " SET `".$field_name."` = NULL " . //can i do this generally?
                    " WHERE id = " . (int)$post_id
                );
                $this->db->query(
                    "UPDATE " . DB_DATABASE . ".posts " .
                    " SET `".$field_name."`= NULL " .//can i do this generally?
                    " WHERE id = " . (int)$post_id
                );
                break;

            default:
                // do nothing i guess as we don't support that field
        }
        $this->cache->delete('post.' . $post_id);
    }

    //This assumes that value is an array (as it should be per the micropub spec)
    public function removePropertyValues($post_id, $field_name, $value){
        $this->log->write('called removePropertyValues with ' . $post_id . ' ' . $field_name . ' ' . $value[0]);
        switch ($field_name) {
            case 'category':
                foreach($value as $cat){
                    $this->load->model('blog/category');
                    $category = $this->model_blog_category->getCategoryByName($cat, true);
                    $this->db->query(
                        "DELETE FROM " . DB_DATABASE . ".category_post " .
                        " WHERE category_id=" . (int)$category['id'] . " " .
                        " AND post_id = " . (int)$post_id
                    );
                }
                $this->cache->delete('categories.post.' . $post_id);

                break;
            case 'photo':
            case 'video':
            case 'audio':
                $query = $this->db->query(
                    "SELECT * " .
                    "FROM " . DB_DATABASE . ".media " .
                    " JOIN " . DB_DATABASE . ".media_post on media_post.media_id = media.id " .
                    " WHERE post_id = " . (int)$post_id .
                    " and type = '" . $this->db->escape($field_name) . "'" 
                );

                foreach($query->rows as $row){
                    if(in_array($row['path'], $value)){
                        $this->db->query(
                            "DELETE " .
                            "FROM " . DB_DATABASE . ".media_post " .
                            " WHERE media_id = " . (int)$row['id']
                        );
                        $this->db->query(
                            "DELETE " .
                            "FROM " . DB_DATABASE . ".media " .
                            " WHERE id = " . (int)$row['id']
                        );
                    }
                }
                break;
            default:
                $this->deleteProperty($post_id, $field_name);
        }
        $this->cache->delete('post.' . $post_id);
    }

    public function addProperty($post_id, $field_name, $value){
        $this->log->write('called addProperty with ' . $post_id . ' ' . $field_name . ' ' . $value[0]);
        switch ($field_name) {
            case 'category':
                $this->load->model('blog/category');
                if(is_array($value)){
                    foreach ($value as $cat) {
                        $category = $this->model_blog_category->getCategoryByName($cat, true);
                        $this->db->query(
                            "INSERT INTO " . DB_DATABASE . ".category_post " .
                            " SET category_id=" . (int)$category['id'] . ", " .
                            " post_id = " . (int)$post_id
                        );
                    }
                } else {
                    $category = $this->model_blog_category->getCategoryByName($value, true);
                    $this->db->query(
                        "INSERT INTO " . DB_DATABASE . ".category_post " .
                        " SET category_id=" . (int)$category['id'] . ", " .
                        " post_id = " . (int)$post_id
                    );

                }
                $this->cache->delete('categories.post.' . $post_id);
                break;
            case 'photo':
            case 'video':
            case 'audio':
                if(!empty($value)){
                    $this->addMediaToPost($post_id, $field_name, $value);
                }

                break;
            case 'in-reply-to':

                if(!empty($value)){

                    $reply_tos = explode(',', $value);

                    foreach($reply_tos as $repl){
                        $query = $this->db->query(
                            "INSERT INTO  " . DB_DATABASE . ".post_reply_to " .
                            " SET post_id = " . (int)$post_id  . " " .
                            " , url = '" . $this->db->escape($repl) . "' " 
                        );
                    }

                }
                break;

            case 'like-of':
            case 'bookmark-of':
            case 'created':
            case 'published':
            case 'updated':
            case 'slug':
            case 'summary':
            case 'content':
            case 'name':
            case 'draft':
            case 'artist':
            case 'deleted_at':
            case 'rsvp':
            case 'location':
            case 'created_by':
            case 'weight':

                if(is_array($value)){
                    $value = $value[0];
                }
                $this->log->write(
                    "UPDATE " . DB_DATABASE . ".posts " .
                    " SET `".$field_name."`='" . $this->db->escape($value) . "' " .
                    " WHERE id = " . (int)$post_id
                );
                $this->db->query(
                    "UPDATE " . DB_DATABASE . ".posts " .
                    " SET `".$field_name."`='" . $this->db->escape($value) . "' " .
                    " WHERE id = " . (int)$post_id
                );
                break;

            default:
                // do nothing as we don't have that field i guess
        }
        $this->cache->delete('post.' . $post_id);
    }
    public function setProperty($post_id, $field_name, $value){

        $this->log->write('called setProperty with ' . $post_id . ' ' . $field_name . ' ' . $value[0]);
        $this->deleteProperty($post_id, $field_name);
        $this->addProperty($post_id, $field_name, $value);
    }

    private function isHash(array $in)
    {
        return is_array($in) && count(array_filter(array_keys($in), 'is_string')) > 0;
    }


}
