    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['name']) ? 'p-name' : '')?>">
      <?php 
          if(isset($post['rsvp']) && !empty($post['rsvp'])) {
            echo '<data class="p-rsvp" value="'.$post['rsvp'].'">';
            echo $post['body_html'];
            echo '</data>';
          } else {
            echo $post['body_html'];
          }
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
