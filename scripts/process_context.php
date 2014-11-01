<?php
require_once('script_setup.php'); //this is to set up interactions to the MVC
$action = new Action('webmention/queue/processcontexts');
$controller->dispatch($action, new Action('error/not_found'));
?>
