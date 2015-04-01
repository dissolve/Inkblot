<?php echo $header; ?>
<?php date_default_timezone_set(LOCALTIMEZONE); ?> 
<div class="h-entry hentry">
  <div class="context_history">
  <?php foreach($post['context'] as $ctx){ ?>
        <div class="comment h-cite entry-meta" >
            <div class="comment_header">    
                <span class="minicard h-card vcard author p-author">
                    <img class="comment_author logo u-photo" src="<?php echo $ctx['author_image']?>" alt="<?php echo $ctx['author_name']?>" width="48" />
                </span>
                <a class="p-name fn value name u-url" href="<?php echo $ctx['author_url']?>"><?php echo $ctx['author_name']?></a>
                <a href="<?php echo $ctx['source_url']?>" class="u-url permalink"><time class="date dt-published" datetime="<?php echo $ctx['timestamp']?>"><?php echo date("F j, Y g:i A", strtotime($ctx['timestamp']))?></time></a>
            </div>
                                                           
            <div class="h-cite entry-meta comment_body">
                <div class="quote-text"><div class="e-content p-name"><?php echo $ctx['body']?></div></div>
            </div>
        </div>
    <?php } ?>
    </div>

          <article id="rsvp-<?php echo $post['post_id']?>" class="rsvp-<?php echo $post['post_id']?> rsvp type-rsvp status-publish format-standard category-uncategorized <?php echo ($post['draft'] == 1 ? 'draft':'') ?> <?php echo ($post['deleted'] == 1 ? 'deleted':'') ?>">

    <header class="entry-meta comment_header">
        <div class="entry-meta">      
        <span class="author p-author vcard hcard h-card">
            <img alt='' src='<?php echo $post['author_image']?>' class='u-photo ' height='40' width='40' /> 
            <span class="p-name"><a class="url uid u-url u-uid fn" href="<?php echo $post['author']['link']?>" title="<?php echo $post['author']['display_name']?>" rel="author">
                <?php echo $post['author']['display_name']?>
            </a></span>
        </span>
        <a href="<?php echo $post['permalink']?>" title="<?php echo date("g:i A", strtotime($post['timestamp']))?>" rel="bookmark" class="permalink u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo $post['timestamp']?>" ><?php echo date("F j, Y g:i A", strtotime($post['timestamp']))?></time></a>

        <a href="<?php echo $post['shortlink']?>" title="Shortlink" rel="shortlink" class="shortlink u-shortlink u-url">Shortlink</a>

        <span class='in_reply_url'>
        <?php if(!empty($post['replyto'])){ ?>
       In Reply To <a class="u-in-reply-to u-url" rel="in-reply-to" href="<?php echo $post['replyto']?>"><?php echo $post['replyto']?></a>
       <?php } ?>
       </span>
        </div><!-- .entry-meta -->
    </header>
  <div class='articlebody'>

    <?php if(!empty($post['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" ><?php echo $post['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content <?php echo (empty($post['title']) ? 'p-name' : '')?>">
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
  </div>
  
  <footer class="entry-meta">

  <?php if(!empty($post['syndications'])){ ?>
    <div id="syndications">
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

  <?php if($post['categories']){ ?>
      <?php foreach($post['categories'] as $category) { ?>
          <?php if(isset($category['person_name'])){ ?>
              <span class="category-link"><a class="u-category" href="<?php echo $category['url']?>" title="<?php echo $category['person_name']?>"><?php echo $category['person_name']?></a></span>
          <?php } else { ?>
              <span class="category-link"><a class="u-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
          <?php } ?>
  
      <?php } // end for post_categories as category ?>
  <?php } // end if post_categories ?>


    <?php if($post['like_count'] > 0) { ?>
<br>
<span id="general-likes"><a id="like"><h3 class="widget-title"><?php echo $post['like_count'] . ($post['like_count'] > 1 ? ' People' : ' Person')?> Liked This Post</h3></a>
        <?php foreach($post['likes'] as $like){?>
                <span class="likewrapper">
                <a href="<?php echo (isset($like['author_url']) ? $like['author_url']: $like['source_url'])?>" rel="nofollow">
                    <img class='like_author' src="<?php echo (isset($like['author_image']) ? $like['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($like['author_name']) ? $like['author_name']: 'Author Image') ?>" /></a>
                </span>
        <?php } ?>
    <div style="clear:both"></div>
	</span>
    <?php } ?>
  </footer><!-- #entry-meta --></article><!-- #post-<?php echo $post['post_id']?> -->

    <?php if($post['comment_count'] > 0) { ?>
    <div class="comments">
        <?php foreach($post['comments'] as $comment) { ?>
            <div class="comment">
                <div class='comment_header'>
                    <span class="minicard h-card vcard author p-author">
                        <img class='comment_author' src="<?php echo (isset($comment['author_image']) ? $comment['author_image']: '/image/person.jpg') ?>" />
                    </span>
                    <a class="p-name fn value name u-url" href="<?php echo (isset($comment['author_url']) ? $comment['author_url']: $comment['source_url'])?>" rel="nofollow" title="<?php echo (isset($comment['author_name']) ? $comment['author_name']: 'View Author') ?>" />
                    <?php echo (isset($comment['author_name']) ? $comment['author_name']: 'A Reader') ?> <!-- <?php echo $comment['source_name']?> -->
                    </a>

                    <a href="<?php echo $comment['source_url']?>" class="u-url permalink"><time class="date dt-published" datetime="<?php echo $comment['timestamp']?>"><?php echo date("F j, Y g:i A", strtotime($comment['timestamp']))?></time></a>
                    <?php if($comment['vouch_url']) { ?>
                        <a href="<?php echo $comment['vouch_url']?>" class="u-url vouch">Vouched</a>
                    <?php } ?>
                   <span class="other-controls">
                      <?php foreach($comment['actions'] as $actiontype => $action){ ?>
                      <indie-action do="<?php echo $actiontype?>" with="<?php echo $comment['permalink']?>">
                      <a href="<?php echo $action['link'] ?>" title="<?php echo $action['title']?>"><?php echo $action['icon']?></a>
                      </indie-action>
                      <?php } ?>
                  </span>
                </div>
                <div class='comment_body'>
                    <?php echo $comment['body']?>
                </div>
            </div>
        <?php } ?>
	</div>
    <?php } ?>
</div>

<?php echo $footer; ?>
