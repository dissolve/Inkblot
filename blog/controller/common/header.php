<?php   
class ControllerCommonHeader extends Controller {
	public function index() {
        $headers = apache_request_headers();
        if(isset($this->request->post['access_token']) || isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) || isset($headers['Authorization'])){
            $token = $this->request->post['access_token'];
            if(!$token){
                $parts = explode(' ', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
                $token = $parts[1];
            }
            if(!$token){
                $parts = explode(' ', $headers['Authorization']);
                $token = $parts[1];
            }
            $this->load->model('auth/token');
            $auth_info = $this->model_auth_token->getAuthFromToken($token);
            if(!empty($auth_info)) {
                $this->session->data['user_site'] = $auth_info['user'];

            }

        }
		$data['title'] = $this->document->getTitle();
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$data['site_title'] = SITE_TITLE;
		$data['site_subtitle'] = SITE_SUBTITLE;

		$webmention_handler = $this->url->link('webmention/receive');
        $auth_endpoint = AUTH_ENDPOINT;
        $token_endpoint = $this->url->link('auth/token');
        $micropub_endpoint = $this->url->link('micropub/receive');

        $this->response->addHeader('Link: <'. $webmention_handler.'>; rel="webmention"', false);
        $this->response->addHeader('Link: <'. $auth_endpoint.'>; rel="authorization_endpoint"', false);
        $this->response->addHeader('Link: <'. $token_endpoint.'>; rel="token_endpoint"', false);
        $this->response->addHeader('Link: <'. $micropub_endpoint.'>; rel="micropub"', false);

		$data['webmention_handler'] = $webmention_handler;
		$data['authorization_endpoint'] = $auth_endpoint;
		$data['token_endpoint'] = $token_endpoint;
		$data['micropub_endpoint'] = $micropub_endpoint;

        if(isset($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if(isset($this->session->data['error'])){
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();	 
		$data['metas'] = $this->document->getMetas();	 
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$data['name'] = $this->config->get('config_name');
		
			$data['icon'] = '/image/static/icon_144.jpg';
		
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
		
		// Menu
		//$this->load->model('blog/category');
		
		//$this->load->model('blog/product');
		
		$data['categories'] = array();
					
		//$categories = $this->model_blog_category->getCategories(0);
		
		/*foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();
				
				$children = $this->model_blog_category->getCategories($category['category_id']);
				
				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);
					
					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_blog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);						
				}
				
				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
        }*/
		
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
						
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/common/header.tpl', $data);
		} else {
			return $this->load->view('default/template/common/header.tpl', $data);
		}
	}
}
?>
