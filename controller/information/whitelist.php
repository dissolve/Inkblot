<?php
class ControllerInformationWhitelist extends Controller {
    public function index()
    {
        $this->document->setTitle('Accepting Webmentions and Comments From...');
        $data['title'] = 'Acceptable Webmention Sources';

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        // pass if we are the owner or not down to the view
        if ($this->session->data['is_owner']) {
            $data['is_owner'] = true;
        }

        $this->document->setDescription($this->config->get('config_meta_description'));

        //get the white list
        $this->load->model('webmention/vouch');

        // if we are the site owner we would like to get the list of ALL whitelisted users, including those we do not pulish
        $whitelist = $this->model_webmention_vouch->getWhitelist($this->session->data['is_owner']);
        $data['whitelist']  = array();

        foreach ($whitelist as $entry) {
            $data['whitelist'][]  = array(
                'domain' => $entry['domain'],
                'public' => $entry['public'] == 1,
                'delete' => $this->url->link('information/whitelist/remove', 'wid=' . $entry['whitelist_id'], ''),
                'make_public' => $this->url->link('information/whitelist/makepublic', 'wid=' . $entry['whitelist_id'], ''),
                'make_private' => $this->url->link('information/whitelist/makeprivate', 'wid=' . $entry['whitelist_id'], '')
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/whitelist.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/information/whitelist.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/information/whitelist.tpl', $data));
        }
    }

    public function makepublic()
    {
        if ($this->session->data['is_owner'] && isset($this->request->get['wid'])) {
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->setWhitelistEntryPublic($this->request->get['wid']);
        }
        $this->response->redirect($this->url->link('information/whitelist'));
    }
    public function makeprivate()
    {
        if ($this->session->data['is_owner'] && isset($this->request->get['wid'])) {
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->setWhitelistEntryPrivate($this->request->get['wid']);
        }
        $this->response->redirect($this->url->link('information/whitelist'));
    }
    public function remove()
    {
        if ($this->session->data['is_owner'] && isset($this->request->get['wid'])) {
            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->removeWhitelistEntry($this->request->get['wid']);
        }
        $this->response->redirect($this->url->link('information/whitelist'));
    }

}
