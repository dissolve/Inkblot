    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['name']) ? 'p-name' : '')?>">
        <?php foreach($post['photo'] as $photo){ ?>
        <img src="<?php echo $photo['path']?>" class="u-photo photo-post" <?php if(isset($photo['alt'])) { echo 'alt="'.$photo['alt'].'"'; } ?> /><br>
        <?php } ?>
        <?php echo $post['body_html'];?>
        <?php echo $post['syndication_extra'];?>
      </div><!-- .entry-content -->
