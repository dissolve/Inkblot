<?php

require_once('script_setup.php'); //this is to set up interactions to the MVC

include '../libraries/php-mf2/Mf2/Parser.php';
include '../libraries/php-comments/src/indieweb/comments.php';
include '../libraries/cassis/cassis-loader.php';

$loader->model('blog/wmqueue');
$post_id = $registry->get('model_blog_wmqueue')->getNext();

while($post_id){
    $loader->model('blog/post');
    $post = $registry->get('model_blog_post')->getPost($post_id);

    // send webmention
    include DIR_BASE . '/libraries/mention-client-php/src/IndieWeb/MentionClient.php';
    if(isset($post['image_file'])){
        $client = new IndieWeb\MentionClient($post['shortlink'], '<a href="'.$post['replyto'].'">ReplyTo</a>' .
        '<img src="'.$post['image_file'].'" class="u-photo photo-post" />' .html_entity_decode($post['body'].$post['syndication_extra']) );
    } else {
        $client = new IndieWeb\MentionClient($post['shortlink'], '<a href="'.$post['replyto'].'">ReplyTo</a>' . html_entity_decode($post['body'].$post['syndication_extra']) );
    }
    $client->debug(false);
    $sent = $client->sendSupportedMentions();
    $urls = $client->getReturnedUrls();
    foreach($urls as $syn_url){
        $registry->get('model_blog_post')->addSyndication($post_id, $syn_url);
    }

    $registry->get('cache')->delete('post.'.$post_id);

    $post_id = $registry->get('model_blog_wmqueue')->getNext();

} //end while
