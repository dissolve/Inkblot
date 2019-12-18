<?php echo $header; ?>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
 <script src="/libraries/cassis/cassis.js"></script>
<script type="text/javascript">
window.addEventListener('load', function() {

    if (!localStorage.posts) {
        localStorage.posts = JSON.stringify([]);
    }

    function updateOnlineStatus(event) {
        var condition = navigator.onLine ? "online" : "offline";
        if(navigator.onLine){
            $('body').addClass("online").removeClass("offline");
        } else {
            $('body').addClass("offline").removeClass("online");
        }
    }

    if(navigator.onLine){
        $('body').addClass("online").removeClass("offline");
        var posts = JSON.parse(localStorage.posts);
        if(posts.length > 0){
            $('#submitOfflined').show();
        }
    } else {
        $('body').addClass("offline").removeClass("online");
    }

    window.addEventListener('online',  updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    $('#saveOffline').click(function (event) {
	event.preventDefault();
        var formdata = $('#form-post').serialize() + '&published=' + get_formatted_date();
        var posts = JSON.parse(localStorage.posts);
        posts.push(formdata);
        localStorage.posts = JSON.stringify(posts);

        $('#form-post input[type=text], #form-post textarea').val("");
	alert('stored for later');
    });

    $('#submitOfflined').click(function (event ) {
	event.preventDefault();
        sendSaved();
    });

    function get_formatted_date(){
        var now = new Date();

        var formatted_out = now.getFullYear() + "-";

        // ugh zero indexed months
        if(now.getMonth() < 9){ formatted_out += "0"; }
        
        formatted_out += (now.getMonth()+1) + "-";

        if(now.getDate() < 10){ formatted_out += "0"; }
        formatted_out += now.getDate() + "T";

        if(now.getHours() < 10){ formatted_out += "0"; }
        formatted_out += now.getHours() + ":";
 
        if(now.getMinutes() < 10){ formatted_out += "0"; }
        formatted_out += now.getMinutes() + ":";
 
        if(now.getSeconds() < 10){ formatted_out += "0"; }
        formatted_out += now.getSeconds();

        tz_offset = now.getTimezoneOffset();

        if(tz_offset > 0){
            formatted_out += "-";
        } else {
            formatted_out += "+";
        }
        
        offset_hours = Math.abs(tz_offset) / 60;
        offset_mins = Math.abs(tz_offset) % 60;

        if(offset_hours < 10){ formatted_out += "0"; }
        formatted_out += offset_hours + ":";

        if(offset_mins < 10){ formatted_out += "0"; }
        formatted_out += offset_mins ;
 
        return formatted_out
    }

    function sendSaved(){

        var posts = JSON.parse(localStorage.posts);
        var formdata = posts.shift();
        localStorage.posts = JSON.stringify(posts);

        var url = '<?php echo ($token?$action:''); ?>';

        var formstring = '<form action="' + url + '" method="post">' ;
        formdata_array = formdata.split('&');
        for (var v = 0; v < formdata_array.length; v++){ 
            input_array = formdata_array[v].split('=');
            if(input_array[1]){
                formstring += '<input type="text" name="'+input_array[0]+'" value="' + input_array[1].replace(/\+/g,' ') + '" />' ;
            }
            
        }
        formstring += '</form>';

        var form = $(formstring);
        $('body').append(form);
        form.submit();
    }

    var posts = JSON.parse(localStorage.posts);
    if(posts.length > 0){
        $('#submitOfflined').show();
    }
    
});
</script>

          <article id="" class="article">

      <div class="entry-content e-content">
      <?php if(isset($user_name)) { ?><br>
      Logged in as <?php echo $user_name?><br>
        <div class="onlineOnly">
            <button style="display:none" id="submitOfflined">Submit Stored Post</button>
        </div>
          <?php if(isset($micropubEndpoint)) { ?>
              Found Micropub Endpoint at <?php echo $micropubEndpoint?><br>
                
              <?php if($token){ ?>
                Access Token Found <br>
                <br>
              <?php } else { ?>
                You must log in with Post access to use this page
                <form action="<?php echo $login?>" method="get">
                  <label for="indie_auth_url">Web Address:</label>
                  <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
                  <p><button type="submit">Log In</button></p>
                  <input type="hidden" name="scope" value="create edit delete" />
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
          <input type="hidden" name="scope" value="create edit delete" />
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
                    $('.control-label[for="input-in-reply-to"]').html('Reply To');
                    if(ed){
                        ed.destroy('input-body');
                    }
                }
                function showSnark(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-note').show();
                    $('.form-group.content').addClass('required');
                    $('.control-label[for="input-in-reply-to"]').html('Reply To');
                    if(ed){
                        ed.destroy('input-body');
                    }
                }
                function showArticle(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-article').show();
                    $('.form-group.content').addClass('required');
                    $('.form-group.title').addClass('required');
                    $('.control-label[for="input-in-reply-to"]').html('Reply To');
                    ed = CKEDITOR.replace('input-body');
                }
                function showRsvp(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-rsvp').show();
                    $('.control-label[for="input-in-reply-to"]').html('Event URL');
                    $('.form-group.rsvp').addClass('required');
                    $('.form-group.reply').addClass('required');
                    if(ed){
                        ed.destroy('input-body');
                    }
                }
                function showCheckin(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-checkin').show();
                    if(ed){
                        ed.destroy('input-body');
                    }
                }
                function showLike(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-like').show();
                    $('.form-group.like').addClass('required');
                    if(ed){
                        ed.destroy('input-body');
                    }
                }
                function showBookmark(){
                    $('.form-group').hide();
                    $('.required').removeClass('required');
                    $('.group-bookmark').show();
                    $('.form-group.bookmark').addClass('required');
                    if(ed){
                        ed.destroy('input-body');
                    }
                }


                $('#radio-note').click(function(){showNote()});
                $('#radio-snark').click(function(){showSnark()});
                $('#radio-article').click(function(){showArticle()});
                $('#radio-rsvp').click(function(){showRsvp()});
                $('#radio-checkin').click(function(){showCheckin()});
                $('#radio-like').click(function(){showLike()});
                $('#radio-bookmark').click(function(){showBookmark()});

                <?php if($type == 'article'){ ?>
                    showArticle();
                <?php } elseif($type == 'rsvp'){ ?>
                    showRsvp();
                <?php } elseif($type == 'checkin'){ ?>
                    showCheckin();
                <?php } elseif($type == 'like'){ ?>
                    showLike();
                <?php } elseif($type == 'bookmark'){ ?>
                    showBookmark();
                <?php } elseif($type == 'snark'){ ?>
                    showSnark();
                <?php } else { 
                    $type = 'note'; ?>
                    showNote();
                <?php } ?>

                $('#get-location-button').click(function(e){
                    e.preventDefault();
                    
                    function showPosition(position) {
                        $('input[name="location"]').val(
                        "geo:" + position.coords.latitude +
                        "," + position.coords.longitude);
                    }
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(showPosition);
                    } else {
                        alert("Geolocation is not supported by this browser.");
                    }
                    return false;
                });

                    $('#input-body').keyup(function(){
                        if(note_length_check(tw_text_proxy($('#input-body').val()), 140) == 413){
                            $('#input-body-ct').html('Too Long For Tweet');
                        } else {
                            $('#input-body-ct').html(' ');
                        }
                    });

            });
        </script>

                    <ul class="mp-type-list">
                      <li class="mp-list-item mp-selected">New</li>
                      <li><a class="mp-list-item" href="<?php echo $edit_entry_link?>">Edit</a></li>
                      <li><a class="mp-list-item" href="<?php echo $delete_entry_link?>">Delete</a></li>
                      <li><a class="mp-list-item" href="<?php echo $undelete_entry_link?>">Undelete</a></li>
                    </ul>
		    <input type="hidden" name="mp-action" value="create" />
                <div class="type-select-wrap">

                    <input type="radio" name="mp-type" class="type-select" value="note"     id="radio-note"     <?php echo($type == 'note' ?'checked' : '')?> class="form-control" /><label class="type-select-label" for="radio-note">Note</label>
                    <input type="radio" name="mp-type" class="type-select" value="snark"     id="radio-snark"   <?php echo($type == 'snark' ? 'checked': '')?> class="form-control" /><label class="type-select-label" for="radio-snark">Snark</label>
                    <input type="radio" name="mp-type" class="type-select" value="article"  id="radio-article"  <?php echo($type == 'article'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-article">Article</label>
                    <input type="radio" name="mp-type" class="type-select" value="rsvp"     id="radio-rsvp"     <?php echo($type == 'rsvp' ? 'checked': '')?> class="form-control" /><label class="type-select-label" for="radio-rsvp">RSVP</label>
                    <input type="radio" name="mp-type" class="type-select" value="checkin"  id="radio-checkin"  <?php echo($type == 'checkin'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-checkin">Checkin</label>
                    <input type="radio" name="mp-type" class="type-select" value="like"     id="radio-like"     <?php echo($type == 'like' ? 'checked': '')?> class="form-control" /><label class="type-select-label" for="radio-like">Like</label>
                    <input type="radio" name="mp-type" class="type-select" value="bookmark" id="radio-bookmark" <?php echo($type =='bookmark'?'checked':'')?> class="form-control" /><label class="type-select-label" for="radio-bookmark">Bookmark</label>
                  </div>
              </div>
            </div>
            <div class="content">

                <div class="form-group group-note group-article group-bookmark title">
                  <label class="col-sm-2 control-label" for="input-title">Title</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($post) ? $post['name'] : ''; ?>" placeholder="Sample Title" id="input-title" class="form-control" />
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
                    <span id="input-body-ct"></span>
                  </div>
                </div>

                <div class="form-group group-note group-article group-rsvp reply">
                  <label class="col-sm-2 control-label" for="input-in-reply-to">Reply To</label>
                  <div class="col-sm-10">
                    <input type="text" name="in-reply-to" value="<?php echo isset($post) ? $post['in-reply-to'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-in-reply-to" class="form-control" />
                  </div>
                </div>


                <div class="form-group group-like like">
                  <label class="col-sm-2 control-label" for="input-like">Like Of</label>
                  <div class="col-sm-10">
                    <input type="text" name="like-of" value="<?php echo isset($post) ? $post['like-of'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-in-reply-to" class="form-control" />
                  </div>
                </div>

                <div class="form-group group-bookmark bookmark ">
                  <label class="col-sm-2 control-label" for="input-bookmark">Bookmark URL</label>
                  <div class="col-sm-10">
                    <input type="text" name="bookmark-of" value="<?php echo isset($post) ? $post['bookmark-of'] : ''; ?>" placeholder="http://somesite.com/posts/123" id="input-in-reply-to" class="form-control" />
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
                    <button id="get-location-button">Get Location</button>
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

                <div class="form-group group-article">
                  <div class="col-sm-10">
                    <input type="checkbox" name="draft" value="1" id="input-draft" class="form-control" />
                  <label class="col-sm-2 control-label" for="input-draft">This is a Draft, do not publish</label>
                  </div>
                </div>

                <?php if(isset($micropubEndpoint) && $token) { ?>
                <div class="form-group group-note group-article group-rsvp group-checkin group-like group-bookmark">
                  <div class="col-sm-12">
                    <input type="submit" value="Submit" class="onlineOnly form-control"/>
                    <button id="saveOffline" class="offlineOnly form-control">Save</button>
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
        <!--<script type="text/javascript" src="/view/javascript/ckeditor/ckeditor.js"></script> -->
<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>

<?php echo $footer; ?>
