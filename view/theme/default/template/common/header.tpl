<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php foreach ($metas as $meta) { ?>
        <meta name="<?php echo $meta['name']; ?>" content="<?php echo $meta['content']; ?>" />
        <?php } ?>

        <link rel="stylesheet" href="/view/theme/default/stylesheet/normalize.css">
        <link rel="stylesheet" href="/view/theme/default/stylesheet/main.css">

        <link rel="webmention" href="<?php echo $webmention_handler?>" />
        <link rel="authorization_endpoint" href="<?php echo $authorization_endpoint ?>">
        <link rel="token_endpoint" href="<?php echo $token_endpoint ?>">
        <link rel="micropub" href="<?php echo $micropub_endpoint ?>">
        <link rel="logstore" href="<?php echo $logger_endpoint ?>">
        <link rel="me" href="<?php echo $public_whitelist ?>">
        <link rel="profile" href="http://microformats.org/profile/specs" />
        <link rel="profile" href="http://microformats.org/profile/hatom" />

        <link rel='stylesheet' id='sempress-style-css'  href='/view/theme/default/stylesheet/stylesheet.css' type='text/css' media='all' />
        <link href="/view/shared/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet">
        <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
        <?php } ?>
        <?php if ($icon) { ?>
        <link href="<?php echo $icon; ?>" rel="icon" />
        <link rel="apple-touch-icon-precomposed" href="<?php echo $icon?>" />
        <?php } ?>

        <script src="/view/javascript/vendor/modernizr-2.6.2.min.js"></script>
        <script src="/view/javascript/fragmention.js"></script>
        <script src="/view/javascript/intents.js"></script>
<?php /*        <script>
// Lazy-create and return an indie-config load promise
// The promise will be resolved with a config once the indie-config has been loaded
var loadIndieConfig = function () {

  // Create the Promise to return
  var loadPromise = new Promise(function (resolve) {

    // Parse the incoming messages
    var parseIndieConfig = function (message) {

      // Check if the message comes from the indieConfigFrame we added (or from some other frame)
      if (message.source !== indieConfigFrame.contentWindow) {
        return;
      }

      var indieConfig;

      // Try to parse the config, it can be malformed
      try {
        indieConfig = JSON.parse(message.data);
      } catch (e) {}

      // We're done – remove the frame and event listener
      window.removeEventListener('message', parseIndieConfig);
      indieConfigFrame.parentNode.removeChild(indieConfigFrame);
      indieConfigFrame = undefined;

      // And resolve the promise with the loaded indie-config
      resolve(indieConfig);
    };

    // Listen for messages from the added iframe and parse those messages
    window.addEventListener('message', parseIndieConfig);

    // Create a hidden iframe pointing to something using the web+action: protocol
    var indieConfigFrame = document.createElement('iframe');
    indieConfigFrame.src = 'web+action:load';
    document.getElementsByTagName('body')[0].appendChild(indieConfigFrame);
    indieConfigFrame.style.display = 'none';
  });

  // Ensure that subsequent invocations return the same promise
  loadIndieConfig = function () {
    return loadPromise;
  };

  return loadPromise;
};
</script> */ ?>
    </head>


<body class="home blog custom-background multi-column single-author custom-header" itemscope="" itemtype="http://schema.org/Blog">
<div id="page">
  <header class="h-card" id="branding" role="banner">
    <h1 id="site-title" itemprop="name" class="p-name"><a href="<?php echo $home?>" title="<?php echo $site_title?>" rel="home" itemprop="url" class="u-url url"><?php echo $site_title?></a></h1>
    <h2 id="site-description" itemprop="description" class="p-role p-summary e-content"><?php echo $site_subtitle?></h2>
        
        <?php if ($icon) { ?>
        <img src="<?php echo $icon; ?>" class="photo u-photo" style="display:none" />
        <?php } ?>
        <span class="p-x-pronoun-nominative" style="display:none"><?php echo $pro_nominative?></span>
        <span class="p-x-pronoun-oblique" style="display:none"><?php echo $pro_oblique ?></span>
        <span class="p-x-pronoun-posessive" style="display:none"><?php echo $pro_posessive ?></span>

  </header><!-- #branding -->

  <div id="main">
    <section id="primary">
      <main id="content" role="main">
      <?php if(isset($success)){ ?>
        <div class="success"><?php echo $success?></div>
      <?php } ?>
      <?php if(isset($error)){ ?>
        <div class="error"><?php echo $error?></div>
      <?php } ?>

      
      <nav id="nav-above">
        <h1 class="assistive-text section-heading">Post navigation</h1>
  
      </nav><!-- #nav-above -->
