<?php
class ControllerGroupFeed extends Controller {
    public function index()
    {
        $group_id = $this->request->get['group_id'];
        $this->document->setBodyClass('h-feed');

        $this->load->model('group/group');
        $data['header'] = $this->load->controller('group/header');
        $data['footer'] = $this->load->controller('group/footer');

        $group_data = $this->model_group_group->getData($group_id);
        $group_members = $this->model_group_group->getMembers($group_id);
        $group_posts = $this->model_group_group->getRecentPosts($group_id);
        //notes getData
        //       - name
        //       - description
        //      getMembers
        //       - name
        //       - url
        //       - image_url
        //      getPosts
        //       - name
        //       - content
        //       ?

        $this->document->setTitle($group_data['name']);
        $data['title'] = $group_data['name'];

        $this->document->setDescription($group_data['description']);
        $data['description'] = $group_data['description'];

        $data['posts'] = $group_posts;
        $data['members'] = $group_members;

        if ($this->session->data['mp-config']) {
            $mpconfig = array();
            parse_str($this->session->data['mp-config'], $mpconfig);
        }




        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/group/group_list')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/group/group_list', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/group/group_list', $data));
        }
    }
}
