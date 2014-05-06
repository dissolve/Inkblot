<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		//if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		//}
		
		// Decode URL
		if (isset($this->request->get['_route_'])) {
            $this->log->write('debug3');
            $route_get =  $this->request->get['_route_'];
            //remove a trailing /
            if(substr($route_get, -1) == '/'){
                $route_get = substr($route_get, 0, -1);
            }
            include DIR_BASE . '/routes.php';

            if(isset($routes[$route_get])){
                $this->request->get['route'] = $routes[$route_get];
            } else{
                foreach($advanced_routes as $adv_route){
                    $matches = array();
                    preg_match($adv_route['expression'], $route_get, $matches);
                    if(!empty($matches)){
                        $this->request->get['route'] = $adv_route['controller'];
                            foreach($matches as $field => $value){
                                $this->request->get[$field] = $value;
                            }
                    }
                }

            }
			
        } else { //_route_ is not set
            $this->log->write('debug4');

            // check to see if '' is set
            include DIR_BASE . '/routes.php';
            if(isset($routes[''])){
                $this->request->get['route'] = $routes[''];
            } 

        }
        if (isset($this->request->get['route'])) {
            return new Action($this->request->get['route']);
        }

	}
	
	public function rewrite($link) {
        include DIR_BASE . '/routes.php';
		$url_info = parse_url(str_replace('&amp;', '&', $link));
	
		$url = ''; 
		
		$data = array();
		
		parse_str($url_info['query'], $data);

        if(isset($data['route'])){
            foreach ($routes as $route_key => $true_route){
                if($data['route'] === $true_route){
                    $url .= '/' . $route_key;
                    unset($data['route']);
                }
            }
            if($url === ''){
                $expression = '';
                foreach ($advanced_routes as $adv_route){
                    if($adv_route['controller'] == $data['route']){
                         $expression = $adv_route['reverse'];
                    }
                }
                unset($data['route']);

                $brace = function($n){ return '{' . $n . '}';};

                $keys = array_map($brace, array_keys($data));
                $values = array_values($data);

                $url .= '/'. str_replace($keys, $values, $expression);

                $matches = array();
                preg_match_all('/{([^}]+)}/', $expression, $matches);

                foreach($matches[1] as $field){
                    unset($data[$field]);
                }

            }
        }
		
	
		if ($url) {
		
			$query = '';
		
			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . $key . '=' . $value;
				}
				
				if ($query) {
					$query = '?' . trim($query, '&');
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}	
}
?>
