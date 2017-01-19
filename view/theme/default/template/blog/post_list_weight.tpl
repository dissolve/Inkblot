<?php echo $header; ?>
<?php date_default_timezone_set(LOCALTIMEZONE); ?> 

<h1>
        <span class="p-author h-card">
 <a class="u-url" href="<?php echo $author['link']?>" title="<?php echo $author['display_name']?>">
<img alt='Author Image' src='<?php echo $author['image']?>' class='u-photo photo' height='40' width='40' title="<?php echo $author['display_name']?>"/>
</a>
<span class="p-name" style="display:none;"><?php echo $author['display_name']?></span>
</span>
<span class="p-name"><?php echo $title ?></span>
</h1>




  <script type="text/javascript">
  window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer",
    {
      title:{
       text: "Weight"   
     },
     theme: "theme1",
     animationEnabled: true,
     axisX: {
       valueFormatString: "YY-MM-DD"      
      },
      axisY:{
        valueFormatString: "#"
      },
     data: [
     {        
      type: "line",
      showInLegend: false,
      dataPoints: [        
<?php 
$datapoints_array = array();
foreach($posts as $post) {
    $datapoints_array[] = '{ x: new Date('.$post['year'].', '.$post['month'].', '.$post['day'].'), y: '.$post['weight_value'].', indexLabel: "'.$post['weight_value']." " . $post['weight_unit'].'", markerType: "circle",  markerColor: "#6B8E23", markerSize: 12}';
    
  }
$datapoints = implode(',',$datapoints_array);
echo $datapoints;
?>
    
      ]
    }
    ]
  });

chart.render();
}
</script>
<script type="text/javascript" src="/libraries/canvasjs/canvasjs.min.js"></script>

  <article id="chartContainer" style="height: 300px; width: 100%;">
  </article>


<?php foreach($posts as $post) { ?>
<article id="<?php echo $post['post_type']?>-<?php echo $post['id']?>" class="<?php echo $post['post_type']?> type-<?php echo $post['post_type']?> h-entry <?php echo ($post['draft'] == 1 ? 'draft':'') ?> <?php echo ($post['deleted'] == 1 ? 'deleted':'') ?>">
  <header class="entry-header">
    <?php if($post['post_type'] != 'listen'){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url" title="Permalink to <?php echo $post['name']?>" ><?php echo $post['name']?></a></h1>
    <?php } ?>

<?php if($post['post_type'] == 'snark'){ ?>
    <h3 class="snark_alert">Sarcasm Alert</h3>
<?php } ?>

        <div class="entry-meta">      
      <span class="sep">Posted on </span>
        <a href="<?php echo $post['permalink']?>" title="<?php echo date("g:i A", strtotime($post['published']))?>"  class="u-url"> <time class="dt-published" datetime="<?php echo $post['published']?>"><?php echo date("F j, Y", strtotime($post['published']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="p-author h-card"><img alt='' src='<?php echo $post['author_image']?>' class='u-photo avatar photo' height='40' width='40' /> <a class="u-url p-name" href="<?php echo $post['author']['link']?>" title="<?php echo $post['author']['display_name']?>" ><?php echo $post['author']['display_name']?></a></span></address>
        <?php if($post['in-reply-to']) { ?>
            <div class="repyto">
               In Reply To <a class="u-in-reply-to" href="<?php echo $post['in-reply-to']?>">This</a>
            </div>
        <?php }  // end if in-reply-to?>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

     <?php if(isset($post['summary_html'])) { ?>
      <div class="p-summary">
     <?php } else { ?>
      <div class="entry-content e-content">
     <?php } ?>
        <?php if(isset($post['weight_value']) && !empty($post['weight_value'])) { ?>
         <h2 class="h-measure p-weight">
             Weight: <data class="p-num" value="<?php echo $post['weight_value']?>"><?php echo $post['weight_value']?></data><data class="p-unit" value="<?php echo $post['weight_unit']?>"><?php echo $post['weight_unit']?></data>
         </h2>
        <?php } ?>
        <?php if(isset($post['bookmark-of']) && !empty($post['bookmark-of'])) { ?>
            <i class="fa fa-bookmark-o"></i> 
            <a class="u-bookmark-of" href="<?php echo $post['bookmark-of']?>"><?php echo (isset($post['name']) && !empty($post['name'])?$post['name']:$post['bookmark-of'])?></a> <br>
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
            <i class="fa fa-heart-o"></i> <a class="u-like-of" href="<?php echo $post['like-of']?>"><?php echo htmlentities($post['like-of']);?></a><br>
        <?php } ?>
        <?php foreach($post['photo'] as $photo){ ?>
            <img src="<?php echo $photo['path']?>" class="u-photo photo-post" <?php if(isset($photo['alt'])) { echo 'alt="'.$photo['alt'].'"'; } ?> /><br>
        <?php } ?>
        <?php foreach($post['audio'] as $audio){ ?>
            <a href="<?php echo $audio['path']?>">Audio</a>
        <?php } ?>
        <?php foreach($post['video'] as $video){ ?>
            <a href="<?php echo $video['path']?>">Video</a>
        <?php } ?>
        <?php if($post['post_type'] == 'listen'){ ?>
            <?php echo 'I listend To <span class="song-title">'.$post['name'].'</span> by <span class="song-artist">'.$post['artist'].'</span>.'; ?>
      
        <?php  } ?>
        <?php if(isset($post['rsvp']) && !empty($post['rsvp'])) { ?>
            <i class="fa fa-calendar"></i>
               <a class="eventlink" href="<?php echo $post['in-reply-to']?>">Event</a>

<br>
            <i class="fa fa-envelope-o"></i>
            <data class="p-rsvp" value="<?php echo $post['rsvp']?>">
            <?php echo (strtolower($post['rsvp']) == 'yes' ? 'Attending' : 'Not Attending' );?>
            </data><br>
        <?php } ?>
             <?php if(isset($post['summary_html']) && !empty($post['summary_html'])) {?>
                <?php echo $post['summary_html']?>
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
              echo '<img id="map" style="width: 400px; height: 300px" src="//maps.googleapis.com/maps/api/staticmap?zoom=13&size=400x300&maptype=roadmap&markers=size:mid%7Ccolor:blue%7C'. $joined_loc.'"/>';
            } ?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
             <?php if(isset($post['summary_html'])) {?>
                <a href="<?php echo $post['permalink']?>" class="u-url">More...</a>
             <?php } ?>

             
    <?php if(!empty($post['reacjis']) ) { ?>
    <span id="general-reacjis">
        <?php foreach($post['reacjis'] as $reacji => $rdata){ ?>
        <span class="reacji-container">
                <span class="reacji"><?php echo $reacji?></span>
                <span class="reacji-count"><?php echo count($rdata)?></span>
        </span>
        <?php } ?>

        <div style="clear:both"></div>
        </span>
    <?php } ?>


    <?php if($post['comment_count'] > 0) { ?>
    <span class="comments-link"><a href="<?php echo $post['permalink']?>#comments" title="Comments for <?php echo $post['name']?>"><i class="fa fa-comment-o"></i> <?php echo $post['comment_count'] ?></a></span>
    <span class="sep"> | </span>
    <?php } ?>

    <?php if($post['like_count'] > 0) { ?>
    <span class="likes-link"><a href="<?php echo $post['permalink']?>#likes" title="Likes of <?php echo $post['name']?>"><i class="fa fa-heart-o"></i> <?php echo $post['like_count']?></a></span>
    <span class="sep"> | </span>
    <?php } ?>
  
    <?php if($post['repost_count'] > 0) { ?>
    <span class="reposts-link"><a href="<?php echo $post['permalink']?>#reposts" title="reposts of <?php echo $post['name']?>"><i class="fa fa-retweet"></i> <?php echo $post['repost_count']?></a></span>
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
      <a class="u-syndication" href="<?php echo $elsewhere['url']?>" ><img src="<?php echo $elsewhere['image']?>" title="<?php echo $elsewhere['site_name']?>" /></a>
      <?php } else { ?>
      <a class="u-syndication" href="<?php echo $elsewhere['url']?>" ><i class="fa fa-link"></i></a>
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

   <?php if(isset($post['created_by'])){ 
        $client_id = strtolower($post['created_by']);
        if(preg_match('/https?:\/\/.+\..+/', $client_id)){ ?>
            <div class="client_line">Created by <a class="u-x-client-id" href="<?php echo $post['created_by']?>"><?php echo $post['created_by']?></a></div>
        <?php } else { ?>
            <div class="client_line">Created by <span class="p-x-client-id"><?php echo $post['created_by']?></span></div>
        <?php } ?>
    <?php } ?>
  </footer><!-- #entry-meta --></article><!-- #post-<?php echo $post['id']?> -->

<?php } //end foreach posts ?>
<?php echo $footer; ?>
