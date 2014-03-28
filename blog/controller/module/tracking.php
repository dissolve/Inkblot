<?php  
class ControllerModuleTracking extends Controller {
	public function index() {
        $json = array();
        $subid = null;
        $amount = null;
        $ip = $_SERVER['REMOTE_ADDR'];

        if(isset($this->request->get['subid'])){
            $subid = $this->request->get['subid'];
        }

        if(isset($this->request->get['amount'])){
            $amount = $this->request->get['amount'];
        }

        $json['ok'] = 1;

        $this->load->model('brand/tracking');
        $this->model_brand_tracking->track202($ip, $subid, $amount);

        $this->response->setOutput(json_encode($json));
    }

}
?>
