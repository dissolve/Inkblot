<?php  
class ControllerWebmentionVouch extends Controller {
	public function get() {
        $json = array();
        if(!isset($this->request->get['url'])){
            //header('HTTP/1.1 400 Bad Request');
            //exit();
            $json['success'] = 'no';
            $this->response->setOutput(json_encode($json));
        } else {
            $this->load->model('webmention/vouch');
            $vouch = $this->model_webmention_vouch->getPossibleVouchFor($this->request->get['url']);
            //$this->log->write($this->request->get['id']);

            if($vouch){
                $json['vouch'] = $vouch;
                $json['success'] = 'yes';
                $this->response->setOutput(json_encode($json));

            } else {
                //header('HTTP/1.1 404 Not Found');
                //exit();
                $json['success'] = 'no';
                $this->response->setOutput(json_encode($json));
            }

        }
	}

}
?>
