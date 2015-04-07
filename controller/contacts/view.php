<?php  
class ControllerContactsView extends Controller {
	public function index() {
		
		$this->load->model('blog/mycard');
		
        $data['auth_page'] = $this->url->link('auth/login');
        $data['auth_endpoint'] = AUTH_ENDPOINT;

        if(isset($this->session->data['user_site'])){
            $data['user_name'] = $this->session->data['user_site'];
        }
        $data['logout'] = $this->url->link('auth/logout');

        if($this->session->data['is_owner'] && $this->request->get['id']){
            die("TODO: get other contact");

        } else {



            $this->document->setTitle(AUTHOR_FIRST_NAME. ' ' . AUTHOR_LAST_NAME);
            $data['contact'] = array();

            foreach ($this->model_blog_mycard->getData($this->session->data['user_site'], 'contact') as $result) {
                    $data['contact'][] = array(
                        'first_name' => AUTHOR_FIRST_NAME,
                        'last_name' => AUTHOR_LAST_NAME,
                        'url'    => str_replace('{}', $result['value'], $result['link_format']),
                        'image'  => $result['image'],
                        'value'  => str_replace('{}', $result['value'], $result['mobile_label']),
                        'title'  => str_replace('{}', $result['value'], $result['title']),
                        'rel'    => $result['rel'],
                        'image'  => $result['field_display_image'],
                        'target' => $result['target']);
            }

            $data['elsewhere'] = array();

            foreach ($this->model_blog_mycard->getData($this->session->data['user_site'], 'elsewhere') as $result) {
                    $data['elsewhere'][] = array(
                        'url'    => str_replace('{}', $result['value'], $result['link_format']),
                        'image'  => $result['image'],
                        'value'  => str_replace('{}', $result['value'], $result['mobile_label']),
                        'title'  => str_replace('{}', $result['value'], $result['title']),
                        'rel'    => $result['rel'],
                        'image'  => $result['field_display_image'],
                        'target' => $result['target']);
            }

            
            $this->load->model('blog/post');
            $data['recent_posts'] = array();

            foreach ($this->model_blog_post->getRecentPosts(10) as $result) {
                if(empty($result['title'])){
                    if($result['post_type'] == 'photo'){
                        $result['title']='photo';
                    } else {
                        $result['title'] = substr(strip_tags(html_entity_decode($result['body'])), 0, 30). '...';
                    }
                }
                
                $data['recent_posts'][] = $result;
            }
        }

		$this->document->setDescription($this->config->get('config_meta_description'));



        $data['header'] = $this->load->controller('common/header/contacts');
        $data['footer'] = $this->load->controller('common/footer/contacts');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/contacts/view.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/contacts/view.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/contacts/view.tpl', $data));
		}
	}
}
?>
