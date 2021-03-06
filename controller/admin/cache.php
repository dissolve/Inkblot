<?php
class ControllerAdminCache extends Controller {
    private $error = array();

    public function index()
    {
        //print_r(json_decode($this->session->data['mp-config'],true)); die();
        $json = array();
        if ($this->session->data['is_owner']) {
            $this->cache->delete('archives');
            $this->cache->delete('article');
            $this->cache->delete('author');
            $this->cache->delete('categories');
            $this->cache->delete('comments');
            $this->cache->delete('context');
            $this->cache->delete('followings');
            $this->cache->delete('interactions');
            $this->cache->delete('likes');
            $this->cache->delete('listens');
            $this->cache->delete('mentions');
            $this->cache->delete('mydata');
            $this->cache->delete('note');
            $this->cache->delete('notes');
            $this->cache->delete('photo');
            $this->cache->delete('photos');
            $this->cache->delete('post');
            $this->cache->delete('post_id');
            $this->cache->delete('posts');
            $this->cache->delete('syndications');
            $this->cache->delete('whitelist');

            $json['success'] = "Full cache cleared!";
            if (isset($this->request->get['reason'])) {
                $json['reason'] = $this->request->get['reason'];
            }
            $this->log->write("Full cache cleared!" . (isset($json['reason']) ? ' Reason: ' . $json['reason'] : ''));

        } else {
            $json['response'] = "Authorization Needed";

        }
        $this->response->setOutput(json_encode($json));

    }


}
