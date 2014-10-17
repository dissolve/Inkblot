<?php

require_once('script_setup.php'); //this is to set up interactions to the MVC

//TODO do i need these anymore?
require_once '../libraries/php-mf2/Mf2/Parser.php';
require_once '../libraries/php-comments/src/indieweb/comments.php';
require_once '../libraries/cassis/cassis-loader.php';

$loader->model('webmention/vouch');
$registry->get('model_webmention_vouch')->processReferers();
