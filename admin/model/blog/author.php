<?php
class ModelBlogAuthor extends Model {
	public function getAuthor($id) {
        $query = $this->db->query("SELECT * FROM " . DATABASE . ".authors WHERE author_id='".(int)$id."' LIMIT 1");
        $data = $query->row;
        $data['link'] = $this->url->link('blog/author', 'id='.$data['author_id'], '');
		return $data;
	}

}
?>
