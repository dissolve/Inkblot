<?php echo $header; ?>

          <article id="note-<?php echo $note['note']?>" class="note-<?php echo $note['note']?> note type-note status-publish format-standard category-uncategorized h-entry hentry h-as-article" itemprop="blogNote" itemscope="" itemtype="http://schema.org/BlogNoteing">
  <header class="entry-header">
    <h1 class="entry-title p-name" itemprop="name headline"><a href="<?php echo $note['permalink']?>" class="u-url url" title="Permalink to <?php echo $note['title']?>" rel="bookmark" itemprop="url"><?php echo $note['title']?></a></h1>

        <div class="entry-meta">      
      <span class="sep">Posted on </span>
        <a href="<?php echo $note['permalink']?>" title="<?php echo date("g:i A", strtotime($note['timestamp']))?>" rel="bookmark" class="url u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($note['timestamp']))?>" itemprop="dateModified"><?php echo date("F j, Y", strtotime($note['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="http://schema.org/Person"><img alt='' src='http://0.gravatar.com/avatar/<?php echo md5($note['author']['email_address'])?>?s=40&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D40&amp;r=G' class='u-photo avatar avatar-40 photo' height='40' width='40' /> <a class="url uid u-url u-uid fn p-name" href="<?php echo $note['author']['link']?>" title="View all notes by <?php echo $note['author']['display_name']?>" rel="author" itemprop="url"><span itemprop="name"><?php echo $note['author']['display_name']?></span></a></span></address>
        <?php if($note['replyto']) { ?>
            <div class="repyto">
               In Reply To <a class="u-url" rel="in-reply-to" href="<?php echo $note['replyto']?>">This</a>
            </div>
        <?php }  // end if replyto?>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

      <div class="entry-content e-content" itemprop="description articleBody">
      <?php echo $note['body_html'];?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
  
  <?php if($note['categories']){ ?>
      <?php foreach($note['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for note_categories as category ?>
  <?php } // end if note_categories ?>


    <?php if($note['like_count'] > 0) { ?>
<br>
<span id="general-likes" class="widget widget_links"><a id="like"><h3 class="widget-title"><?php echo $note['like_count'] . ($note['like_count'] > 1 ? ' People' : ' Person')?> Liked This Note</h3></a>
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
<br>
<span id="general-comments" class="widget widget_links"><a id="comment"><h3 class="widget-title">Comments:</h3></a>
        <?php foreach($note['comments'] as $comment){?>
                <div class="comment">
                <div class='comment_header'>
                <a href="<?php echo (isset($comment['author_url']) ? $comment['author_url']: $comment['source_url'])?>" rel="nofollow">
                    <img class='comment_author' src="<?php echo (isset($comment['author_image']) ? $comment['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($comment['author_name']) ? $comment['author_name']: 'Author Image') ?>" /></a>
                <a href="<?php echo $comment['source_url']?>" rel="nofollow">Permalink</a>
                <?php echo $comment['source_name']?>
                </div>
                <div class='comment_body'>
                <?php echo $comment['body']?>
                </div>
                </div>
        <?php } ?>
	</span>
    <?php } ?>

<?php echo $footer; ?>
