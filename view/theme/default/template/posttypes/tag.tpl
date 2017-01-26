    <?php if(!empty($post['name'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['name']?>" rel="bookmark" ><?php echo $post['name']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content tag <?php echo (empty($post['name']) ? 'p-name' : '')?>">
            <i class="fa fa-heart-o"></i><br>
      <?php 
	if(isset($post['tag_person']) && !empty($post['tag_person'])){
          echo $post['author']['display_name'] . 
	    ' tagged <a class="u-category h-card" href="'.$post['tag_url'].'" '.
                (isset($post['tag_shape']) && !empty($post['tag_shape']) ? 'shape="'.$post['tag_shape'].'" ': '').
                (isset($post['tag_coords']) && !empty($post['tag_coords']) ? 'coords="'.$post['tag_coords'].'" ': '').
                '>'.
                $post['tag_person'].
	    '</a> in '.
            '<a class="u-tag-of" href="'.$post['like-of'].'">this post</a>.';
	} else {
          echo $post['author']['display_name'] . ' tagged <a class="u-tag-of" href="'.$post['like-of'].'">this post</a> with <span class="p-category">'.$post['tag_category'].'</span>.' ;
	}
          echo $post['body_html'];
          echo $post['syndication_extra'];
      ?>
      
      </div><!-- .entry-content -->
