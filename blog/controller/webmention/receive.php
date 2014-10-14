<?php  
class ControllerWebmentionReceive extends Controller {
    public function index() {
        $source = $this->request->post['source'];
        $target = $this->request->post['target'];
        $vouch = $this->request->post['vouch'];

        // are URLs Valid?
        if(!$this->is_valid_url($source) || !$this->is_valid_url($target)){
            header('HTTP/1.1 400 Bad Request');
            exit();
        } elseif (!$this->is_approved_source($source)){

            if(!USE_VOUCH){
                header('HTTP/1.1 400 Bad Request');
                exit();
            } else {
                if(!isset($this->request->post['vouch'])){
                    header('HTTP/1.1 449 Retry With vouch');
                    exit();

                } elseif(!$this->is_valid_url($vouch)){
                    header('HTTP/1.1 400 Bad Request');
                    exit();
                } elseif(!$this->is_approved_source($vouch)){
                    header('HTTP/1.1 400 Bad Request');
                    exit();

                } else {
                    //todo  review this
                    $this->response->addHeader('HTTP/1.1 202 Accepted');

                    $this->load->model('webmention/queue');
                    $queue_id = $this->model_webmention_queue->addEntry($source, $target, $vouch);

                    if(isset($this->request->post['callback'])){
                        $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
                    }

                    $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

                    $this->response->addHeader('Link: <'.$link.'>; rel="status"');

                    $this->response->setOutput($link);
                }
            }

        } else {
            $this->response->addHeader('HTTP/1.1 202 Accepted');

            $this->load->model('webmention/queue');
            $queue_id = $this->model_webmention_queue->addEntry($source, $target);

            if(isset($this->request->post['callback'])){
                $this->model_webmention_queue->setCallback($queue_id, $this->request->post['callback']);
            }

            $link = $this->url->link('webmention/queue', 'id='.$queue_id, '');

            $this->response->addHeader('Link: <'.$link.'>; rel="status"');

            $this->response->setOutput($link);
        }
    }

    private function is_approved_source($url){
        if(!USE_VOUCH){
            return true;
        }
        $this->load->model('webmention/vouch');
        return $this->model_webmention_vouch->isWhiteListed($url);
    }

    //very basic function to determine if URL is valid, this is certainly a great place for improvement
    private function is_valid_url($url){
        if(!isset($url)) {
            return false;
        }
        if(empty($url)) {
            return false;
        } 
        if(strpos($url, '.') == 0) {
            return false;
        }
        return true;
    }

}
?>
