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
    $config->set('config_short_url', HTTP_SHORT);
    $config->set('config_short_ssl', HTTPS_SHORT);
    $config->set('config_secure', true);

// Url
$url = new Url($config->get('config_ssl'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Short_Url
$short_url = new Url($config->get('config_short_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_short_url'));
$registry->set('short_url', $short_url);

// Log
$log = new Log('scripted-error.txt');
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline)
{
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

// Front Controller
$controller = new Front($registry);
$controller->addPreAction(new Action('common/seo_url'));

//** END BASIC SET UP **//

