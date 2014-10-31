<?php
require_once('script_setup.php'); //this is to set up interactions to the MVC
$controller = new Front($registry);
$action = new Action('webmention/receive/process_webmentions');
$controller->dispatch($action, new Action('error/not_found'));
?>
