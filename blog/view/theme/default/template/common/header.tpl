<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" lang="en-US">
<![endif]-->
<!--[if IE 7]>
<html id="ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html id="ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<?php foreach ($metas as $meta) { ?>
<meta name="<?php echo $meta['name']; ?>" content="<?php echo $meta['content']; ?>" />
<?php } ?>
<title><?php echo $title; ?></title>
<link rel="webmention" href="<?php echo $webmention_handler?>" />
<link rel="authorization_endpoint" href="<?php echo $authorization_endpoint ?>">
<link rel="token_endpoint" href="<?php echo $token_endpoint ?>">
<link rel="profile" href="http://microformats.org/profile/specs" />
<link rel="profile" href="http://microformats.org/profile/hatom" />

<link rel='stylesheet' id='sempress-style-css'  href='/blog/view/theme/default/stylesheet/stylesheet.css' type='text/css' media='all' />
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
</head>
<body class="home blog custom-background multi-column single-author custom-header hfeed h-feed feed" itemscope="" itemtype="http://schema.org/Blog">
<div id="page">
  <header id="branding" role="banner">
    <h1 id="site-title" itemprop="name" class="p-name"><a href="<?php echo $home?>" title="<?php echo $site_title?>" rel="home" itemprop="url" class="u-url url"><?php echo $site_title?></a></h1>
    <h2 id="site-description" itemprop="description" class="p-summary e-content"><?php echo $site_subtitle?></h2>
    
          <img src="/image/uploaded/rainbow.jpg" height="200" width="950" alt="header image" id="site-image" />
        
    <nav id="access" role="navigation">
      <h1 class="assistive-text section-heading"><a href="#access" title="Main menu">Main menu</a></h1>
      <div class="skip-link screen-reader-text"><a href="#content" title="Skip to content">Skip to content</a></div>

      <div class="menu"><ul><li class="current_page_item"><a href="<?php echo $home?>">Home</a></li></ul></div>
    </nav><!-- #access -->
  </header><!-- #branding -->

  <div id="main">
    <section id="primary">
      <main id="content" role="main">

      
          <nav id="nav-above">
    <h1 class="assistive-text section-heading">Post navigation</h1>

  
  </nav><!-- #nav-above -->
