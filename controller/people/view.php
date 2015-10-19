<?php
class ControllerPeopleView extends Controller {
    public function index()
    {


        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if (isset($this->session->data['user_site'])) {
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');

        if (!$this->session->data['is_owner']){
            $this->response->redirect('error/unauthorized');

        } else {
            $this->document->setTitle('People');
            $this->document->setDescription($this->config->get('config_meta_description'));

            $this->load->model('blog/person');

            $data['people'] = $this->model_blog_person->getPeople();


            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/contacts/view.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/contacts/view.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/contacts/view.tpl', $data));
            }
        }
    }
}
