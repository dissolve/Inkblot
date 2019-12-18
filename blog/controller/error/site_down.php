<?php
class ControllerErrorSiteDown extends Controller {
    public function index()
    {

        $this->document->setTitle(PAGE_TITLE);
        $this->data['document_title'] = PAGE_TITLE;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/site_down')) {
            $this->template = $this->config->get('config_template') . '/template/error/site_down';
        } else {
            $this->template = 'default/template/error/site_down';
        }

        $this->response->setOutput($this->render());
    }
}
