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
        <?php foreach ($metas as $meta) { ?>
        <meta name="<?php echo $meta['name']; ?>" content="<?php echo $meta['content']; ?>" />
        <?php } ?>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="/blog/view/theme/default/stylesheet/normalize.css">
        <link rel="stylesheet" href="/blog/view/theme/default/stylesheet/main.css">

        <link rel="webmention" href="<?php echo $webmention_handler?>" />
        <link rel="authorization_endpoint" href="<?php echo $authorization_endpoint ?>">
        <link rel="token_endpoint" href="<?php echo $token_endpoint ?>">
        <link rel="micropub" href="<?php echo $micropub_endpoint ?>">
        <link rel="profile" href="http://microformats.org/profile/specs" />
        <link rel="profile" href="http://microformats.org/profile/hatom" />

        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
        <?php } ?>
        <?php if ($icon) { ?>
        <link href="<?php echo $icon; ?>" rel="icon" />
        <?php } ?>

    <style>
      body{background:black url('/image/static/android-bubbles.jpg') no-repeat center center fixed; 
        overflow-x:hidden;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        margin:0;padding:0px;
        font-family: "Droid Sans", sans-serif;
      }
      ol {list-style-type:none;padding:0; margin:0;}
      ol li a img{width:60px;height:60px;border-radius:10px;padding:0;margin:0;}
      ol li{float:left;width:80px;height:80px;text-align:center;margin:20px 5px 0;}
      span.iconlabel{color:white;font-size:10pt;text-decoration:none; white-space: nowrap}
      a{text-decoration:none;}
      h1{ clear:both; text-align:center; color:white;width:100%; background-color:black;margin:0;z-index:100;position:relative;}
      .links {width:100%;background:transparent;border:0; border-radius: 40px;position:relative;overflow:hidden;padding:5px 0 25px;z-index:0;box-shadow:0 0 0 30px black;}
      .black {background-color:black;color:white;}
      ul {margin:0;}
      #main {background:black; color:white; padding:15px 10px}
      #main a {color:white;}
      #main a:visited {color:white;}

    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->

    </head>


<body class="h-card">
<h1 class='p-name'>
  <span class='p-given-name'><?php echo $author['first_name'] ?></span>
  <span class='p-family-name'><?php echo $author['last_name'] ?></span>
</h1>

<div class="links">
<ol>
  <?php foreach($mydata as $data){?>
      <li>
      <a href="<?php echo $data['url'];?>" rel="<?php echo $data['rel']?>" title="<?php echo $data['title'];?>" target="<?php echo $data['target'];?>"><img src="<?php echo $data['image']?>" alt="" /><span class="iconlabel"><?php echo $data['value'];?></span></a></li>
  <?php } ?>
</ol>
</div>

<div id="page">

  <div id="main">
    <section id="primary">
      <main id="content" role="main">


<section id="recent-posts-2" class=" ">
<h3 class="widget-title">Recent Posts</h3>
<ul>
<?php foreach($recent_posts as $post){ ?>
    <li>
        <a href="<?php echo $post['permalink']?>"><?php echo $post['title']?></a>
    </li>
<?php } // end foreach recent_post ?>
</ul>
</section>


<section id="login" class="widget">
    <?php if(isset($user_name)) { ?>
    <h3 class="widget-title">Signed In As "<?php echo $user_name?>"</h3>
    <ul><li>
        <a href="<?php echo $logout?>">Sign Out</a>
    </li></ul>
    <?php } else { ?>
    <h3 class="widget-title">Sign In with IndieAuth</h3>
    <ul><li>
        <form action="<?php echo $auth_endpoint?>" method="get">
          <label for="indie_auth_url">Web Address:</label>
          <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
          <p><button type="submit">Sign In</button></p>
          <input type="hidden" name="client_id" value="<?php echo $client_id?>" />
          <input type="hidden" name="redirect_uri" value="<?php echo $auth_page?>" />
        </form>
    </li></ul>
    <?php } ?>


      </main><!-- #content -->
    </section><!-- #primary -->

</div><!-- #main -->


</div><!-- #page -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_analytics_id?>', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
