<?php echo $header; ?>

          <article id="" class="article">

      <div class="entry-content e-content">
      <?php if(isset($user_name)) { ?><br>
      Logged in as <?php echo $user_name?><br>
          <?php if(isset($micropubEndpoint)) { ?>
              Found Micropub Endpoint at <?php echo $micropubEndpoint?><br>
              <?php if($token){ ?>
                Access Token Found 
              <?php } else { ?>
                You must log in with Post access to use this page
                <form action="<?php echo $login?>" method="get">
                  <label for="indie_auth_url">Web Address:</label>
                  <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
                  <p><button type="submit">Log In</button></p>
                  <input type="hidden" name="scope" value="post" />
                  <input type="hidden" name="c" value="micropub/client" />
                </form>
              <?php } ?>
          <?php } else { ?>
              No Micropub Endpoint Found! 
          <?php } ?>
      <?php } else { ?>
        You must log in with Post access to use this page
        <form action="<?php echo $login?>" method="get">
          <label for="indie_auth_url">Web Address:</label>
          <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
          <p><button type="submit">Log In</button></p>
          <input type="hidden" name="scope" value="post" />
          <input type="hidden" name="c" value="micropub/client" />
        </form>
      <?php } ?>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-post" class="form-horizontal">
            <div class="content">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-slug">Slug</label>
                  <div class="col-sm-10">
                    <input type="text" name="slug" value="<?php echo isset($post) ? $post['slug'] : ''; ?>" placeholder="<?php echo $entry_slug; ?>" id="input-slug" class="form-control" />
                    <?php if (isset($error_slug)) { ?>
                    <span class="text-danger"><?php echo $error_slug; ?></span>
                    <?php } ?>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-2 control-label" for="input-body">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                    <?php if (isset($error_body)) { ?>
                    <span class="text-danger"><?php echo $error_body; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-replyto">Reply To</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['replyto'] : ''; ?>" placeholder="<?php echo $entry_replyto; ?>" id="input-replyto" class="form-control" />
                    <?php if (isset($error_replyto)) { ?>
                    <span class="text-danger"><?php echo $error_replyto; ?></span>
                    <?php } ?>
                  </div>
                </div>

            </div>
            <input type="submit" value="Submit" />

      </form>
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
        <div class="entry-meta">      
        </div><!-- .entry-meta -->
  


  </footer><!-- #entry-meta --></article>

<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript">
CKEDITOR.replace('input-body');
</script> 
<?php echo $footer; ?>
