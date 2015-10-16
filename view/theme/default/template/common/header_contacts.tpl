<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php foreach ($metas as $meta) { ?>
        <meta name="<?php echo $meta['name']; ?>" content="<?php echo $meta['content']; ?>" />
        <?php } ?>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="/view/theme/default/stylesheet/normalize.css">
        <link rel="stylesheet" href="/view/theme/default/stylesheet/main.css">

        <link rel="webmention" href="<?php echo $webmention_handler?>" />
        <link rel="authorization_endpoint" href="<?php echo $authorization_endpoint ?>">
        <link rel="token_endpoint" href="<?php echo $token_endpoint ?>">
        <link rel="micropub" href="<?php echo $micropub_endpoint ?>">
        <link rel="profile" href="http://microformats.org/profile/specs" />
        <link rel="profile" href="http://microformats.org/profile/hatom" />

        <link rel='stylesheet' href='/view/theme/default/stylesheet/contacts.css' type='text/css' media='all' />
        <link href="/view/shared/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet">
        <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
        <?php } ?>
        <?php if ($icon) { ?>
        <link href="<?php echo $icon; ?>" rel="icon shortcut" />
        <meta name="mobile-web-app-capable" content="yes" />
        <link rel="apple-touch-icon-precomposed" href="<?php echo $icon?>" />
        <?php } ?>
        <script src="/view/javascript/cubiq-add-to-homescreen-a94cf94/src/addtohomescreen.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->

    </head>


