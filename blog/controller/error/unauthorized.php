<?php
class ControllerErrorUnauthorized extends Controller {
    public function index()
    {

        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 401 Unauthorized');

        $this->document->setTitle('Uh Uh Uh You didn\'t say the magic workd');
        $this->document->setDescription('Unauthorized');

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_authorized')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_authorized', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/error/not_authorized', $data));
        }

    }
}
