

<div class="main">

<section id="login" class="widget">
    <?php if(isset($user_name)) { ?>
    <h3 class="widget-title">Signed In As "<?php echo $user_name?>"</h3>
    <ul><li>
        <a href="<?php echo $logout?>">Sign Out</a>
    </li></ul>
    <?php } else { ?>
    <h3 class="widget-title">Sign In with IndieAuth</h3>
    <ul><li>
        <form action="<?php echo $auth_endpoint?>" method="get">
          <label for="indie_auth_url">Web Address:</label>
          <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
          <p><button type="submit">Sign In</button></p>
          <input type="hidden" name="client_id" value="<?php echo $client_id?>" />
          <input type="hidden" name="redirect_uri" value="<?php echo $auth_page?>" />
        </form>
    </li></ul>
    <?php } ?>
</section>
</div>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_analytics_id?>', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
        <script> 
addToHomescreen({
        detectHomescreen: true;
});

</script>
</body>
</html>
