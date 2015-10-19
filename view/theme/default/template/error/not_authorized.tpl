<?php echo $header; ?>
<div id="content"><?php echo $content_top; ?>
  <h1>Access Denied!</h1>
  <div class="content"><?php echo $text_error; ?>
<div style="text-align:center">
    I'm sorry, the page you are trying to access is private or just not for you.
</div>

</div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>
