          <nav id="nav-below">
    <h1 class="assistive-text section-heading">Post navigation</h1>

  
  </nav><!-- #nav-below -->
  
      
      </main><!-- #content -->
    </section><!-- #primary -->

  <aside id="sidebar">
    <div id="secondary" class="widget-area" role="complementary">
<!-- <section id="search-2" class="widget widget_search"><form role="search" method="get" class="search-form" action="http://ben.thatmustbe.me/">
    <label>
        <span class="screen-reader-text">Search for:</span>
        <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="s" title="Search for:" />
    </label>
    <input type="submit" class="search-submit" value="Search" />
</form></section> -->
<section id="recent-posts-2" class="widget widget_recent_entries">
<h3 class="widget-title">Recent Posts</h3>
<ul>
<?php foreach($recent_posts as $post){ ?>
    <li>
        <a href="<?php echo $post['permalink']?>"><?php echo $post['title']?></a>
    </li>
<?php } // end foreach recent_post ?>
</ul>
</section>

<section id="recent-comments-2" class="widget widget_recent_comments">
<h3 class="widget-title">Recent Comments</h3>
<ul id="recentcomments">
</ul>
</section>
<section id="archives-2" class="widget widget_archive">
<h3 class="widget-title">Archives</h3>
<ul>
    <?php foreach($archives as $arch){ ?>
    <li><a href='<?php echo $arch['permalink']?>'><?php echo $arch['name'] ?></a></li>
    <?php } // end foreach archives ?>
</ul>
</section>

<section id="categories-2" class="widget widget_categories">
<h3 class="widget-title">Categories</h3>
<ul>
    <?php foreach($categories as $category){?>
        <li class="cat-item cat-item-1">
            <a href="<?php echo $category['permalink'];?>" title="View all posts filed under <?php echo $category['name'];?>"><?php echo $category['name'];?></a>
        </li>
    <?php } ?>
</ul>
</section>

<section id="linkcat-3" class="widget widget_links"><h3 class="widget-title">Elsewhere</h3>
	<ul>
        <?php foreach($melinks as $melink){?>
            <li><a href="<?php echo $melink['url'];?>" rel="me" title="<?php echo $melink['title'];?>" target="<?php echo $melink['target'];?>"><?php echo $melink['value'];?></a></li>
        <?php } ?>
	</ul>
</section>

<?php if($recent_mentions){ ?>
<section id="mentions" class="widget widget_links"><h3 class="widget-title">Recent Mentions</h3>
	<ul>
        <?php foreach($recent_mentions as $mention){?>
            <li><a href="<?php echo $mention['source_url'];?>" title="External Web Mention"><?php echo $mention['source_url'];?></a></li>
        <?php } ?>
	</ul>
</section>
<?php } //end if recent mentions ?>

<?php if($like_count > 0){ ?>
<section id="general-likes" class="widget widget_links"><h3 class="widget-title"><?php echo $like_count . ($like_count > 1 ? ' People' : ' Person')?> Liked This Site</h3>
	<ul>
        <?php foreach($likes as $like){?>
            <li>
                <a href="<?php echo (isset($like['author_url']) ? $like['author_url']: $like['source_url'])?>" rel="nofollow">
                    <img class='like_author' src="<?php echo (isset($like['author_image']) ? $like['author_image']: '/image/person.jpg') ?>"
                        title="<?php echo (isset($like['author_name']) ? $like['author_name']: 'Author Image') ?>" />
                </a>
            </li>
        <?php } ?>
	</ul>
    <div style="clear:both"></div>
</section>
<?php } //end if like_coutn > 0 ?>

</div><!-- #secondary .widget-area -->

</aside>

</div><!-- #main -->

<footer id="colophon" role="contentinfo">
  <div id="site-generator">
    This site is powered by <a href="https://github.com/dissolve/openblog">OpenBlog</a> based on <a href="http://opencart.com/">OpenCart</a>
  </div>
</footer><!-- #colophon -->

</div><!-- #page -->
</body>
</html>
