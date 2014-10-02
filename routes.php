<?php
$routes = array();
$advanced_routes = array();

$routes['']                   = 'common/home';
$routes['sitemap']            = 'information/sitemap';

$routes['clearcache']         = 'admin/cache';
$routes['clearrevision']      = 'admin/cache/revision';
$routes['webmention']         = 'webmention/receive';
$routes['token']              = 'auth/token';
$routes['micropub']           = 'micropub/receive';
$routes['login']              = 'auth/login';
$routes['login_callback']     = 'auth/login/callback';
$routes['login_token']        = 'auth/login/tokencallback';
$routes['logout']             = 'auth/logout';
$routes['contact']            = 'contacts/me';
$routes['micropub-send']      = 'micropub/client/send';
$routes['new/rsvp']           = 'micropub/client/rsvp';
$routes['new/checkin']        = 'micropub/client/checkin';
$routes['new/article']        = 'micropub/client/article';
$routes['new']                = 'micropub/client';
$routes['new/note']           = 'micropub/client';

$advanced_routes[] = array('controller' => 'blog/pages',
    'expression' => '`page/(?P<id>\w+)`i',
    'reverse' => 'page/{slug}');

$advanced_routes[] = array('controller' => 'webmention/queue',
    'expression' => '`queue/(?P<id>\d+)`i',
    'reverse' => 'queue/{id}');

$advanced_routes[] = array('controller' => 'blog/archive',
    'expression' => '`archive/(?P<year>\d{4})-(?P<month>\d{1,2})`i',
    'reverse' => 'archive/{year}-{month}');

$advanced_routes[] = array('controller' => 'blog/category',
    'expression' => '`category/(?P<name>\w+)`i',
    'reverse' => 'category/{name}');

$advanced_routes[] = array('controller' => 'blog/shortener',
    'expression' => '`s/(?P<eid>.+)`',
    'reverse' => 's/{eid}');

$advanced_routes[] = array('controller' => 'blog/article',
    'expression' => '`post/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/.*`i',
    'reverse' => 'post/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/note',
    'expression' => '`note/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/.*`i',
    'reverse' => 'note/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/photo',
    'expression' => '`photo/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/?.*`i',
    'reverse' => 'photo/{year}/{month}/{day}/{daycount}');

$advanced_routes[] = array('controller' => 'blog/author',
    'expression' => '`author/(?P<id>\d+)`i',
    'reverse' => 'author/{id}');


?>
