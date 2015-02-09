          <nav id="nav-below">
    <h1 class="assistive-text section-heading">Post navigation</h1>

  
  </nav><!-- #nav-below -->
  
      
      </main><!-- #content -->
    </section><!-- #primary -->

  <aside id="sidebar">
    <div id="secondary" class="widget-area" role="complementary">
<section id="search-2" class="widget widget_search">
  <form role="search" method="get" class="search-form" action="https://www.google.com/search">
    <label>
        <span class="screen-reader-text">Search for:</span>
        <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="q" title="Search for:" />
        <input type="hidden" name='as_sitesearch' value='<?php echo $sitesearch ?>' />
        <input type="hidden" name="tbs" value="sbd:1,cdr:1,cd_min:1/1/1970"/>
    </label>
    <input type="submit" class="search-submit" value="Search" />
  </form>
</section>


<?php if($recent_drafts) { ?>
    <section id="recent-drafts-2" class="widget widget_recent_entries">
    <h3 class="widget-title">Recent Drafts</h3>
    <ul>
    <?php foreach($recent_drafts as $post){ ?>
        <li>
            <a href="<?php echo $post['permalink']?>"><?php echo $post['title']?></a>
        </li>
    <?php } // end foreach recent_drafts?>
    </ul>
    </section>
<?php } ?>

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
        <?php foreach($mylinks as $mylink){?>
            <li><a href="<?php echo $mylink['url'];?>" rel="<?php echo $mylink['rel']?>" title="<?php echo $mylink['title'];?>" target="<?php echo $mylink['target'];?>"><?php echo $mylink['value'];?></a></li>
        <?php } ?>
	</ul>
</section>

<?php if(!empty($recent_mentions)){ ?>
<section id="mentions" class="widget widget_links"><h3 class="widget-title">Recent Mentions</h3>
	<ul>
        <?php foreach($recent_mentions as $mention){?>
            <li><a href="<?php echo $mention['source_url'];?>" title="External Web Mention"><?php echo $mention['source_url'];?></a></li>
        <?php } ?>
	</ul>
</section>
<?php } //end if recent mentions ?>

<section id="login" class="widget">
    <?php if(isset($user_name)) { ?>
    <h3 class="widget-title">Logged In As "<?php echo $user_name?>"</h3>
    <ul><li>
        <a href="<?php echo $logout?>">Log Out</a>
    </li></ul>
    <?php } else { ?>
    <h3 class="widget-title">Log In with IndieAuth</h3>
    <ul><li>
        <form action="<?php echo $login?>" method="get">
          <label for="indie_auth_url">Web Address:</label>
          <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
          <p><button type="submit">Log In</button></p>
        </form>
    </li></ul>
    <?php } ?>
</section>
        <?php if(isset($webaction)){ ?>
        <section class="widget">
            <button onclick=" window.navigator.registerProtocolHandler('web+action', '<?php echo $webaction;?>' , 'Postly');" value="" >Register Your Handler</button>
        </section>
        <?php } ?>

</div><!-- #secondary .widget-area -->

</aside>

</div><!-- #main -->

<footer id="colophon" role="contentinfo">
  <div id="site-generator">
    This site is powered by <a href="https://github.com/dissolve/postly">Postly</a>
  </div>
</footer><!-- #colophon -->

</div><!-- #page -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_analytics_id?>', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
</body>
</html>
