<?php
class ModelBlogCategory extends Model {
    require_once DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php';
    //require_once DIR_BASE.'libraries/cassis/cassis-loader.php';
    //require_once DIR_BASE.'libraries/php-mf2-shim/Mf2/functions.php';
    //require_once DIR_BASE.'libraries/php-mf2-shim/Mf2/Shim/Twitter.php';

    public function getCategories($min = 1)
    {
        $data = $this->cache->get('categories.all.' . $min);
        if (!$data) {
            $query = $this->db->query(
                "SELECT categories.* 
                FROM " . DATABASE . ".categories 
                JOIN " . DATABASE . ".categories_posts USING (category_id)
                GROUP BY category_id 
                HAVING count(post_id) >= " . (int)$min . "
                ORDER BY name;"
            );

            $data = $query->rows;
            $data_array = array();
            foreach ($data as $category) {
                $data_array[] = array_merge($category, array(
                    'permalink' => $this->url->link('information/category', 'name=' . urlencode($category['name']), '')
                ));
            }
            $this->cache->set('categories.all.' . $min, $data_array);
        } else {
            $data_array = $data;
        }

        return $data_array;
    }



    //note that we intentionally ignore cateogy_id = 0 for this list
    // this entry is the uncategoriezed entry and thus should not show at all

    public function getCategoriesForPost($post_id)
    {
        $data = $this->cache->get('categories.post' . $post_id);
        if (!$data) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DATABASE . ".categories " .
                " JOIN " . DATABASE . ".categories_posts USING(category_id) " .
                " WHERE post_id = '" . (int)$post_id . "' " .
                " AND NOT category_id = 0 " .
                " ORDER BY name ASC"
            );
            $data = $query->rows;

            $data_array = array();
            foreach ($data as $category) {
                $data_array[] = array_merge($category, array(
                    'permalink' => $this->url->link('information/category', 'name=' . $this->db->escape($category['name']), '')
                ));
            }
            $this->cache->set('categories.post.' . $post_id, $data_array);
        } else {
            $data_array = $data;
        }

        return $data_array;
    }

    public function getCategoryByName($name, $autocreate = false)
    {
        $name = trim($name);

        $cid = $this->findCategoryByName($name);

        if ($autocreate && !$cid) {
            $cid = $this->addCategory($name);
        }

        if (!$cid) {
            return null;
        }

        $data = $this->cache->get('categories.id.' . $cid);
        if (!$data) {
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories WHERE category_id = '" . $this->db->escape($cid) . "'");
            $data = $query->row;
            if ($data) {
                $data['permalink'] = $this->url->link('information/category', 'name=' . $this->db->escape($data['name']), '');
            }
            $this->cache->set('categories.id.' . $cid, $data);

        }

        return $data;
    }

    private function findCategoryByName($category_name)
    {
        $category_name = trim($category_name);

        $find_cat = $this->cache->get('categories.name.' . $category_name);
        if (!$find_cat) {
        // this presumes that the DB will do an case insensative search
            $query = $this->db->query("SELECT category_id FROM " . DATABASE . ".categories where name='" . $this->db->escape($category_name) . "'");
            $find_cat = $query->row;
            $this->cache->set('categories.name.' . $category_name, $find_cat);
        }

        if (!empty($find_cat)) {
            return $find_cat['category_id'];
        } else {
            return null;
        }

    }

    public function addCategory($category_name)
    {

        $category_name = trim($category_name);

        $cid = $this->findCategoryByName($category_name);

        if ($cid == null) {
            //todo add twitter handle checking too
            if ($this->isUrl($category_name)) {
                $tag_obj = $this->getTagObj($category_name);
                $this->db->query(
                    "INSERT INTO " . DATABASE . ".categories " .
                    " SET name='" . $this->db->escape($category_name) . "', " .
                    " person_name='" . $tag_obj['name'] . "', " .
                    " url='" . $tag_obj['url'] . "'"
                );
                $cid = $this->db->getLastId();
            } else {
                $this->db->query(
                    "INSERT INTO " . DATABASE . ".categories " .
                    " SET name='" . $this->db->escape($category_name) . "'"
                );
                $cid = $this->db->getLastId();
            }
        }
        return $cid;
    }

    private function isUrl($potential_string)
    {
        $potentional_string = strtolower($potential_string);
        if (preg_match('/https?:\/\/.+\..+/', $potential_string)) {
            return true;
        }
        return false;
    }

    private function getTagObj($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        $page_content = curl_exec($c);
        curl_close($c);
        unset($c);

        $result = array('name' => $url, 'url' => $url);
        if ($page_content !== false) {
            $mf2_parsed = Mf2\parse($page_content, $real_source_url);

            foreach ($mf2_parsed['items'] as $item) {
                if (array_key_exists('type', $item) && in_array('h-card', $item['type']) && array_key_exists('properties', $item)) {
                    $properties = $item['properties'];
                    if (array_key_exists('name', $properties)) {
                        $result['name'] = $properties['name'][0];
                    }
                    if (array_key_exists('url', $properties)) {
                        $result['url'] = $properties['url'][0];
                    }
                }
            }

        }
        return $result;
    }

}
