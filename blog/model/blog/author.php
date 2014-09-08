<?php
class ModelBlogAuthor extends Model {
	public function getAuthor($id) {
        $data = $this->cache->get('author.'. $id);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".authors WHERE author_id='".(int)$id."' LIMIT 1");
            $data = $query->row;
            $data['link'] = $this->url->link('blog/author', 'id='.(int)$data['author_id'], '');
            $this->cache->set('author.'.$id, $data);
        }
		return $data;
	}

}
?>
