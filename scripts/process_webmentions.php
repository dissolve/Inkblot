<?php

//** BASIC SET UP (Since this will be run independently) **//
// Configuration
require_once('../config/blog.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

//flag for site being broken/administratively down
$site_down = false;

// Log
$log = new Log('webmentions-error.txt');

// Database 
try {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, '');
	$registry->set('db', $db);
} catch (ErrorException $ex) {
	$log->write('Error connecting to database!');
	die('Error connecting to database!');
}

	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);	
	$config->set('config_secure', true);	

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));	
$registry->set('url', $url);

function error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}
		
	if ($config->get('config_error_display')) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}

// Error Handler
set_error_handler('error_handler');

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache); 

//** END BASIC SET UP **//

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';


//check if target is at this site
$result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
$webmention = $result->row;

while($webmention){

    $source_url = trim($webmention['source_url']);
    $target_url = trim($webmention['target_url']);

    $webmention_id = $webmention['webmention_id'];

    //echo $webmention_id;

    //to verify that target is on my site
    $c = curl_init();
    curl_setopt($c, CURLOPT_NOBODY, 1);
    curl_setopt($c, CURLOPT_URL, $target_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $real_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
    curl_close($c);
    unset($c);


    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $source_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $page_content = curl_exec($c);
    curl_close($c);
    unset($c);

    if($page_content === FALSE){
        //our curl command failed to fetch the source site
        $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Failed To Fetch Source' WHERE webmention_id = ". (int)$webmention_id);

    //} elseif(strpos($real_url, HTTP_SERVER) !== 0 && strpos($real_url, HTTPS_SERVER) !== 0){
        ////target_url does not point actually redirect to our site
        //$db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Does Not Point Here' WHERE webmention_id = ". (int)$webmention_id);
//
    } elseif(stristr($page_content, $target_url) === FALSE){
        //we could not find the target_url anywhere on the source page.
        $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Not Found At Source' WHERE webmention_id = ". (int)$webmention_id);

    } else {
        $mf2_parsed = Mf2\parse($page_content);
        $comment_parsed = IndieWeb\comments\parse($mf2_parsed['items'][0], $target_url);

        include DIR_BASE . '/routes.php';

        $data = array();
        foreach($advanced_routes as $adv_route){
            $matches = array();
            preg_match($adv_route['expression'], $real_url, $matches);
            if(!empty($matches)){
                $model = $adv_route['controller'];
                    foreach($matches as $field => $value){
                        $data[$field] = $value;
                    }
            }
        }

        try {
            $loader->model($model);
            $registry->get('model_'. str_replace('/', '_', $model))->addWebmention($data, $mention_id, $comment_parsed);
        } catch (Exception $e) {
            $db->query("INSERT INTO ". DATABASE.".mentions SET source_url = '".$source_url."', parse_timestamp = NOW(), approved=1");
            $mention_id = $db->getLastId();
            $db->query("UPDATE ". DATABASE.".webmentions SET resulting_mention_id = '".(int)$mention_id."', webmention_status_code = '200', webmention_status = 'OK' WHERE webmention_id = ". (int)$webmention_id);
            $cache->delete('mentions');
        }


    }

    $result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
    $webmention = $result->row;

} //end while($webmention) loop
