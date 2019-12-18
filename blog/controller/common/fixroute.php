<?php
class ControllerCommonFixroute extends Controller {
    public function index()
    {
        $year = $this->request->get['year'];
        $month = $this->request->get['month'];
        $day = $this->request->get['day'];
        $daycount = $this->request->get['daycount'];

        $this->load->model('blog/post');
        $post = $this->model_blog_post->getPostByDayCount($year, $month, $day, $daycount);

        if ($post) {
            $this->response->redirect($post['permalink']);
        } else {
            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

            $this->document->setTitle('FOHR OH FOHR');
            $this->document->setDescription('FOHR OH FOHR');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');


            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/error/not_found', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/error/not_found', $data));
            }
        }
    }

}
