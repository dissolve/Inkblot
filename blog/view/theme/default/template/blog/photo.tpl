<?php echo $header; ?>

          <article id="photo-<?php echo $photo['photo_id']?>" class="photo-<?php echo $photo['photo_id']?> photo type-photo status-publish format-standard category-uncategorized h-entry hentry h-as-article" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">

      <div class="entry-content e-content p-note" itemprop="description articleBody">
        <img src="<?php echo $photo['image_file']?>" class="u-photo photo-post" /><br>
        <?php echo $photo['body_html'];?>
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
        <div class="entry-meta">      
        <?php if($photo['replyto']) { ?>
            <div class="repyto">
               In Reply To <a class="u-url u-in-reply-to" rel="in-reply-to" href="<?php echo $photo['replyto']?>">This</a>
            </div>
        <?php }  // end if replyto?>
      <span class="sep">Posted on </span>
        <a href="<?php echo $photo['permalink']?>" title="<?php echo date("g:i A", strtotime($photo['timestamp']))?>" rel="bookmark" class="url u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($photo['timestamp']))?>" itemprop="dateModified"><?php echo date("F j, Y", strtotime($photo['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="http://schema.org/Person"><img alt='' src='http://0.gravatar.com/avatar/<?php echo md5($photo['author']['email_address'])?>?s=40&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D40&amp;r=G' class='u-photo avatar avatar-40 photo' height='40' width='40' /> <a class="url uid u-url u-uid fn p-name" href="<?php echo $photo['author']['link']?>" title="View all Images by <?php echo $photo['author']['display_name']?>" rel="author" itemprop="url"><span itemprop="name"><?php echo $photo['author']['display_name']?></span></a></span></address>
        </div><!-- .entry-meta -->
  
  <?php if($photo['categories']){ ?>
      <?php foreach($photo['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for photo_categories as category ?>
  <?php } // end if photo_categories ?>


    <?php if($photo['like_count'] > 0) { ?>
<br>
<span id="general-likes" class="widget widget_links"><a id="like"><h3 class="widget-title"><?php echo $photo['like_count'] . ($photo['like_count'] > 1 ? ' People' : ' Person')?> Liked This Photo</h3></a>
        <?php foreach($photo['likes'] as $like){?>
                <span class="likewrapper">
                <a href="<?php echo (isset($like['author_url']) ? $like['author_url']: $like['source_url'])?>" rel="nofollow">
                    <img class='like_author' src="<?php echo (isset($like['author_image']) ? $like['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($like['author_name']) ? $like['author_name']: 'Author Image') ?>" /></a>
                </span>
        <?php } ?>
    <div style="clear:both"></div>
	</span>
    <?php } ?>
  </footer><!-- #entry-meta --></article><!-- #photo-<?php echo $photo['photo_id']?> -->

    <?php if($photo['comment_count'] > 0) { ?>
<br>
<span id="general-comments" class="widget widget_links"><a id="comment"><h3 class="widget-title">Comments:</h3></a>
        <?php foreach($photo['comments'] as $comment){?>
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
