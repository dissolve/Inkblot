<?php
class ModelBlogInteraction extends Model {

    public function addWebmention($data, $webmention_id, $comment_data, $post_id = null){

        //check if the source of this is already parsed
        $query = $this->db->query("SELECT * FROM ".DATABASE.".interaction_syndication WHERE syndication_url='".$comment_data['url']."' LIMIT 1");
        if(!empty($query->row)){
            $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '200', webmention_status = 'duplicate' WHERE webmention_id = ". (int)$webmention_id);
            return null;
        }

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
            $this->load->model('blog/post');
            $post = null;
            if($post_id){
                $post= $this->model_blog_post->getPost($post_id);
            } else {
                $post = $this->model_blog_post->getPostByDayCount($data['year'],$data['month'], $data['day'], $data['daycount']);
            }

            $interaction_type = 'mention';

            switch($comment_data['type']) {
            case 'like':
                $interaction_type = 'like';
                break;
            case 'reply':
                $interaction_type = 'reply';
                break;
            case 'repost':
                $interaction_type = 'repost';
                break;
            case 'tag':
                $interaction_type = 'tag';
                break;
            case 'rsvp':
                $interaction_type = 'rsvp';
                break;
            case 'mention':
            default:
                $query = $this->db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_id = ".(int)$webmention_id." LIMIT 1");
                $mention_record = $query->row;
                if($mention_record['target_url'] == HTTP_SERVER ||
                        $mention_record['target_url'] == HTTPS_SERVER ||
                        $mention_record['target_url'] == HTTP_SHORT ||
                        $mention_record['target_url'] == HTTPS_SHORT ){
                    $interaction_type = 'person-mention';
                }
                break;
            }

            //TODO: move this to some config setting
            $autoapprove = true;

            $this->db->query("INSERT INTO ". DATABASE.".interactions SET source_url = '".$comment_data['url']."'".
                    ((isset($comment_data['author']) && isset($comment_data['author']['name']) && !empty($comment_data['author']['name']))? ", author_name='".$comment_data['author']['name']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['url']) && !empty($comment_data['author']['url']))? ", author_url='".$comment_data['author']['url']."'" : "") .
                    ((isset($comment_data['author']) && isset($comment_data['author']['photo']) && !empty($comment_data['author']['photo']))? ", author_image='".$comment_data['author']['photo']."'" : "") .
                    ((isset($comment_data['tag-of']) && !empty($comment_data['tag-of']))? ", tag_of='".$comment_data['tag-of']."'" : "") .
                    ((isset($comment_data['text'])  && !empty($comment_data['text']))? ", body='".$this->db->escape($comment_data['text'])."'" : "") .
                    ((isset($comment_data['name'])  && !empty($comment_data['name']))? ", source_name='".$this->db->escape($comment_data['name'])."'" : "") .
                    ((isset($comment_data['published'])  && !empty($comment_data['published']))? ", `timestamp`='".$this->db->escape($comment_data['published'])."'" : ", `timestamp`=NOW()") .
                    ", webmention_id='".$webmention_id."'".
                    ", interaction_type='".$interaction_type."'".
                            ", post_id = ".(int)$post['post_id'].
                    ", parse_timestamp = NOW()".
                    ($autoapprove ? ", approved=1" : '' ) .
            "");

            $interaction_id = $this->db->getLastId();

            $syndication_sites = $this->cache->get('syndication.sites');
            if(!$syndication_sites){
                $syn_site_query = $this->db->query("SELECT * FROM ".DATABASE.".syndication_site");
                $syndication_sites = $syn_site_query->rows;
                $this->cache->set('syndication.sites',$syndication_sites); 
            }
            if(isset($comment_data['syndications'])){

                foreach($comment_data['syndications'] as $syndication_url){

                    // figure out what syndicaiton_site_id to use
                    foreach($syndication_sites as $possible_site){
                        if(strpos($syndication_url, $possible_site['site_url_match']) === 0){
                            $syn_site_id = $possible_site['syndication_site_id'];
                        }
                    }

                    $this->db->query("INSERT INTO ". DATABASE.".interaction_syndication 
                        SET syndication_url = '".$this->db->escape($syndication_url)."',
                        ". (isset($syn_site_id) ? "syndication_site_id = ".(int)$syn_site_id . ", " : "" ) . "
                            interaction_id = ".(int)$interaction_id);

                    //remove any syndicated copies we have already parsed
                    $query = $this->db->query("SELECT * FROM ".DATABASE.".interaction WHERE source_url='".$this->db->escape($syndication_url)."' LIMIT 1");
                    if(!empty($query->row)){
                        $this->db->query("DELETE FROM ".DATABASE.".interaction WHERE source_url='".$this->db->escape($syndication_url)."' LIMIT 1");
                    }
                }
            }

            if($autoapprove && $type=='tag'){
                foreach($comment_data['tags'] as $tag){
                    if(isset($tag['category'])){
                        $this->model_blog_post->addToCategory($post['post_id'], $tag['category']);
                    } elseif(isset($tag['url'])){
                        $this->model_blog_post->addToCategory($post['post_id'], $tag['url']);
                    }
                }

            }

            //TODO: add $comment_data['comments'] to 

            $this->db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
            $this->cache->delete('interactions');

            return $interaction_id;

        } else {
            throw new Exception('Cannot look up record');
            //throwing an exception will go back to calling script and run the generic add
        }
    }

    public function editWebmention($data, $webmention_id, $comment_data, $post_id = null){

        $query = $this->db->query("SELECT webmention_id, interactions.* FROM ". DATABASE.".webmentions JOIN ".DATABASE.".interactions USING(webmention_id) WHERE webmention_id = ".(int)$webmention_id." LIMIT 1");
        $webmention = $query->row;
        if($webmention_id){
            $this->db->query("UPDATE ".DATABASE.".interactions SET deleted=1 WHERE webmention_id = ".(int)$webmention_id);
            $new_interaction_id = $this->addWebmention($data, $webmention_id, $comment_data, $post_id);
            $this->db->query("UPDATE ".DATABASE.".webmentions SET webmention_status='Updated' WHERE webmention_id = ".(int)$webmention_id);
            return $new_interaction_id;
        }
    
    }



//below this has been upgraded to Interactions methods

    public function getGenericInteractions($type, $limit=100, $skip=0) {
        $data = $this->cache->get('interactions.'.$type.'.generic.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".interactions WHERE interaction_type='".$type."' AND post_id IS NULL AND deleted=0 ORDER BY timestamp ASC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('interactions.'.$type.'.generic.'. $skip . '.' .$limit, $data);
        }
    return $data;
    }

    public function getGenericInteractionCount($type) {
        $data = $this->cache->get('interactions.'.$type.'.generic.count');
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".interactions WHERE interaction_type='".$type."' AND post_id IS NULL AND deleted=0");
            $data = $query->row['total'];
            $this->cache->set('interactions.'.$type.'.generic.count', $data);
        }
    return $data;
    }

    public function getInteractionsForPost($type, $post_id, $limit=100, $skip=0) {
    //correct my vocabulary
    if($type == 'comment'){
        $type = 'reply';
    }

        $data = $this->cache->get('interactions.'.$type.'.post.'.$post_id.'.'. $skip . '.'.  $limit);
        if(!$data){
            $query = $this->db->query("SELECT interactions.*, webmentions.vouch_url FROM " . DATABASE . ".interactions JOIN " . DATABASE . ".webmentions USING(webmention_id) WHERE interaction_type='".$type."' AND post_id = ".(int)$post_id." AND deleted=0 ORDER BY timestamp ASC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = $query->rows;
            $this->cache->set('interactions.'.$type.'.post.'.$post_id.'.'. $skip . '.' .$limit, $data);
        }
        return $data;
    }

    public function getInteractionCountForPost($type, $post_id) {
        $data = $this->cache->get('interactions.'.$type.'.post.count.'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DATABASE . ".interactions WHERE interaction_type='".$type."' AND post_id = ".(int)$post_id." AND deleted=0");
            $data = $query->row['total'];
            $this->cache->set('interactions.'.$type.'.post.count.'.$post_id, $data);
        }
        return $data;
    }

    public function getSyndications($interaction_id) {

        $data = $this->cache->get('syndications.interaction.'.$interaction_id);
        if(!$data){
            $query = $this->db->query("SELECT * FROM ".DATABASE.".interaction_syndication JOIN ".DATABASE.".syndication_site USING(syndication_site_id) WHERE interaction_id = ".(int)$interaction_id);

            $data = $query->rows;
            $this->cache->set('syndications.interaction.'.$interaction_id, $data);
        }
        return $data;
    }

    public function getRecentInteractions($limit = 20, $skip = 0){

        $data = $this->cache->get('interactions.recent.post.'. $skip . '.'.  $limit);
        if(!$data){
            $this->load->model('blog/post');

            $query = $this->db->query("SELECT interactions.*, webmentions.vouch_url, webmentions.target_url FROM " . DATABASE . ".interactions JOIN " . DATABASE . ".webmentions USING(webmention_id) WHERE deleted=0 ORDER BY timestamp DESC LIMIT ". (int)$skip . ", " . (int)$limit);
            $data = array();
            foreach($query->rows as $row){
                $data[] = array_merge($row, array(
                    'post' => $this->model_blog_post->getPost($row['post_id'])
                ));
            }
            $this->cache->set('interactions.recent.'. $skip . '.' .$limit, $data);
        }
        return $data;
    }

}
