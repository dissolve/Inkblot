<?php echo $header; ?>

<div id="notes-stream">
<?php foreach($notes as $note) { ?>
          <article id="note-<?php echo $note['note_id']?>" class="note-<?php echo $note['note_id']?> note type-note status-publish format-standard category-uncategorized h-entry hentry h-as-article" itemprop="blogPost" itemscope="" itemtype="http://schema.org/BlogPosting">
  <header class="entry-header">
    <h1 class="entry-title p-name" itemprop="name headline"><a href="<?php echo $note['permalink']?>" class="u-url url" title="Permalink to <?php echo $note['title']?>" rel="bookmark" itemprop="url"><?php echo $note['title']?></a></h1>

        <div class="entry-meta">      
        <a href="<?php echo $note['permalink']?>" title="<?php echo date("g:i A", strtotime($note['timestamp']))?>" rel="bookmark" class="url u-url"> <time class="entry-date updated published dt-updated dt-published" datetime="<?php echo date("c", strtotime($note['timestamp']))?>" itemprop="dateModified"><?php echo date("F j, Y", strtotime($note['timestamp']))?></time> </a>
        <address class="byline"> <span class="sep"> by </span> <span class="author p-author vcard hcard h-card" itemprop="author" itemscope itemtype="http://schema.org/Person"><img alt='' src='http://0.gravatar.com/avatar/<?php echo md5($note['author']['email_address'])?>?s=40&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D40&amp;r=G' class='u-photo avatar avatar-40 photo' height='40' width='40' /> <a class="url uid u-url u-uid fn p-name" href="<?php echo $note['author']['link']?>" title="View all notes by <?php echo $note['author']['display_name']?>" rel="author" itemprop="url"><span itemprop="name"><?php echo $note['author']['display_name']?></span></a></span></address>
        </div><!-- .entry-meta -->
      </header><!-- .entry-header -->

      <div class="entry-content e-content" itemprop="description articleBody">
      <?php echo $note['body_html']?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
    <?php if($note['comment_count'] > 0) { ?>
    <span class="comments-link"><a href="<?php echo $note['permalink']?>#comments" title="Comments for <?php echo $note['title']?>"><i class="fa fa-comment-o"></i> <?php echo $note['comment_count'] ?></a></span>
    <span class="sep"> | </span>
    <?php } ?>

    <?php if($note['like_count'] > 0) { ?>
    <span class="likes-link"><a href="<?php echo $note['permalink']?>#likes" title="Likes of <?php echo $note['title']?>"><i class="fa fa-heart-o"></i> <?php echo $note['like_count']?></a></span>
    <span class="sep"> | </span>
    <?php } ?>
  
  <?php if($note['categories']){ ?>
      <?php foreach($note['categories'] as $category) { ?>
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for note_categories as category ?>
  <?php } // end if note_categories ?>
  </footer><!-- #entry-meta --></article><!-- #note-<?php echo $note['note_id']?> -->

<?php } //end foreach notes ?>
</div>

<div id="posts-stream">
<?php foreach($posts as $post) { ?>
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
      <?php echo $post['body_html']?>
      
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
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
          <span class="category-link"><a class="p-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>"><?php echo $category['name']?></a></span>
  
      <?php } // end for post_categories as category ?>
  <?php } // end if post_categories ?>
  </footer><!-- #entry-meta --></article><!-- #post-<?php echo $post['post_id']?> -->

<?php } //end foreach posts ?>
</div>
<?php echo $footer; ?>
