<?php echo $header; ?>
<?php date_default_timezone_set(LOCALTIMEZONE); ?> 
<div class="h-feed" id="posts-stream">
<div style="display:none;" class="p-name">Main Feed</div>
<?php foreach($posts as $post) { ?>
<article id="post-<?php echo $post['post_id']?>" class="<?php echo $post['post_type']?> h-entry <?php echo ($post['draft'] == 1 ? 'draft':'') ?> <?php echo ($post['deleted'] == 1 ? 'deleted':'') ?>" >
  <header class="entry-header">
    <?php if($post['post_type'] != 'listen'){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url" title="Permalink to <?php echo $post['title']?>" rel="bookmark"><?php echo $post['title']?></a></h1>
    <?php } ?>

        <div class="entry-meta">      
      <span class="sep">Posted on </span>
        <a href="<?php echo $post['permalink']?>" title="<?php echo date("g:i A", strtotime($post['timestamp']))?>" rel="bookmark" class="u-url"> <time class="dt-published" datetime="<?php echo date("c", strtotime($post['timestamp']))?>" ><?php echo date("F j, Y", strtotime($post['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="p-author h-card"><img alt='' src='<?php echo $author_image?>' class='u-photo avatar photo' height='40' width='40' /> <a class="u-url p-name" href="<?php echo $post['author']['link']?>" title="<?php echo $post['author']['display_name']?>" rel="author"><?php echo $post['author']['display_name']?></a></span></address>
        <?php if($post['replyto']) { ?>
            <div class="repyto">
               In Reply To <a class="u-in-reply-to" rel="in-reply-to" href="<?php echo $post['replyto']?>">This</a>
            </div>
        <?php }  // end if replyto?>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

     <?php if(isset($post['excerpt_html'])) { ?>
      <div class="p-summary">
     <?php } else { ?>
      <div class="entry-content e-content">
     <?php } ?>
        <?php if(isset($post['bookmark']) && !empty($post['bookmark'])) { ?>
            <i class="fa fa-bookmark-o"></i> 
            <a class="u-bookmark-of" href="<?php echo $post['bookmark']?>"><?php echo (isset($post['name']) && !empty($post['name'])?$post['name']:$post['bookmark'])?></a> <br>
        <?php } ?>
        <?php if(isset($post['like-of']) && !empty($post['like-of'])) { ?>
            <i class="fa fa-heart-o"></i> <br>
            I liked <a class="u-like-of" href="<?php echo $post['like-of']?>">This</a> page.
        <?php } ?>
        <?php if(isset($post['following']) && !empty($post['following'])) { ?>
            <?php echo $post['author']['display_name'] . 
             ($post['post_type'] == 'follow' ? ' followed ' : ' unfollowed ' ) .
            '<a class="u-follow-of h-card" href="'.$post['following']['url'].'" >'.
            (isset($post['following']['photo']) && !empty($post['following']['photo']) ? '<img class="u-photo" style="width:40px;" src="'.$post['following']['photo'].'" />' : '' ).
            $post['following']['name'].
            '</a>'; ?>
        <?php } ?>
        <?php if(isset($post['like-of']) && !empty($post['like-of'])) { ?>
            <i class="fa fa-heart-o"></i> <br>
            I liked <a class="u-like-of" href="<?php echo $post['like-of']?>">This</a> page.
        <?php } ?>
        <?php if($post['image_file']) { ?>
            <img src="<?php echo $post['image_file']?>" class="u-photo photo-post" /><br>
        <?php } ?>
        <?php if($post['audio_file']) { ?>
                <a href="<?php echo $post['audio_file']?>">Audio</a>
        <?php } ?>
        <?php if($post['video_file']) { ?>
                <a href="<?php echo $post['video_file']?>">Video</a>
        <?php } ?>
        <?php if($post['post_type'] == 'listen'){ ?>
            <?php echo 'I listend To <span class="song-title">'.$post['title'].'</span> by <span class="song-artist">'.$post['artist'].'</span>.'; ?>
      
        <?php  } ?>
        <?php if(isset($post['rsvp']) && !empty($post['rsvp'])) { ?>
            <i class="fa fa-calendar"></i>
               <a class="eventlink" href="<?php echo $post['replyto']?>">Event</a>

<br>
            <i class="fa fa-envelope-o"></i>
            <data class="p-rsvp" value="<?php echo $post['rsvp']?>">
            <?php echo (strtolower($post['rsvp']) == 'yes' ? 'Attending' : 'Not Attending' );?>
            </data><br>
        <?php } ?>
             <?php if(isset($post['excerpt_html'])) {?>
                <?php echo $post['excerpt_html']?>
             <?php } else { ?>
                <?php echo $post['body_html']?>
             <?php } ?>
            <?php if(isset($post['place_name']) && !empty($post['place_name'])){ 
            echo "<br>Checked In At ".$post['place_name'];
            } ?>
            <?php if(isset($post['location']) && !empty($post['location'])){
              $joined_loc = str_replace('geo:', '', $post['location']);
              $latlng = explode($joined_loc, ',');
              echo '<br>';
              echo '<img id="map" style="width: 200px; height: 200px" src="//maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap&markers=size:mid%7Ccolor:blue%7C'. $joined_loc.'"/>';
            } ?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
             <?php if(isset($post['excerpt_html'])) {?>
                <a href="<?php echo $post['permalink']?>" class="u-url">More...</a>
             <?php } ?>
    <?php if($post['comment_count'] > 0) { ?>
    <span class="comments-link"><a href="<?php echo $post['permalink']?>#comments" title="Comments for <?php echo $post['title']?>"><i class="fa fa-comment-o"></i> <?php echo $post['comment_count'] ?></a></span>
    <span class="sep"> | </span>
    <?php } ?>

    <?php if($post['like_count'] > 0) { ?>
    <span class="likes-link"><a href="<?php echo $post['permalink']?>#likes" title="Likes of <?php echo $post['title']?>"><i class="fa fa-heart-o"></i> <?php echo $post['like_count']?></a></span>
    <span class="sep"> | </span>
    <?php } ?>
  
  <?php if($post['categories']){ ?>
      <?php foreach($post['categories'] as $category) { ?>
          <?php if(isset($category['person_name'])){ ?>
              <span class="category-link"><a class="u-category h-card" href="<?php echo $category['url']?>" title="<?php echo $category['url']?>"><?php echo $category['person_name']?></a></span>
          <?php } else { ?>
              <span class="category-link"><a class="u-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
          <?php } ?>
  
      <?php } // end for post_categories as category ?>
  <?php } // end if post_categories ?>
  <?php if(!empty($post['syndications'])){ ?>
    <div class="syndications">
    <?php foreach($post['syndications'] as $elsewhere){ ?>

      <?php if(isset($elsewhere['image'])){ ?>
      <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" ><img src="<?php echo $elsewhere['image']?>" title="<?php echo $elsewhere['site_name']?>" /></a>
      <?php } else { ?>
      <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" ><i class="fa fa-link"></i></a>
      <?php } ?>
      
    <?php } //end foreach ?>
    </div>
  <?php } ?>
    <div class="admin-controls">
      <?php foreach($post['actions'] as $actiontype => $action){ ?>
      <indie-action do="<?php echo $actiontype?>" with="<?php echo $post['permalink']?>">
      <a href="<?php echo $action['link'] ?>" title="<?php echo $action['title']?>"><?php echo $action['icon']?></a>
      </indie-action>
      <?php } ?>
    </div>
  </footer><!-- #entry-meta --></article><!-- #post-<?php echo $post['post_id']?> -->

<?php } //end foreach posts ?>
</div>
<?php echo $footer; ?>
