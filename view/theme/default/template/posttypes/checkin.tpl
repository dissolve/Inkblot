
    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content p-note <?php echo (empty($post['name']) ? 'p-name' : '')?>">
      <?php 
          echo $post['body_html'];
          echo '<span class="p-geo">';
          if(isset($post['location']) && isset($post['location']['name'])){ 
              echo "<br>";
              echo '<span class="p-name">Checked In At '.$post['location']['name']. '</span>';
          }
          if(isset($post['location']) && isset($post['location']['latitude']) && isset($post['location']['longitude']) ){
              echo '<br>';
              echo '<img id="map" style="width: 400px; height: 300px" src="//maps.googleapis.com/maps/api/staticmap?zoom=13&size=400x300&maptype=roadmap&markers=size:mid%7Ccolor:blue%7C'. $post['location']['latitude'] .',' . $post['location']['longitude'].'"/>';
              echo '<span class="h-geo">';
              echo '<data class="p-latitude" value="'.$post['location']['latitude'].'"></data>';
              echo '<data class="p-longitude" value="'.$post['location']['logitude'].'"></data>';
              echo '</span>'; //end h-geo

          }
          echo '</span>'; //end p-geo
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
