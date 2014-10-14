          <nav id="nav-below">
    <h1 class="assistive-text section-heading">Post navigation</h1>

  
  </nav><!-- #nav-below -->
  
      
      </main><!-- #content -->
    </section><!-- #primary -->

  <aside id="sidebar">
    <div id="secondary" class="widget-area" role="complementary">



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

</div><!-- #secondary .widget-area -->

</aside>

</div><!-- #main -->

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
