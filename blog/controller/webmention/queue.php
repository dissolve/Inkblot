<?php  
class ControllerWebmentionQueue extends Controller {
	public function index() {
        if(!isset($this->request->get['id'])){
            header('HTTP/1.1 400 Bad Request');
            exit();
        } else {
            $this->load->model('webmention/queue');
            $entry = $this->model_webmention_queue->getEntry($this->request->get['id']);
            $this->log->write($this->request->get['id']);
            if($entry){
                header('Webmention-Status: ' . $entry['webmention_status']);

                if($entry['webmention_status'] == 'success'){
                    $this->response->setOutput('Webmention is awaiting Approval');
                    //TODO: after it is approved, etc
                } elseif ($entry['webmention_status'] == 'queued'){
                    $this->response->setOutput('Webmention is processing');
                } else {
                    $this->response->setOutput('Webmention processing failed');
                }

            } else {
                header('HTTP/1.1 404 Not Found');
                exit();
            }

        }
	}

}
?>
