
    <?php if(!empty($post['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" ><?php echo $post['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content like <?php echo (empty($post['title']) ? 'p-name' : '')?>">
            <i class="fa fa-heart-o"></i><br>
      <?php 
          echo 'I liked <a class="u-like-of" href="'.$post['like-of'].'">This</a> page.';
          echo $post['body_html'];
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
