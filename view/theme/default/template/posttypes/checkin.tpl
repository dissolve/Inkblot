
    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content p-note <?php echo (empty($post['name']) ? 'p-name' : '')?>">
      <?php 
          echo $post['body_html'];
          echo '<span class="p-geo">';
          if(isset($post['place_name']) && !empty($post['place_name'])){
              echo "<br>";
              echo '<span class="p-name">Checked In At '.$post['place_name']. '</span>';
          }
          if(isset($post['location']) && !empty($post['location'])){
              // echo "<br>".$post['location'];
              $joined_loc = str_replace('geo:', '', $post['location']);
              $latlng = explode($joined_loc, ',');
              echo '<br>';
              echo '<img class="p-map" id="map" style="width: 400px; height: 300px" src="//maps.googleapis.com/maps/api/staticmap?zoom=13&size=400x300&maptype=roadmap&markers=size:mid%7Ccolor:blue%7C'.$joined_loc.'"/>';
              echo '<span class="h-geo">';
              echo '<data class="p-latitude" value="'.$latlng[0].'"></data>';
              echo '<data class="p-longitude" value="'.$latlng[1].'"></data>';
              echo '</span>'; //end h-geo

          }
          echo '</span>'; //end p-geo
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
