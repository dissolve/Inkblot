    <?php if(!empty($post['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" ><?php echo $post['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content unfollow <?php echo (empty($post['title']) ? 'p-name' : '')?>">
      <?php 

          echo $post['author']['display_name'] . 
            ' unfollowed <a class="u-follow-of h-card" href="'.$post['following']['url'].'" >'.
            (isset($post['following']['photo']) && !empty($post['following']['photo']) ? '<img class="u-photo" style="width:40px;" src="'.$post['following']['photo'].'" />' : '' ).
            $post['following']['name'].
            '</a>';
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
