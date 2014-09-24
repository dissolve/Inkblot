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
                  <input type="hidden" name="c" value="micropub/client/article" />
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
          <input type="hidden" name="c" value="micropub/client/article" />
        </form>
      <?php } ?>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-post" class="form-horizontal">
            <div class="content">
                <div class="form-group">
                  <div class="col-sm-10">
                    <!--<script>
                        function setDisplay(theClass,display){
                                var elements = document.getElementsByClassName(theClass), i;

                                for (var i = 0; i < elements.length; i ++) {
                                    elements[i].style.display = display;
                                    }

                            }
                    </script>
                    <style>
                        .group-edit{display:none}
                    </style>-->
                    <span> Type: 
                    <a href="<?php echo $note_create_link?>">Note</a>
                    <b>Article</b>
                    </span>
                    <br>
                    <input type="hidden" name="type" value="article" />
                    <!--<input type="radio" name="edit-type" value="" id="radio-create" checked class="form-control" onclick="setDisplay('group-edit','none');setDisplay('group-create','block');" /> Create
                    <input type="radio" name="edit-type" value="" id="radio-edit" class="form-control" onclick="setDisplay('group-create','none');setDisplay('group-edit','block');" /> Edit-->
                  </div>
                </div>
            <div class="content">

                <div class="form-group required group-create">
                  <label class="col-sm-2 control-label" for="input-title">Title</label>
                  <div class="col-sm-10">
                    <input type="text" name="title" value="<?php echo isset($post) ? $post['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title" class="form-control" />
                    <?php if (isset($error_title)) { ?>
                    <span class="text-danger"><?php echo $error_title; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group required group-create">
                  <label class="col-sm-2 control-label" for="input-slug">Slug</label>
                  <div class="col-sm-10">
                    <input type="text" name="slug" value="<?php echo isset($post) ? $post['slug'] : ''; ?>" placeholder="<?php echo $entry_slug; ?>" id="input-slug" class="form-control" />
                    <?php if (isset($error_slug)) { ?>
                    <span class="text-danger"><?php echo $error_slug; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group required group-create">
                  <label class="col-sm-2 control-label" for="input-body">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                    <?php if (isset($error_body)) { ?>
                    <span class="text-danger"><?php echo $error_body; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-replyto">Reply To</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['replyto'] : ''; ?>" placeholder="<?php echo $entry_replyto; ?>" id="input-replyto" class="form-control" />
                    <?php if (isset($error_replyto)) { ?>
                    <span class="text-danger"><?php echo $error_replyto; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-syndication">Syndication URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="syndication" value="" placeholder="Permalink to Sydicated Copy" id="input-syndication" class="form-control" />
                    <?php if (isset($error_syndication)) { ?>
                    <span class="text-danger"><?php echo $error_syndication; ?></span>
                    <?php } ?>
                  </div>
                </div>

                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-replyto">Syndicate To</label>
                  <div class="col-sm-10">
                    <select name="syndicate-to[]" id="sydicate_to_select" multiple="multiple">
                        <option value="https://www.brid.gy/publish/twitter">Brid.gy Twitter</option>
                        <option value="https://www.brid.gy/publish/facebook">Brid.gy Facebook</option>
                    </select>
                  </div>
                </div>

            </div>
            <?php if(isset($micropubEndpoint) && $token) { ?>
            <input type="submit" value="Submit" />
            <?php } ?>

      </form>
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
        <div class="entry-meta">      
        </div><!-- .entry-meta -->
  


  </footer><!-- #entry-meta --></article>

<script type="text/javascript" src="blog/view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript">
CKEDITOR.replace('input-body');
</script>
<?php echo $footer; ?>
