    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['name']) ? 'p-name' : '')?>">
        <?php foreach($post['photo'] as $photo){ ?>
        <img src="<?php echo $photo['path']?>" class="u-photo photo-post" <?php if(isset($photo['alt'])) { echo 'alt="'.$photo['alt'].'"'; } ?> /><br>
        <?php } ?>
         <h2 class="h-measure p-weight">
             Weight: <data class="p-num" value="<?php echo $post['weight_value']?>"><?php echo $post['weight_value']?></data><data class="p-unit" value="<?php echo $post['weight_unit']?>"><?php echo $post['weight_unit']?></data>
         </h2>
          <?php 
              echo $post['body_html'];
              echo $post['syndication_extra'];
          ?><br>
              View my progress at <a href="https://ben.thatmustbe.me/weight/">https://ben.thatmustbe.me/weight/</a>
      
      </div><!-- .entry-content -->
