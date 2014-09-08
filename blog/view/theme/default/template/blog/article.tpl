<?php echo $header; ?>

          <article id="article-<?php echo $article['article_id']?>" class="article-<?php echo $article['article_id']?> article type-article status-publish format-standard category-uncategorized h-entry hentry h-as-article" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
  <header class="entry-header">
    <h1 class="entry-title p-name" itemprop="name headline"><a href="<?php echo $article['permalink']?>" class="u-url url" title="Permalink to <?php echo $article['title']?>" rel="bookmark" itemprop="url"><?php echo $article['title']?></a></h1>

        <div class="entry-meta">      
      <span class="sep">Posted on </span>
        <a href="<?php echo $article['permalink']?>" title="<?php echo date("g:i A", strtotime($article['timestamp']))?>" rel="bookmark" class="url u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($article['timestamp']))?>" itemprop="dateModified"><?php echo date("F j, Y", strtotime($article['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="http://schema.org/Person"><img alt='' src='http://0.gravatar.com/avatar/<?php echo md5($article['author']['email_address'])?>?s=40&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D40&amp;r=G' class='u-photo avatar avatar-40 photo' height='40' width='40' /> <a class="url uid u-url u-uid fn p-name" href="<?php echo $article['author']['link']?>" title="View all articles by <?php echo $article['author']['display_name']?>" rel="author" itemprop="url"><span itemprop="name"><?php echo $article['author']['display_name']?></span></a></span></address>
        <?php if($article['replyto']) { ?>
            <div class="repyto">
               In Reply To <a class="u-url u-in-reply-to" rel="in-reply-to" href="<?php echo $article['replyto']?>">This</a>
            </div>
        <?php }  // end if replyto?>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

      <div class="entry-content e-content" itemprop="description articleBody">
      <?php echo $article['body_html'];?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
    
  
  <?php if($article['categories']){ ?>
      <?php foreach($article['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for article_categories as category ?>
  <?php } // end if article_categories ?>


    <?php if($article['like_count'] > 0) { ?>
<br>
<span id="general-likes" class="widget widget_links"><a id="like"><h3 class="widget-title"><?php echo $article['like_count'] . ($article['like_count'] > 1 ? ' People' : ' Person')?> Liked This Post</h3></a>
        <?php foreach($article['likes'] as $like){?>
                <span class="likewrapper">
                <a href="<?php echo (isset($like['author_url']) ? $like['author_url']: $like['source_url'])?>" rel="nofollow">
                    <img class='like_author' src="<?php echo (isset($like['author_image']) ? $like['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($like['author_name']) ? $like['author_name']: 'Author Image') ?>" /></a>
                </span>
        <?php } ?>
    <div style="clear:both"></div>
	</span>
    <?php } ?>
  </footer><!-- #entry-meta --></article><!-- #article-<?php echo $article['article_id']?> -->

    <?php if($article['comment_count'] > 0) { ?>
<br>
<span id="comments" class="widget widget_links"><h3 class="widget-title">Comments:</h3>
        <?php foreach($article['comments'] as $comment){?>
                <div class="comment">
                <div class='comment_header'>
                <a href="<?php echo (isset($comment['author_url']) ? $comment['author_url']: $comment['source_url'])?>" rel="nofollow">
                    <img class='comment_author' src="<?php echo (isset($comment['author_image']) ? $comment['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($comment['author_name']) ? $comment['author_name']: 'Author Image') ?>" /></a>
                <a href="<?php echo $comment['source_url']?>" rel="nofollow"><i class="fa fa-link"></i></a>
                <?php echo (isset($comment['author_name']) ? $comment['author_name']: 'A Reader') ?> <!-- <?php echo $comment['source_name']?> -->
                </div>
                <div class='comment_body'>
                <?php echo $comment['body']?>
                </div>
                </div>
        <?php } ?>
	</span>
    <?php } ?>

<?php echo $footer; ?>
