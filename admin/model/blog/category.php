<?php
class ModelBlogCategory extends Model {
	public function getCategories() {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories ORDER BY name ASC");
        $data = $query->rows;
        $data_array = array();
        foreach($data as $category){
            $data_array[] = array_merge($category, array(
                'permalink' => $this->url->link('blog/category', 'name='.$category['name'], '')
            ));
        }
	
		return $data_array;
	}

	public function getCategoryByName($name) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories WHERE name = '".$this->db->escape($name)."'");
        $data = $query->row;
        if($data){
            $data['permalink'] = $this->url->link('blog/category', 'name='.$data['name'], '');
        }
		return $data;
	}


    //note that we intentionally ignore cateogy_id = 0 for this list
    // this entry is the uncategoriezed entry and thus should not show at all

	public function getCategoriesForPost($post_id) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".categories JOIN ".DATABASE.".categories_posts USING(category_id) WHERE post_id = '".(int)$post_id."' AND NOT category_id = 0 ORDER BY name ASC");
        $data = $query->rows;

        $data_array = array();
        foreach($data as $category){
            $data_array[] = array_merge($category, array(
                'permalink' => $this->url->link('blog/category', 'name='.$category['name'], '')
            ));
        }

		return $data_array;
	}

}
?>
