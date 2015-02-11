<?php echo $header; ?>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

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
                  <input type="hidden" name="scope" value="post edit delete" />
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
          <input type="hidden" name="scope" value="post edit delete" />
        </form>
      <?php } ?>
      <form action="<?php echo ($token?$action:''); ?>" method="post" enctype="multipart/form-data" id="form-post" class="form-horizontal">
            <div class="content">
                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <div class="col-sm-10">
        <script>
            $(function(){ 
                
                var ed = null;
                function showNote(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-note').show();
                    $('.form-group.content').addClass('required');
                    $('.control-label[for="input-replyto"]').html('Reply To');
                    ed.destroy('input-body');
                }
                function showArticle(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-article').show();
                    $('.form-group.content').addClass('required');
                    $('.form-group.title').addClass('required');
                    ed = CKEDITOR.replace('input-body');
                }
                function showRsvp(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-rsvp').show();
                    $('.control-label[for="input-replyto"]').html('Event URL');
                    $('.form-group.rsvp').addClass('required');
                    $('.form-group.reply').addClass('required');
                }
                function showCheckin(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-checkin').show();
                }
                function showLike(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-like').show();
                    $('.form-group.like').addClass('required');
                }
                function showBookmark(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-bookmark').show();
                    $('.form-group.bookmark').addClass('required');
                }
                $('#radio-note').click(function(){showNote()});
                $('#radio-article').click(function(){showArticle()});
                $('#radio-rsvp').click(function(){showRsvp()});
                $('#radio-checkin').click(function(){showCheckin()});
                $('#radio-like').click(function(){showLike()});
                $('#radio-bookmark').click(function(){showBookmark()});
                showNote();
                
            });
        </script>

        <!--<script type="text/javascript" src="/view/javascript/ckeditor/ckeditor.js"></script> -->
                    <ul class="mp-type-list">
                      <li><a class="mp-list-item" href="<?php echo $new_entry_link?>">New</a></li>
                      <li class="mp-list-item mp-selected">Edit</li>
                      <li><a class="mp-list-item" href="<?php echo $delete_entry_link?>">Delete</a></li>
                      <li><a class="mp-list-item" href="<?php echo $undelete_entry_link?>">Undelete</a></li>
                    </ul>
		    <input type="hidden" name="action" value="edit" />

                <!--
                    <input type="radio" name="type" class="type-select" value="note"     id="radio-note"     <?php echo($type == 'note' ? '' :'checked')?> class="form-control" /><label class="type-select-label" for="radio-note">Note</label>
                    <input type="radio" name="type" class="type-select" value="article"  id="radio-article"  <?php echo($type == 'article'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-article">Article</label>
                    <input type="radio" name="type" class="type-select" value="rsvp"     id="radio-rsvp"     <?php echo($type == 'rsvp' ? 'checked': '')?> class="form-control" /><label class="type-select-label" for="radio-rsvp">RSVP</label>
                    <input type="radio" name="type" class="type-select" value="checkin"  id="radio-checkin"  <?php echo($type == 'checkin'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-checkin">Checkin</label>
                    <input type="radio" name="type" class="type-select" value="like"     id="radio-like"     <?php echo($type == 'like' ? 'checked': '')?> class="form-control" /><label class="type-select-label" for="radio-like">Like</label>
                    <input type="radio" name="type" class="type-select" value="bookmark" id="radio-bookmark" <?php echo($type =='bookmark'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-bookmark">Bookmark</label>
                    -->
                  </div>
                </div>
            <div class="content">
                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <label class="col-sm-2 control-label" for="input-url">URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="url" value="<?php echo isset($post) ? $post['permalink'] : ''; ?>" placeholder="Permalink to Entry" id="input-url" class="form-control" />
                  </div>
                </div>


                <?php if(isset($syn_arr)){ ?>
                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <label class="col-sm-2 control-label" for="input-syndicateto">Syndicate To</label>
                  <div class="col-sm-10">
                    <select name="syndicate-to[]" id="input-syndicateto" multiple="multiple">
                        <?php foreach($syn_arr as $syndication_target) { ?>
                        <option value="<?php echo $syndication_target?>"><?php echo $syndication_target?></option>
                        <?php } ?>
                    </select>
                  </div>
                </div>
                <?php } ?>


                <div class="form-group group-note group-article title">
                  <label class="col-sm-2 control-label" for="input-title">Title</label>
                  <div class="col-sm-10">
                    <input type="text" name="title" value="<?php echo isset($post) ? $post['title'] : ''; ?>" placeholder="Sample Title" id="input-title" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-note group-article">
                  <label class="col-sm-2 control-label" for="input-slug">Slug</label>
                  <div class="col-sm-10">
                    <input type="text" name="slug" value="<?php echo isset($post) ? $post['slug'] : ''; ?>" placeholder="sample_note_title" id="input-slug" class="form-control" />
                  </div>
                </div>

                <div class="form-group required group-note group-checkin group-article group-bookmark group-rsvp content">
                  <label class="col-sm-2 control-label" for="input-body">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>

                <div class="form-group group-note group-rsvp reply">
                  <label class="col-sm-2 control-label" for="input-replyto">Reply To</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['replyto'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-like like">
                  <label class="col-sm-2 control-label" for="input-like">Like Of</label>
                  <div class="col-sm-10">
                    <input type="text" name="like" value="<?php echo isset($post) ? $post['like'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-bookmark bookmark ">
                  <label class="col-sm-2 control-label" for="input-bookmark">Bookmark URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="bookmark" value="<?php echo isset($post) ? $post['bookmark'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-bookmark">
                  <label class="col-sm-2 control-label" for="input-title">Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($post) ? $post['name'] : ''; ?>" placeholder="Name of Bookmark" id="input-name" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-rsvp rsvp ">
                  <label class="col-sm-2 control-label" for="input-rsvp">RSVP:</label>
                  <div class="col-sm-10">
                    <select name="rsvp" id="input-rsvp" class="form-control">
                        <option value="yes">Attending</option>
                        <option value="no">Not Attending</option>
                    </select>
                  </div>
                </div>

                <div class="form-group group-note group-article group-bookmark">
                  <label class="col-sm-2 control-label" for="input-category">Category</label>
                  <div class="col-sm-10">
                    <input type="text" name="category" value="<?php echo isset($post) ? $post['category'] : ''; ?>" placeholder="Category1, Category2" id="input-category" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-note group-article">
                  <label class="col-sm-2 control-label" for="input-syndication">Syndication URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="syndication" value="" placeholder="Permalink to Syndicated Copy" id="input-syndication" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-note group-checkin">
                  <label class="col-sm-2 control-label" for="input-place_name">Place Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="place_name" value="<?php echo isset($post) ? $post['place_name'] : ''; ?>" placeholder="Name or Title of place" id="input-place_name" class="form-control" />
                  </div>
                </div>
                <div class="form-group group-note group-checkin">
                  <label class="col-sm-2 control-label" for="input-location">Location</label>
                  <div class="col-sm-10">
                    <input type="text" name="location" value="<?php echo isset($post) ? $post['location'] : ''; ?>" placeholder="<?php echo $entry_location; ?>" id="input-location" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <label class="col-sm-2 control-label" for="input-syndicateto">Syndicate To</label>
                  <div class="col-sm-10">
                    <select name="syndicate-to[]" id="input-syndicateto" multiple="multiple">
                        <option value="https://www.brid.gy/publish/twitter">Brid.gy Twitter</option>
                        <option value="https://www.brid.gy/publish/facebook">Brid.gy Facebook</option>
                    </select>
                  </div>
                </div>

                <div class="form-group group-article">
                  <div class="col-sm-10">
                    <input type="checkbox" name="draft" value="1" id="input-draft" class="form-control" />
                  <label class="col-sm-2 control-label" for="input-draft">This is a Draft, do not publish</label>
                  </div>
                </div>

                <?php if(isset($micropubEndpoint) && $token) { ?>
                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <div class="col-sm-12">
                    <input type="submit" value="Submit" class="form-control"/>
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

<?php echo $footer; ?>
