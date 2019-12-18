<?php
//TODO do i need these anymore?  should probably be in the model instead
require_once DIR_BASE . 'vendor/mf2/mf2/Mf2/Parser.php';
require_once DIR_BASE . '/libraries/php-comments/src/indieweb/comments.php';
require_once DIR_BASE . 'vendor/tantek/cassis/cassis.php';

class ControllerWebmentionVouch extends Controller {
    public function get()
    {
        $json = array();
        if (!$this->session->data['is_owner']) {
            //header('HTTP/1.1 400 Bad Request');
            $json['success'] = 'no';
            $this->response->setOutput(json_encode($json));

        } elseif (!isset($this->request->get['url'])) {
            //header('HTTP/1.1 400 Bad Request');
            //exit();
            $json['success'] = 'no';
            $this->response->setOutput(json_encode($json));

        } else {
            $this->load->model('webmention/vouch');
            $vouch = $this->model_webmention_vouch->getPossibleVouchFor($this->request->get['url']);
            //$this->log->write($this->request->get['id']);

            if ($vouch) {
                $json['vouch'] = $vouch;
                $json['success'] = 'yes';
                $this->response->setOutput(json_encode($json));

            } else {
                //header('HTTP/1.1 404 Not Found');
                //exit();
                $json['success'] = 'no';
                $this->response->setOutput(json_encode($json));
            }

        }
    }

    public function processreferers()
    {
        $this->load->model('webmention/vouch');
        $this->model_webmention_vouch->processReferers();
    }

}
