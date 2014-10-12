<?php

require_once('script_setup.php'); //this is to set up interactions to the MVC

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';
include '../libraries/cassis/cassis-loader.php';

$loader->model('webmention/vouch');
$registry->get('model_webmention_vouch')->processReferers();
