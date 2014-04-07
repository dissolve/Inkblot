<?php
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

// Database 
try {
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, '');
	$registry->set('db', $db);
} catch (ErrorException $ex) {
	die('error connecting to database');
}

		
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);	
	$config->set('config_secure', true);	


// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));	
$registry->set('url', $url);

// Log
$log = new Log('webmentions-error.txt');
$registry->set('log', $log);

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

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';

//select * from webmentions where webmention_status_code == '202';


//check if target is at this site
$result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
$webmention = $result->row;

while($webmention){

    $source_url = $webmention['source_url'];
    $source_url = $webmention['target_url'];

    $webmention_id = $webmention['webmention_id'];

    echo $webmention_id;

    //TODO verify that target is on my site

    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $source_url);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    $page_content = curl_exec($c);
    curl_close($c);
    unset($c);

    if($page_content === FALSE){
       $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Failed To Get Source' WHERE webmention_id = ". (int)$webmention_id);
    } elseif(stristr($page_content, $target_url) === FALSE){

       $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '400', webmention_status = 'Target Link Not Found At Source' WHERE webmention_id = ". (int)$webmention_id);
       continue;
    } else {

        $parsed = Mf2\parse($page_content);
        $result = IndieWeb\comments\parse($parsed['items'][0], $target_url);
        print_r($result);

           $db->query("UPDATE ". DATABASE.".webmentions SET webmention_status_code = '200', webmention_status = 'Pending Moderation' WHERE webmention_id = ". (int)$webmention_id);
        //if(isset($parsed['rels']) && isset($parsed['rels']['webmention']) && isset($parsed['rels']['webmention'][0])) {
            //echo $parsed['rels']['webmention'][0];
        //}
    }
    $result = $db->query("SELECT * FROM ". DATABASE.".webmentions WHERE webmention_status_code = '202' LIMIT 1");
    $webmention = $result->row;
}
