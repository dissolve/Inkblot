<?php  
class ControllerInformationActivity extends Controller {
    public function index(){
		$this->document->setTitle('Recent Activity');
		$data['title'] = 'Recent Activity';

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

        // pass if we are the owner or not down to the view
        if($this->session->data['is_owner']){
            $data['is_owner'] = true;
        }

		$this->document->setDescription($this->config->get('config_meta_description'));

        $this->load->model('blog/interaction');
        $data['recent_interactions'] = $this->model_blog_interaction->getRecentInteractions(40);


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/interactions.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/interactions.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/information/interactions.tpl', $data));
		}
    }


}
?>
