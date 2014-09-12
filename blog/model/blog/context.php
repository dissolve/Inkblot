<?php
class ModelBlogContext extends Model {

	public function getImmediateContextForPost($post_id) {
        $data = $this->cache->get('context.immediate.post.'.$post_id);
        if(!$data){
            $query = $this->db->query("SELECT * FROM " . DATABASE . ".context JOIN ".DATABASE.".post_context USING(context_id) WHERE post_id = ".(int)$post_id);
            $data = $query->rows;
            $this->cache->set('context.immediate.post.'.$post_id, $data);
        }
	
		return $data;
	}

	public function getAllContextForPost($post_id) {
        $data = $this->cache->get('context.all.post.'.$post_id);
        if(!$data){
            
            $ids = array();
            $query = $this->db->query("SELECT context_id FROM " . DATABASE . ".post_context WHERE post_id = ".(int)$post_id);

            foreach($query->rows as $toAdd){
                if(!in_array((int)$toAdd['context_id'], $ids)){
                    $ids[] = (int)$toAdd['context_id'];
                }
            } 

            $prev = 0;
            while(count($ids) > $prev){
                $prev = count($ids);

                $query = $this->db->query("SELECT parent_context_id AS context_id FROM " . DATABASE . ".context_to_context WHERE context_id in (".implode(',',$ids).")");
                foreach($query->rows as $toAdd){
                    if(!in_array((int)$toAdd['context_id'], $ids)){
                        $ids[] = (int)$toAdd['context_id'];
                    }
                } 
            }

            $query = $this->db->query("SELECT * FROM " . DATABASE . ".context WHERE context_id in (".implode(',',$ids).") ORDER BY `timestamp` ASC");
            $data = $query->rows;

            $this->cache->set('context.all.post.'.$post_id, $data);
        }
		return $data;
	}


    /*
    //recursively called function, foreach has this nice little ability to handle our base case for us.  
    //  When there are no parents, we return an empty array.  The previous run will test for the empty array and not set a parents value
    private function getAllContextForContext($context_id) {
        $query = $this->db->query("SELECT c.* FROM " . DATABASE . ".context c JOIN ".DATABASE.".context_to_context ctc ON c.context_id = ctc.parent_context_id WHERE ctc.context_id = ".(int)$context_id);
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
	
}

