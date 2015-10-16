<?php
class ControllerCommonHeader extends Controller {
    public function index()
    {


        $data = $this->commonSetup();


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
        } else {
            return $this->load->view('default/template/common/header.tpl', $data);
        }
    }

    public function clean()
    {

        $data = $this->commonSetup();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header_clean.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/common/header_clean.tpl', $data);
        } else {
            return $this->load->view('default/template/common/header_clean.tpl', $data);
        }
    }

    public function contacts()
    {

        $data = $this->commonSetup();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header_contacts.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/common/header_contacts.tpl', $data);
        } else {
            return $this->load->view('default/template/common/header_contacts.tpl', $data);
        }
    }

    private function commonSetup()
    {
        //store our referrer
        $this->load->model('webmention/vouch');
        $this->model_webmention_vouch->recordReferer($_SERVER['HTTP_REFERER']);

        $headers = apache_request_headers();
        if (isset($this->request->post['access_token']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($headers['Authorization'])) {
            $token = $this->request->post['access_token'];
            if (!$token) {
                $parts = explode(' ', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
                $token = $parts[1];
            }
            if (!$token) {
                $parts = explode(' ', $headers['Authorization']);
                $token = $parts[1];
            }
            $this->load->model('auth/token');
            $auth_info = $this->model_auth_token->getAuthFromToken($token);
            if (!empty($auth_info)) {
                $this->session->data['user_site'] = $auth_info['user'];

            }

        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        $data['site_title'] = SITE_TITLE;
        $data['site_subtitle'] = SITE_SUBTITLE;

        $data['feedlinks'] = array();
        $data['feedlinks'][] = array( 'link' => $this->url->link('blog/article/latest'), 'title' => 'Latest Articles');
        $data['feedlinks'][] = array( 'link' => $this->url->link('blog/note/latest'), 'title' => 'Latest Notes');
        $data['feedlinks'][] = array( 'link' => $this->url->link('blog/like/latest'), 'title' => 'Latest Likes');
        $data['feedlinks'][] = array( 'link' => $this->url->link('blog/photo/latest'), 'title' => 'Latest Photos');


        $webmention_handler = $this->url->link('webmention/receive');
        $auth_endpoint = AUTH_ENDPOINT;
        $token_endpoint = $this->url->link('auth/token');
        $micropub_endpoint = $this->url->link('micropub/receive');
        $public_whitelist = $this->url->link('information/whitelist');

        if ($this->session->data['is_owner']) {
            $data['is_owner'] = true;
        }

        $this->response->addHeader('Link: <' . $webmention_handler . '>; rel="webmention"', false);
        $this->response->addHeader('Link: <' . $auth_endpoint . '>; rel="authorization_endpoint"', false);
        $this->response->addHeader('Link: <' . $token_endpoint . '>; rel="token_endpoint"', false);
        $this->response->addHeader('Link: <' . $micropub_endpoint . '>; rel="micropub"', false);

        $data['webmention_handler'] = $webmention_handler;
        $data['authorization_endpoint'] = $auth_endpoint;
        $data['token_endpoint'] = $token_endpoint;
        $data['micropub_endpoint'] = $micropub_endpoint;
        $data['public_whitelist'] = $public_whitelist;

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        $data['base'] = $server;
        $data['title'] = $this->document->getTitle();
        $data['bodyclass'] = $this->document->getBodyClass();
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['metas'] = $this->document->getMetas();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts();
        $data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
        $data['name'] = $this->config->get('config_name');

        $data['pro_nominative'] = PRONOUN_NOMINATIVE;
        $data['pro_oblique'] = PRONOUN_OBLIQUE;
        $data['pro_posessive'] = PRONOUN_POSESSIVE;


        $data['icon'] = $this->document->getIcon();
        if (!$data['icon']) {
            $data['icon'] = '/image/static/icon_144.jpg';
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }


        $data['home'] = $this->url->link('common/home');

        $status = true;

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
            $robots = explode("\n", str_replace(array("\r\n", "\r"), "\n", trim($this->config->get('config_robots'))));

            foreach ($robots as $robot) {
                if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
                    $status = false;

                    break;
                }
            }
        }

        $data['categories'] = array();

        $data['search'] = $this->load->controller('module/search');

        // For page specific css
        if (isset($this->request->get['route'])) {
            if (isset($this->request->get['product_id'])) {
                $class = '-' . $this->request->get['product_id'];
            } elseif (isset($this->request->get['path'])) {
                $class = '-' . $this->request->get['path'];
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $class = '-' . $this->request->get['manufacturer_id'];
            } else {
                $class = '';
            }

            $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
        } else {
            $data['class'] = 'common-home';
        }

        return $data;

    }

}
