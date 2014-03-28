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
  <!-- <span class="comments-link"><a href="<?php echo $post['commentlink']?>" title="Comment on <?php echo $post['title']?>">Leave a comment</a></span> -->
  
  <?php if($post['categories']){ ?>
      <?php foreach($post['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for post_categories as category ?>
  <?php } // end if post_categories ?>
  </footer><!-- #entry-meta --></article><!-- #post-<?php echo $post['post_id']?> -->


<?php echo $footer; ?>
