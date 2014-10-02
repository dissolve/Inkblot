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
                  <div class="col-sm-10">
                    <script>
                        function setDisplay(theClass,display){
                            var elements = document.getElementsByClassName(theClass), i;

                            for (var i = 0; i < elements.length; i ++) {
                                elements[i].style.display = display;
                                }
                        }
                        function enableGroup(groupName){
                            setDisplay('group-create','none');
                            setDisplay('group-edit','none');
                            setDisplay('group-delete','none');
                            setDisplay('group-undelete','none');
                            setDisplay(groupName,'block');
                        }

                    </script>
                    <style>
                        .group-edit{display:none}
                        .group-delete{display:none}
                        .group-undelete{display:none}
                        .group-create{display:block}
                    </style>
                      <ul class="mp-type-list">
                      <li><a class="mp-list-item" href="<?php echo $note_create_link?>">Note</a></li>
                      <li><a class="mp-list-item" href="<?php echo $article_create_link?>">Article</a></li>
                      <li><a class="mp-list-item" href="<?php echo $rsvp_create_link?>">RSVP</a></li>
                      <li><a class="mp-list-item" href="<?php echo $checkin_create_link?>">Checkin</a></li>
                      <li><a class="mp-list-item" href="<?php echo $like_create_link?>">Like</a></li>
                      <li class="mp-list-item mp-selected">Bookmark</li>
                    </ul>
                    <input type="hidden" name="type" value="note" />
                    <input type="radio" name="operation" value="create" id="radio-create" checked class="form-control" onclick="enableGroup('group-create');" /> Create
                    <!-- <input type="radio" name="operation" value="edit" id="radio-edit" class="form-control" onclick="enableGroup('group-edit');" /> Edit-->
                    <input type="radio" name="operation" value="delete" id="radio-delete" class="form-control" onclick="enableGroup('group-delete');" /> Delete
                  </div>
                </div>
            <div class="content">
                <div class="form-group group-edit group-delete" >
                  <label class="col-sm-2 control-label" for="input-url">URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="url" value="" placeholder="Permalink to Entry" id="input-url" class="form-control" />
                  </div>
                </div>


                <div class="form-group group-edit">
                  <label class="col-sm-2 control-label" for="input-syndication">Syndication URL to Add</label>
                  <div class="col-sm-10">
                    <input type="text" name="syndication" value="" placeholder="Permalink to Syndicated Copy" id="input-syndication" class="form-control" />
                  </div>
                </div>

                <div class="form-group required group-create">
                  <label class="col-sm-2 control-label" for="input-replyto">Bookmark URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="bookmark" value="<?php echo isset($post) ? $post['bookmark'] : ''; ?>" placeholder="URL to Bookmark" id="input-bookmark" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-title">Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($post) ? $post['name'] : ''; ?>" placeholder="Name of Bookmark" id="input-name" class="form-control" />
                  </div>
                </div>

                <div class="form-group  group-create">
                  <label class="col-sm-2 control-label" for="input-body">Description</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>


                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-category">Category</label>
                  <div class="col-sm-10">
                    <input type="text" name="category" value="<?php echo isset($post) ? $post['category'] : ''; ?>" placeholder="Category to file bookmark under" id="input-category" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-create">
                  <label class="col-sm-2 control-label" for="input-replyto">Syndicate To</label>
                  <div class="col-sm-10">
                    <select name="syndicate-to[]" id="syndicate_to_select" multiple="multiple">
                        <option value="https://www.brid.gy/publish/twitter">Brid.gy Twitter</option>
                        <option value="https://www.brid.gy/publish/facebook">Brid.gy Facebook</option>
                    </select>
                  </div>
                </div>

                <?php if(isset($micropubEndpoint) && $token) { ?>
                <div class="form-group group-create group-edit">
                  <div class="col-sm-12">
                    <input type="submit" value="Submit" class="form-control"/>
                  </div>
                </div>

                <div class="form-group group-delete">
                  <div class="col-sm-12">
                    <input type="submit" value="Delete" name="" class="form-control"/>
                  </div>
                </div>


                <?php } ?>
            </div>

      </form>
      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
        <div class="entry-meta">      
        </div><!-- .entry-meta -->
  


  </footer><!-- #entry-meta --></article>

<!-- <script type="text/javascript" src="blog/view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript">
CKEDITOR.replace('input-body');
</script>  -->
<?php echo $footer; ?>
