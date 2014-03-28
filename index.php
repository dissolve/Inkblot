<?php
// Version
define('VERSION', '2.0');

// Configuration
require_once('config/blog.php');

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
	$site_down = true;
}

		
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);	
	$config->set('config_secure', true);	


// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));	
$registry->set('url', $url);

// Log
$log = new Log('error.txt');
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

// Request
$request = new Request();
$registry->set('request', $request);

if(ENVIRONMENT != 'LIVE' && isset($request->get['theme'])){
    $config->set('config_template', $request->get['theme']);	 
} else {
    $config->set('config_template', 'default');	 
    //$config->set('config_template', TEMPLATE_THEME);	 
}
    
// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response); 

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache); 

// Session
$session = new Session();
$registry->set('session', $session);


$config->set('config_language_id', 1);
$config->set('config_language', 'en');

// Language	
$language = new Language('english');
$language->load('english');	
$registry->set('language', $language); 

// Document
$registry->set('document', new Document()); 		


// Front Controller 
$controller = new Front($registry);

// SEO URL's
$controller->addPreAction(new Action('common/seo_url'));

// Maintenance Mode
//$controller->addPreAction(new Action('common/maintenance'));
	
// Router
if ($site_down) {
	$action = new Action('error/site_down');
} else if (isset($request->get['route'])) {
	$action = new Action($request->get['route']);
} else {
	$action = new Action('common/home');
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();
?>
