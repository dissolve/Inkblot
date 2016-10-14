
    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content like <?php echo (empty($post['name']) ? 'p-name' : '')?>">
            <i class="fa fa-heart-o"></i><br>
      <?php 
          echo 'I liked <a class="u-like-of" href="'.$post['like-of'].'">This</a> page.';
          echo $post['body_html'];
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
