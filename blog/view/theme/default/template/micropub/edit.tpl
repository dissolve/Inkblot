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
                setDisplay('edit-note','none');
                setDisplay('edit-article','none');
                setDisplay('edit-bookmark','none');
                setDisplay('edit-like','none');
                setDisplay('edit-checkin','none');
                setDisplay('edit-rsvp','none');
                setDisplay(groupName,'block');
            }
        </script>
        <style>
            .edit-note{display:none}
            .edit-article{display:none}
            .edit-bookmark{display:none}
            .edit-like{display:none}
            .edit-checkin{display:none}
            .edit-rsvp{display:none}
			<?php if(isset($type) && $type=="article") { ?>
				.edit-article{display:block}
			<?php } elseif(isset($type) && $type=="bookmark") { ?>
				.edit-bookmark{display:block}
			<?php } elseif(isset($type) && $type=="like") { ?>
				.edit-like{display:block}
			<?php } elseif(isset($type) && $type=="checkin") { ?>
				.edit-checkin{display:block}
			<?php } elseif(isset($type) && $type=="rsvp") { ?>
				.edit-rsvp{display:block}
			<?php } else { ?>
				.edit-note{display:block}
			<?php } ?>
        </style>

        <script type="text/javascript" src="/blog/view/javascript/ckeditor/ckeditor.js"></script> 
                    <ul class="mp-type-list">
                      <li><a class="mp-list-item" href="<?php echo $new_entry_link?>">New</a></li>
                      <li class="mp-list-item mp-selected">Edit</li>
                      <li><a class="mp-list-item" href="<?php echo $delete_entry_link?>">Delete</a></li>
                    </ul>
		    <input type="hidden" name="operation" value="edit" />


                    <!--<input type="radio" name="type" class="type-select" value="note" id="radio-note" <?php echo($type == 'note' ? '' :'checked')?> class="form-control" onclick="enableGroup('new-note');" /><label class="type-select-label" for="radio-note">Note</label>
                    <input type="radio" name="type" class="type-select" value="article" id="radio-article" <?php echo($type == 'article' ? 'checked':'')?> class="form-control" onclick="enableGroup('new-article');" /><label class="type-select-label" for="radio-article">Article</label>
                    <input type="radio" name="type" class="type-select" value="rsvp" id="radio-rsvp" <?php echo($type == 'rsvp' ? 'checked': '')?> class="form-control" onclick="enableGroup('new-rsvp');" /><label class="type-select-label" for="radio-rsvp">RSVP</label>
                    <input type="radio" name="type" class="type-select" value="checkin" id="radio-checkin" <?php echo($type == 'checkin' ? 'checked': '')?> class="form-control" onclick="enableGroup('new-checkin');" /><label class="type-select-label" for="radio-checkin">Checkin</label>
                    <input type="radio" name="type" class="type-select" value="like" id="radio-like" <?php echo($type == 'like' ? 'checked': '')?> class="form-control" onclick="enableGroup('new-like');" /><label class="type-select-label" for="radio-like">Like</label>
                    <input type="radio" name="type" class="type-select" value="bookmark" id="radio-bookmark" <?php echo($type == 'bookmark' ? 'checked': '')?> class="form-control" onclick="enableGroup('new-bookmark');" /><label class="type-select-label" for="radio-bookmark">Bookmark</label>-->
                  </div>
                </div>
            <div class="content">
                <div class="form-group edit-note edit-article edit-rsvp edit-checkin edit-like edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-url">URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="url" value="<?php echo isset($post) ? $post['permalink'] : ''; ?>" placeholder="Permalink to Entry" id="input-url" class="form-control" />
                  </div>
                </div>


                <div class="form-group edit-note edit-article edit-rsvp edit-checkin edit-like edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-syndication">Syndication URL to Add</label>
                  <div class="col-sm-10">
                    <input type="text" name="syndication" value="" placeholder="Permalink to Syndicated Copy" id="input-syndication" class="form-control" />
                  </div>
                </div>


                <div class="form-group edit-note">
                  <label class="col-sm-2 control-label" for="input-title">Title</label>
                  <div class="col-sm-10">
                    <input type="text" name="title" value="<?php echo isset($post) ? $post['title'] : ''; ?>" placeholder="Sample Title" id="input-title" class="form-control" />
                  </div>
                </div>
                <div class="form-group edit-article required">
                  <label class="col-sm-2 control-label" for="input-title">Title</label>
                  <div class="col-sm-10">
                    <input type="text" name="title" value="<?php echo isset($post) ? $post['title'] : ''; ?>" placeholder="Sample Title" id="input-title" class="form-control" />
                  </div>
                </div>

                <div class="form-group required edit-note">
                  <label class="col-sm-2 control-label" for="input-body">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>
                <div class="form-group required edit-article">
                  <label class="col-sm-2 control-label" for="input-body2">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body2" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>
                <div class="form-group edit-rsvp edit-checkin">
                  <label class="col-sm-2 control-label" for="input-body3">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body3" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>

                <div class="form-group edit-note">
                  <label class="col-sm-2 control-label" for="input-replyto">Reply To</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['replyto'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>
                <div class="form-group edit-rsvp">
                  <label class="col-sm-2 control-label" for="input-replyto">Event URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['replyto'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-like required">
                  <label class="col-sm-2 control-label" for="input-replyto">Like Of</label>
                  <div class="col-sm-10">
                    <input type="text" name="like" value="<?php echo isset($post) ? $post['like'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-bookmark required">
                  <label class="col-sm-2 control-label" for="input-replyto">Bookmark URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="bookmark" value="<?php echo isset($post) ? $post['bookmark'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-replyto" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-title">Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($post) ? $post['name'] : ''; ?>" placeholder="Name of Bookmark" id="input-name" class="form-control" />
                  </div>
                </div>

                <div class="form-group  edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-body">Description</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
                  </div>
                </div>

                <div class="form-group edit-rsvp required">
                  <label class="col-sm-2 control-label" for="input-rsvp">RSVP:</label>
                  <div class="col-sm-10">
                    <select name="rsvp" id="input-rsvp" class="form-control">
                        <option value="yes">Attending</option>
                        <option value="no">Not Attending</option>
                    </select>
                  </div>
                </div>

                <div class="form-group edit-note edit-article edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-category">Category</label>
                  <div class="col-sm-10">
                    <input type="text" name="category" value="<?php echo isset($post) ? $post['category'] : ''; ?>" placeholder="Category1, Category2" id="input-category" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-note edit-article">
                  <label class="col-sm-2 control-label" for="input-syndication">Syndication URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="syndication" value="" placeholder="Permalink to Syndicated Copy" id="input-syndication" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-note edit-checkin">
                  <label class="col-sm-2 control-label" for="input-place_name">Place Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="place_name" value="<?php echo isset($post) ? $post['place_name'] : ''; ?>" placeholder="Name or Title of place" id="input-place_name" class="form-control" />
                  </div>
                </div>
                <div class="form-group edit-note edit-checkin">
                  <label class="col-sm-2 control-label" for="input-location">Location</label>
                  <div class="col-sm-10">
                    <input type="text" name="location" value="<?php echo isset($post) ? $post['location'] : ''; ?>" placeholder="<?php echo $entry_location; ?>" id="input-location" class="form-control" />
                  </div>
                </div>

                <div class="form-group edit-note edit-article edit-rsvp edit-checkin edit-like edit-bookmark">
                  <label class="col-sm-2 control-label" for="input-replyto">Syndicate To</label>
                  <div class="col-sm-10">
                    <select name="syndicate-to[]" id="syndicate_to_select" multiple="multiple">
                        <option value="https://www.brid.gy/publish/twitter">Brid.gy Twitter</option>
                        <option value="https://www.brid.gy/publish/facebook">Brid.gy Facebook</option>
                    </select>
                  </div>
                </div>

                <div class="form-group edit-article">
                  <div class="col-sm-10">
                    <input type="checkbox" name="draft" value="1" id="input-draft" class="form-control" />
                  <label class="col-sm-2 control-label" for="input-replyto">This is a Draft, do not publish</label>
                  </div>
                </div>

                <?php if(isset($micropubEndpoint) && $token) { ?>
                <div class="form-group group-create group-edit">
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

<!--<script type="text/javascript">
CKEDITOR.replace('input-body2');
</script>-->
<?php echo $footer; ?>
