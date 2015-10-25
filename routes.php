<?php
$routes = array();
$advanced_routes = array();

$routes['']                   = 'common/home';
$routes['feed']               = 'blog/feed';
$routes['feed/full']          = 'blog/feed/firehose';
$routes['sitemap']            = 'information/sitemap';

$routes['clearcache']         = 'admin/cache';
$routes['clearrevision']      = 'admin/cache/revision';
$routes['webmention']         = 'webmention/receive';
$routes['token']              = 'auth/token';
$routes['micropub']           = 'micropub/receive';
#$routes['logstore']           = 'logs/endpoint';
$routes['login']              = 'auth/login';
$routes['login_callback']     = 'auth/login/callback';
$routes['login_token']        = 'auth/login/tokencallback';
$routes['logout']             = 'auth/logout';
$routes['contact']            = 'contacts/view';
$routes['people']             = 'people/view';
$routes['people/merge']       = 'people/view/merge';
$routes['micropub-send']      = 'micropub/client/send';
$routes['new']                = 'micropub/client';
$routes['live']                = 'micropub/client/live';
$routes['edit']               = 'micropub/client/editPost';
$routes['delete']             = 'micropub/client/deletePost';

$routes['unauthorized']       = 'error/unauthorized';

$routes['new/note']           = 'micropub/client/note';
$routes['new/checkin']        = 'micropub/client/checkin';

$routes['manage/contacts']    = 'micropub/client/contacts';

$routes['undelete']           = 'micropub/client/undeletePost';
$routes['vouchsearch']        = 'webmention/vouch/get';
$routes['activity']          = 'information/activity';
$routes['whitelist']          = 'information/whitelist';
$routes['whitelist/delete']   = 'information/whitelist/remove';
$routes['whitelist/private']  = 'information/whitelist/makeprivate';
$routes['whitelist/public']   = 'information/whitelist/makepublic';


$routes['manifest']      = 'webmention/notification/manifest';
$routes['subscribe']      = 'webmention/notification/subscribe';
$routes['unsubscribe']      = 'webmention/notification/unsubscribe';

$advanced_routes[] = array('controller' => 'blog/pages',
    'expression' => '`^page/(?P<id>\w+)`i',
    'reverse' => 'page/{slug}');

$advanced_routes[] = array('controller' => 'webmention/queue',
    'expression' => '`^queue/(?P<id>\d+)`i',
    'reverse' => 'queue/{id}');

$advanced_routes[] = array('controller' => 'information/category',
    'expression' => '`^category/(?P<name>\w+)/?`i',
    'reverse' => 'category/{name}');

$advanced_routes[] = array('controller' => 'common/shortener',
    'expression' => '`^s/(?P<eid>.+)`',
    'reverse' => 's/{eid}');


//redirects if case is not correct or if there is no trailing slash when the slug is missing
$advanced_routes[] = array('controller' => 'common/fixroute',
    'expression' => '`^[a-z]+/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/?.*`i',
    'reverse' => 'post/{year}/{month}/{day}/{daycount}/{slug}');
//The way this file is processed, the  later routes have precedence

$advanced_routes[] = array('controller' => 'blog/post',
    'expression' => '`^(?P<post_type>\w+)/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => '{post_type}/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/post/latest',
    'expression' => '`^(?P<post_type>\w+)/?$`',
    'reverse' => '{post_type}/');



$advanced_routes[] = array('controller' => 'information/author',
    'expression' => '`^author/(?P<id>\d+)`i',
    'reverse' => 'author/{id}');

$advanced_routes[] = array('controller' => 'information/archive/day',
    'expression' => '`^(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/?`i',
    'reverse' => '{year}/{month}/{day}');

$advanced_routes[] = array('controller' => 'information/archive',
    'expression' => '`^(?P<year>\d{4})/(?P<month>\d{1,2})/?`i',
    'reverse' => '{year}/{month}');


?>
