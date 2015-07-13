    <?php if(!empty($post['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" ><?php echo $post['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['title']) ? 'p-name' : '')?>">
        <img src="<?php echo $post['image_file']?>" class="u-photo photo-post" /><br>
        <?php echo $post['body_html'];?>
        <?php echo $post['syndication_extra'];?>
      </div><!-- .entry-content -->
