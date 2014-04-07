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
                header('Webmention-Status: ' . $entry['webmention_status_code']);

                if($entry['webmention_status'] == 'accepted'){
                    $this->response->setOutput('This webmention has been accepted is awaiting moderator approval.');

                } elseif($entry['webmention_status'] == 'OK'){
                    $this->response->setOutput('This webmention has been accepted and approved.');

                } elseif ($entry['webmention_status'] == 'queued'){
                    $this->response->setOutput('This webmention is in the process queue.');

                } else {
                    $this->response->setOutput('This webmention processing failed because: ' . $entry['webmention_status']);

                }

            } else {
                header('HTTP/1.1 404 Not Found');
                exit();
            }

        }
	}

}
?>
