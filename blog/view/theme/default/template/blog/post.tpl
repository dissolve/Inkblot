<?php echo $header; ?>

          <article id="post-<?php echo $post['post_id']?>" class="post-<?php echo $post['post_id']?> post type-post status-publish format-standard category-uncategorized h-entry hentry h-as-article" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
  <header class="entry-header">
    <h1 class="entry-title p-name" itemprop="name headline"><a href="<?php echo $post['permalink']?>" class="u-url url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" itemprop="url"><?php echo $post['title']?></a></h1>

        <div class="entry-meta">      
      <span class="sep">Posted on </span>
        <a href="<?php echo $post['permalink']?>" title="<?php echo date("g:i A", strtotime($post['timestamp']))?>" rel="bookmark" class="url u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($post['timestamp']))?>" itemprop="dateModified"><?php echo date("F j, Y", strtotime($post['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="http://schema.org/Person"><img alt='' src='http://0.gravatar.com/avatar/<?php echo md5($post['author']['email_address'])?>?s=40&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D40&amp;r=G' class='u-photo avatar avatar-40 photo' height='40' width='40' /> <a class="url uid u-url u-uid fn p-name" href="<?php echo $post['author']['link']?>" title="View all posts by <?php echo $post['author']['display_name']?>" rel="author" itemprop="url"><span itemprop="name"><?php echo $post['author']['display_name']?></span></a></span></address>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

      <div class="entry-content e-content" itemprop="description articleBody">
      <p><?php echo str_replace("\n", "</p>\n", str_replace("^", "<p>", $post['body']));?></p>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
  Posted  
    <span class="sep"> | </span>
  
  <?php if($post['categories']){ ?>
      <?php foreach($post['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for post_categories as category ?>
  <?php } // end if post_categories ?>


    <?php if($post['like_count'] > 0) { ?>
<br>
<span id="general-likes" class="widget widget_links"><a id="like"><h3 class="widget-title"><?php echo $post['like_count'] . ($post['like_count'] > 1 ? ' People' : ' Person')?> Liked This Post</h3></a>
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
<br>
<span id="general-comments" class="widget widget_links"><a id="comment"><h3 class="widget-title">Comments:</h3></a>
        <?php foreach($post['comments'] as $comment){?>
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
