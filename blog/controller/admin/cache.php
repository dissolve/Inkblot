<?php 
class ControllerAdminCache extends Controller {
	private $error = array(); 
	    
  	public function index() {
        $json = array();

    	$this->cache->delete('author');
    	$this->cache->delete('archives');
    	$this->cache->delete('article');
    	$this->cache->delete('categories');
    	$this->cache->delete('mentions');
    	$this->cache->delete('post');
    	$this->cache->delete('posts');
    	$this->cache->delete('photo');
    	$this->cache->delete('photos');
    	$this->cache->delete('note');
    	$this->cache->delete('notes');
    	$this->cache->delete('likes');
    	$this->cache->delete('listens');
    	$this->cache->delete('comments');
    	$this->cache->delete('mydata');

        $json['success'] = "Full cache cleared!";
        if(isset($this->request->get['reason'])){
            $json['reason'] = $this->request->get['reason'];
        }
        $this->log->write( "Full cache cleared!" . (isset($json['reason']) ? ' Reason: '.$json['reason'] : ''));

        $this->response->setOutput(json_encode($json));
				
  	}


}
?>
