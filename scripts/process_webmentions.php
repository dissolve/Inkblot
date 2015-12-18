<?php
require_once('script_setup.php'); //this is to set up interactions to the MVC
$action = new Action('webmention/receive/processWebmentions');
$controller->dispatch($action);
