<?php echo $header?>


<body class="h-card">
<h1>
  <img class="u-photo" src="/image/static/icon_64.jpg" />
  <span class="p-name">
    <span class='p-given-name'><?php echo $author['first_name'] ?></span>
    <span class='p-family-name'><?php echo $author['last_name'] ?></span>
  </span>
<?php if(isset($author['url'])){ ?>
    <a class="u-url" href="<?php echo $author['url']?>">Website</a>
<?php } ?>
</h1>

<div class="links">
<ol>
  <?php foreach($contact as $data){?>
      <li>
      <a href="<?php echo $data['url'];?>" rel="<?php echo $data['rel']?>" title="<?php echo $data['title'];?>" target="<?php echo $data['target'];?>"><img src="<?php echo $data['image']?>" alt="" /><span class="iconlabel"><?php echo $data['value'];?></span></a></li>
  <?php } ?>
</ol>
</div>

<div id="page">

  <div id="main">
    <section id="primary">
      <main id="content" role="main">

<h2>Elsewhere</h2>
<div class="links elsewhere">
<ol>
  <?php foreach($elsewhere as $data){?>
      <li>
      <a href="<?php echo $data['url'];?>" rel="<?php echo $data['rel']?>" title="<?php echo $data['title'];?>" target="<?php echo $data['target'];?>"><img src="<?php echo $data['image']?>" alt="" /><span class="iconlabel"><?php echo $data['value'];?></span></a></li>
  <?php } ?>
</ol>
</div>

<section id="recent-posts-2" class=" ">
<h3 class="widget-title">Recent Posts</h3>
<ul>
<?php foreach($recent_posts as $post){ ?>
    <li>
        <a href="<?php echo $post['permalink']?>"><?php echo $post['name']?></a>
    </li>
<?php } // end foreach recent_post ?>
</ul>
</section>

</div><!-- #main -->


</div><!-- #page -->

<?php echo $footer?>
