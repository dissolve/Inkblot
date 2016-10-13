    <?php if(!empty($post['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" ><?php echo $post['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content">
        <?php foreach($post['video'] as $video){ ?>
            <video controls>
                <source class="u-video" src="<?php echo $video['path']?>" type="video/mp4" >
                <a href="<?php echo $video['path']?>" >Link</a>
            </video>
        <?php } ?>
        <?php echo $post['body_html'];?>
        <?php echo $post['syndication_extra'];?>
      </div><!-- .entry-content -->
