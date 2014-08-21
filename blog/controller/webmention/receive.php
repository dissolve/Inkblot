<?php  
class ControllerWebmentionReceive extends Controller {
    public function index() {
        if(isset($this->request->post['source']) && isset($this->request->post['target'])){
            $this->response->addHeader('HTTP/1.1 202 Accepted');

            $this->load->model('webmention/queue');
            $queue_id = $this->model_webmention_queue->addEntry($this->request->post['source'], $this->request->post['target']);
            if(isset($this->request->post['callback'])){
                $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
            }
            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');

            $this->response->setOutput($link);
        } else {
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
    }

}
?>
