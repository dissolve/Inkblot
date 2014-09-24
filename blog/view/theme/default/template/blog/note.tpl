<?php echo $header; ?>
<div class="h-entry hentry">
  <div class="context_history">
  <?php foreach($note['context'] as $ctx){ ?>
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

          <article id="note-<?php echo $note['note_id']?>" class="note-<?php echo $note['note_id']?> note type-note status-publish format-standard category-uncategorized">

    <header class="entry-meta comment_header">
        <div class="entry-meta">      
        <span class="author p-author vcard hcard h-card">
            <img alt='' src='<?php echo $note['author_image']?>' class='u-photo ' height='40' width='40' /> 
            <span class="p-name"><a class="url uid u-url u-uid fn" href="<?php echo $note['author']['link']?>" title="View all notes by <?php echo $note['author']['display_name']?>" rel="author">
                <?php echo $note['author']['display_name']?>
            </a></span>
        </span>
        <a href="<?php echo $note['permalink']?>" title="<?php echo date("g:i A", strtotime($note['timestamp']))?>" rel="bookmark" class="permalink u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($note['timestamp']))?>" ><?php echo date("F j, Y g:i A", strtotime($note['timestamp']))?></time></a>

        <span class='in_reply_url'>
        <?php if(!empty($note['replyto'])){ ?>
       In Reply To <a class="u-in-reply-to u-url" rel="in-reply-to" href="<?php echo $note['replyto']?>"><?php echo $note['replyto']?></a>
       <?php } ?>
       </span>
        </div><!-- .entry-meta -->
    </header>
  <div class='articlebody'>

    <?php if(!empty($note['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $note['permalink']?>" class="u-url url" title="Permalink to <?php echo $note['title']?>" rel="bookmark" ><?php echo $note['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content p-note <?php echo (empty($note['title']) ? 'p-name' : '')?>">
      <?php echo $note['body_html'];?>
      <?php echo $note['syndication_extra'];?>
      
      </div><!-- .entry-content -->
  </div>
  
  <footer class="entry-meta">

  <?php if(!empty($note['syndications'])){ ?>
    <div id="syndications">
    <?php foreach($note['syndications'] as $elsewhere){ ?>

      <?php if(isset($elsewhere['image'])){ ?>
      <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" ><img src="<?php echo $elsewhere['image']?>" title="<?php echo $elsewhere['site_name']?>" /></a>
      <?php } else { ?>
      <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" ><i class="fa fa-link"></i></a>
      <?php } ?>
      
    <?php } //end foreach ?>
    </div>
  <?php } ?>

  <?php if($note['categories']){ ?>
      <?php foreach($note['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for note_categories as category ?>
  <?php } // end if note_categories ?>


    <?php if($note['like_count'] > 0) { ?>
<br>
<span id="general-likes"><a id="like"><h3 class="widget-title"><?php echo $note['like_count'] . ($note['like_count'] > 1 ? ' People' : ' Person')?> Liked This Note</h3></a>
        <?php foreach($note['likes'] as $like){?>
                <span class="likewrapper">
                <a href="<?php echo (isset($like['author_url']) ? $like['author_url']: $like['source_url'])?>" rel="nofollow">
                    <img class='like_author' src="<?php echo (isset($like['author_image']) ? $like['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($like['author_name']) ? $like['author_name']: 'Author Image') ?>" /></a>
                </span>
        <?php } ?>
    <div style="clear:both"></div>
	</span>
    <?php } ?>
  </footer><!-- #entry-meta --></article><!-- #note-<?php echo $note['note_id']?> -->

    <?php if($note['comment_count'] > 0) { ?>
        <?php foreach($note['comments'] as $comment) { ?>
            <div class="comment">
                <div class='comment_header'>
                    <span class="minicard h-card vcard author p-author">
                        <img class='comment_author' src="<?php echo (isset($comment['author_image']) ? $comment['author_image']: '/image/person.jpg') ?>"
                    </span>
                    <a class="p-name fn value name u-url" href="<?php echo (isset($comment['author_url']) ? $comment['author_url']: $comment['source_url'])?>" rel="nofollow" title="<?php echo (isset($comment['author_name']) ? $comment['author_name']: 'View Author') ?>" /></a>
                    <?php echo (isset($comment['author_name']) ? $comment['author_name']: 'A Reader') ?> <!-- <?php echo $comment['source_name']?> -->
                    </a>

                    <a href="<?php echo $comment['source_url']?>" class="u-url permalink"><time class="date dt-published" datetime="<?php echo $comment['timestamp']?>"><?php echo date("F j, Y g:i A", strtotime($comment['timestamp']))?></time></a>
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
