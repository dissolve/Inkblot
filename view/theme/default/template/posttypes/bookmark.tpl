
      <div class="entry-content e-content bookmark <?php echo (empty($post['name']) ? 'p-name' : '')?>">
            <i class="fa fa-bookmark-o"></i>
            <a class="u-bookmark-of" href="<?php echo $post['bookmark']?>"><?php echo (isset($post['name']) && !empty($post['name']) ? $post['name']:$post['bookmark'])?></a> <br>
      <?php 
          echo $post['body_html'];
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
