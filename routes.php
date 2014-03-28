<?php
$routes = array();
$advanced_routes = array();

$routes['']                   = 'common/home';
$routes['sitemap']            = 'information/sitemap';

$routes['clearcache']     	  = 'admin/cache';
$routes['clearrevision']   	  = 'admin/cache/revision';


$advanced_routes[] = array('controller' => 'blog/category',
    'expression' => '`category/(?P<name>\w+)`i',
    'reverse' => 'category/{name}');

$advanced_routes[] = array('controller' => 'blog/post',
    'expression' => '`post/(?P<year>\d{4})/(?P<month>\d\d+)/(?P<day>\d\d+)/(?P<daycount>\d+)/.*`i',
    'reverse' => 'post/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/author',
    'expression' => '`author/(?P<id>\d+)`i',
    'reverse' => 'author/{id}');


?>
