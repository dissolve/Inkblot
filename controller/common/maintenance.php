<?php
class ControllerCommonMaintenance extends Controller {
    public function index()
    {
        if ($this->config->get('config_maintenance')) {
            $route = '';

            if (isset($this->request->get['route'])) {
                $part = explode('/', $this->request->get['route']);

                if (isset($part[0])) {
                    $route .= $part[0];
                }
            }

            // Show site if logged in as admin
            $this->load->library('user');

            $this->user = new User($this->registry);

            if (($route != 'payment') && !$this->user->isLogged()) {
                return $this->forward('common/maintenance/info');
            }
        }
    }

    public function info()
    {

        $this->document->setTitle(PAGE_TITLE . '|Maintenance');

        $this->data['heading_title'] = PAGE_TITLE . '|Maintenance';

        $text_message  = '<h1 style="text-align:center;">
            We are currently performing some scheduled maintenance. <br/>
            We will be back as soon as possible. Please check back soon.</h1>';

        $this->data['message'] = $text_message;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/maintenance.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/maintenance.tpl';
        } else {
            $this->template = 'default/template/common/maintenance.tpl';
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
