<?php

require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';

require_once DIR_BASE . 'libraries/php-comments/src/indieweb/comments.php';
require_once DIR_BASE . 'libraries/cassis/cassis-loader.php';
require_once DIR_BASE . 'libraries/php-mf2-shim/Mf2/functions.php';
require_once DIR_BASE . 'libraries/php-mf2-shim/Mf2/Shim/Twitter.php';
//require_once DIR_BASE.'libraries/php-mf2-shim/Mf2/Shim/Facebook.php';

class ModelBlogContext extends Model {

    public function getImmediateContextForPost($post_id)
    {
        $data = $this->cache->get('context.immediate.post.' . $post_id);
        if (!$data) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".context " .
                " JOIN " . DATABASE . ".post_context USING(context_id) " .
                " WHERE post_id = " . (int)$post_id
            );
            $data = $query->rows;
            $this->cache->set('context.immediate.post.' . $post_id, $data);
        }

        return $data;
    }

    public function getAllContextForPost($post_id)
    {
        $data = $this->cache->get('context.all.post.' . $post_id);
        if (!$data) {
            $ids = array();
            $query = $this->db->query("SELECT context_id " .
                " FROM " . DATABASE . ".post_context " .
                " WHERE post_id = " . (int)$post_id);

            foreach ($query->rows as $toAdd) {
                if (!in_array((int)$toAdd['context_id'], $ids)) {
                    $ids[] = (int)$toAdd['context_id'];
                }
            }

            $prev = 0;
            while (count($ids) > $prev) {
                $prev = count($ids);

                $query = $this->db->query("SELECT parent_context_id AS context_id " .
                    " FROM " . DATABASE . ".context_to_context " .
                    " WHERE context_id in (" . implode(',', $ids) . ")");
                foreach ($query->rows as $toAdd) {
                    if (!in_array((int)$toAdd['context_id'], $ids)) {
                        $ids[] = (int)$toAdd['context_id'];
                    }
                }
            }

            $query = $this->db->query("SELECT * " .
                " FROM " . DATABASE . ".context " .
                " WHERE context_id in (" . implode(',', $ids) . ") " .
                " ORDER BY `timestamp` ASC");
            $data = $query->rows;

            date_default_timezone_set(LOCALTIMEZONE);
            foreach($data as &$row){
                $row['timestamp'] = date("c", strtotime($row['timestamp']));
            }

            $this->cache->set('context.all.post.' . $post_id, $data);
        }
        return $data;
    }

    private function getContextId($source_url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $source_url);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        $page_content = curl_exec($c);
        curl_close($c);
        unset($c);

        if ($page_content !== false) {
            $mf2_parsed = Mf2\parse($page_content, $real_source_url);
            $source_data = IndieWeb\comments\parse($mf2_parsed['items'][0], false, 300);
            if (empty($source_data['url'])) {
                $mf2_parsed = Mf2\Shim\parseTwitter($page_content, $real_source_url);
                $source_data = IndieWeb\comments\parse($mf2_parsed['items'][0], false, 300);
            }
            //if(empty($source_data['url'])){
                //$mf2_parsed = Mf2\Shim\parseFacebook($page_content, $real_source_url);
                //$source_data = IndieWeb\comments\parse($mf2_parsed['items'][0]);
            //}
            if (empty($source_data['url'])) {
                return null;
            }


            $real_url = $source_data['url'];

            $query = $this->db->query("SELECT * " .
                " FROM " . DATABASE . ".context " .
                " WHERE source_url='" . $this->db->escape($real_url) . "' " .
                " LIMIT 1");

            if (!empty($query->row)) {
                return $query->row['context_id'];

            } else {
                $published = $source_data['published'];
                $body = $source_data['text'];
                $source_name = $source_data['name'];

                $author_name = $source_data['author']['name'];
                $author_url = $source_data['author']['url'];
                $author_image = $source_data['author']['photo'];


                // do our best to conver to local time
                date_default_timezone_set(LOCALTIMEZONE);
                $date = new DateTime($published);
                $now = new DateTime;
                $tz = $now->getTimezone();
                $date->setTimezone($tz);
                $published = $date->format('Y-m-d H:i:s') . "\n";


                if (empty($real_url)) {
                    return null;
                }

                $this->db->query("INSERT INTO " . DATABASE . ".context SET 
                    author_name = '" . $this->db->escape($author_name) . "',
                    author_url = '" . $this->db->escape($author_url) . "',
                    author_image = '" . $this->db->escape($author_image) . "',
                    source_name = '" . $this->db->escape($source_name) . "',
                    source_url = '" . $this->db->escape($real_url) . "',
                    body = '" . $this->db->escape($body) . "',
                    timestamp ='" . $published . "'");

                $context_id = $this->db->getLastId();

                foreach ($mf2_parsed['items'][0]['properties']['in-reply-to'] as $citation) {
                    if (isset($citation['properties'])) {
                        foreach ($citation['properties']['url'] as $reply_to_url) {
                            $ctx_id = $this->getContextId($reply_to_url);
                            if ($ctx_id) {
                                $this->db->query("INSERT INTO " . DATABASE . ".context_to_context SET 
                                context_id = " . (int)$context_id . ",
                                parent_context_id = " . (int)$ctx_id);
                            }

                        }
                    } else {
                        $reply_to_url = $citation;

                        $ctx_id = $this->getContextId($reply_to_url);
                        if ($ctx_id) {
                            $this->db->query("INSERT INTO " . DATABASE . ".context_to_context SET 
                            context_id = " . (int)$context_id . ",
                            parent_context_id = " . (int)$ctx_id);
                        }
                    }

                }
                return $context_id;
            }
        } else {
            return null;
        }
    }

    public function processContexts()
    {
        $result = $this->db->query("SELECT * " .
            " FROM " . DATABASE . ".posts " .
            " WHERE NOT replyto is NULL AND context_parsed=0 " .
            " LIMIT 1");
        $post = $result->row;

        while ($post) {
            //immediately update this to say that it is parsed.. this way we don't end up trying to run it multiple times on the same post
            $this->db->query(
                "UPDATE " . DATABASE . ".posts SET context_parsed = 1 " .
                " WHERE post_id = " . (int)$post_id
            );

            $source_url = trim($post['replyto']); //todo want to support multiples

            $post_id = $post['post_id'];
            $context_id = $this->getContextId($source_url);

            if ($context_id) {
                $this->db->query(
                    "INSERT INTO " . DATABASE . ".post_context " .
                    " SET post_id = " . (int)$post_id . ", " .
                    " context_id = " . (int)$context_id
                );
            }


            $result = $this->db->query("SELECT * " .
                " FROM " . DATABASE . ".posts " .
                " WHERE NOT replyto is NULL AND context_parsed=0 " .
                " LIMIT 1");
            $post = $result->row;

        } //end while($post) loop
        $cache->delete('context');
    }


    /*
    //recursively called function, foreach has this nice little ability to handle our base case for us.
    //  When there are no parents, we return an empty array.  The previous run will test for the empty array and not set a parents value
    private function getAllContextForContext($context_id) {
        $query = $this->db->query("SELECT c.* " .
            " FROM " . DATABASE . ".context c " .
            " JOIN ".DATABASE.".context_to_context ctc ON c.context_id = ctc.parent_context_id " .
            " WHERE ctc.context_id = ".(int)$context_id
        );
        $data = array();
        foreach($query->rows as $context){
            $parents = $this->getAllContextForContext($context['context_id']);
            if(!empty($parents)){
                $context['parents'] = $parents;
            }
            $data[] = $context;
        }
        return $data;

    }
    */

    public function getSyndications($context_id)
    {

        $data = $this->cache->get('syndications.context.' . $context_id);
        if (!$data) {
            $query = $this->db->query("SELECT * " .
                " FROM " . DATABASE . ".context_syndication " .
                " JOIN " . DATABASE . ".syndication_site USING(syndication_site_id) " .
                " WHERE context_id = " . (int)$context_id);

            $data = $query->rows;
            $this->cache->set('syndications.context.' . $context_id, $data);
        }
        return $data;
    }

}

