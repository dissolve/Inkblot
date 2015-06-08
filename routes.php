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
$routes['logstore']           = 'logs/endpoint';
$routes['login']              = 'auth/login';
$routes['login_callback']     = 'auth/login/callback';
$routes['login_token']        = 'auth/login/tokencallback';
$routes['logout']             = 'auth/logout';
$routes['contact']            = 'contacts/view';
$routes['micropub-send']      = 'micropub/client/send';
$routes['new']                = 'micropub/client';
$routes['edit']               = 'micropub/client/editPost';
$routes['delete']             = 'micropub/client/deletePost';

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

$routes['article']            = 'blog/article/latest';
$routes['article/']           = 'blog/article/latest';
$routes['note']               = 'blog/note/latest';
$routes['note/']              = 'blog/note/latest';
$routes['tag']                = 'blog/tag/latest';
$routes['tag/']               = 'blog/tag/latest';
$routes['follow']             = 'blog/follow/latest';
$routes['follow/']            = 'blog/follow/latest';
$routes['unfollow']           = 'blog/unfollow/latest';
$routes['unfollow/']          = 'blog/unfollow/latest';
$routes['rsvp']               = 'blog/rsvp/latest';
$routes['rsvp/']              = 'blog/rsvp/latest';
$routes['checkin']            = 'blog/checkin/latest';
$routes['checkin/']           = 'blog/checkin/latest';
$routes['bookmark']           = 'blog/bookmark/latest';
$routes['bookmark/']          = 'blog/bookmark/latest';
$routes['like']               = 'blog/like/latest';
$routes['like/']              = 'blog/like/latest';
$routes['listen']             = 'blog/listen/latest';
$routes['listen/']            = 'blog/listen/latest';
$routes['photo']              = 'blog/photo/latest';
$routes['photo/']             = 'blog/photo/latest';
$routes['video']              = 'blog/video/latest';
$routes['video/']             = 'blog/video/latest';
$routes['audio']              = 'blog/audio/latest';
$routes['audio/']             = 'blog/audio/latest';

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

$advanced_routes[] = array('controller' => 'blog/article',
    'expression' => '`^article/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'article/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/note',
    'expression' => '`^note/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'note/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/tag',
    'expression' => '`^tag/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'tag/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/follow',
    'expression' => '`^follow/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'follow/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/unfollow',
    'expression' => '`^unfollow/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'unfollow/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/rsvp',
    'expression' => '`^rsvp/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'rsvp/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/checkin',
    'expression' => '`^checkin/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'checkin/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/bookmark',
    'expression' => '`^bookmark/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'bookmark/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/like',
    'expression' => '`^like/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'like/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/listen',
    'expression' => '`^listen/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'listen/{year}/{month}/{day}/{daycount}/{slug}');

$advanced_routes[] = array('controller' => 'blog/photo',
    'expression' => '`^photo/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'photo/{year}/{month}/{day}/{daycount}/');

$advanced_routes[] = array('controller' => 'blog/video',
    'expression' => '`^video/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'video/{year}/{month}/{day}/{daycount}/');

$advanced_routes[] = array('controller' => 'blog/audio',
    'expression' => '`^audio/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})/(?P<daycount>\d+)/(?P<slug>.*)`',
    'reverse' => 'audio/{year}/{month}/{day}/{daycount}/');


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
