<?php  

class ControllerWebmentionNotification extends Controller {
	public function manifest() {

        $data['site_name'] = SITE_TITLE;
        $data['gcm_id'] = GCM_PROJECT_ID;

        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/webmention/webapp_manifest.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/webmention/webapp_manifest.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/webmention/webapp_manifest.tpl', $data));
        }
    }

	public function subscribe() {

        if($this->session->data['is_owner']){
            $this->load->model('webmention/notification');
            $this->model_webmention_notification->addEntry( $this->request->post['endpoint'], $this->request->post['subscriptionId'] );
        }

    }

	public function unsubscribe() {

        if($this->session->data['is_owner']){
            $this->load->model('webmention/notification');
            $this->model_webmention_notification->deleteEntry($this->request->post['subscriptionId'] );
        }

    }

    public function pushMessage(){
            $this->load->model('webmention/notification');
            $entries = $this->model_webmention_notification->getEntries();
            foreach($entries as $entry){
                $ch = curl_init($entry['endpoint']);

                if(!$ch){$this->log->write('error with curl_init');}

                $data = array("registration_ids" => array($entry['subscription_id']));
                $data_string = json_encode($data);     

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: key='. GCM_API_KEY, 
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string)));
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

                /////////////////////////////////////////////////
                //TODO: once my hosting provider fixes its issue i can remove this
                //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                /////////////////////////////////////////////////

                $response = curl_exec($ch);
            }
    
    }

}
?>
