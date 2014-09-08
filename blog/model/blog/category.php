<?php
class ModelBlogCategory extends Model {
	public function getCategories() {
        $data = $this->cache->get('categories.all');
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories ORDER BY name ASC");
            $data = $query->rows;
            $data_array = array();
            foreach($data as $category){
                $data_array[] = array_merge($category, array(
					'permalink' => $this->url->link('blog/category', 'name='.$this->db->escape($category['name']), '')
                ));
            }
            $this->cache->set('categories.all', $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

	public function getCategoryByName($name) {
        $data = $this->cache->get('category.'.$name);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories WHERE name = '".$this->db->escape($name)."'");
            $data = $query->row;
            if($data){
                $data['permalink'] = $this->url->link('blog/category', 'name='.$this->db->escape($data['name']), '');
            }
            $this->cache->set('category.'.$name, $data);
        }
		return $data;
	}


    //note that we intentionally ignore cateogy_id = 0 for this list
    // this entry is the uncategoriezed entry and thus should not show at all

	public function getCategoriesForPost($post_id) {
        $data = $this->cache->get('categories.post'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories JOIN ".DATABASE.".categories_posts USING(category_id) WHERE post_id = '".(int)$post_id."' AND NOT category_id = 0 ORDER BY name ASC");
            $data = $query->rows;

            $data_array = array();
            foreach($data as $category){
                $data_array[] = array_merge($category, array(
					'permalink' => $this->url->link('blog/category', 'name='.$this->db->escape($category['name']), '')
                ));
            }
            $this->cache->set('categories.post.'.$post_id, $data_array);
        } else {
            $data_array = $data;
        }
	
		return $data_array;
	}

}
?>
