<?php
	function rewrite_url($link) {
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
?>
