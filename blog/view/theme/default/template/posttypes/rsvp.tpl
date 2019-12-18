    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['name']) ? 'p-name' : '')?>">
      <?php 
        if(isset($post['rsvp']) && !empty($post['rsvp'])) {?>
            <i class="fa fa-calendar"></i> <a class="eventlink" href="<?php echo $post['replyto']?>">Event</a><br>
            <i class="fa fa-envelope-o"></i> <data class="p-rsvp" value="<?php echo $post['rsvp']?>">
                <?php echo (strtolower($post['rsvp']) == 'yes' ? 'Attending' : 'Not Attending' );?>
            </data><br>
            <?php echo $post['body_html'];
          } else {
            echo $post['body_html'];
          }
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
