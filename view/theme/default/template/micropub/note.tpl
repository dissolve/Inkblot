<?php echo $header; ?>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

          <article id="" class="article">

      <div class="entry-content e-content">
      <?php if(isset($user_name)) { ?><br>
      Logged in as <?php echo $user_name?><br>
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

		    <input type="hidden" name="mp-action" value="create" />
            <input type="hidden" name="mp-type" class="type-select" value="note" />
            <div class="content">

                <div class="form-group required group-note group-checkin group-article group-bookmark group-rsvp content">
                  <label class="col-sm-2 control-label" for="input-body">Body</label>
                  <div class="col-sm-10">
                    <textarea name="content" placeholder="Body of Post" id="input-body" class="form-control"><?php echo isset($post['body']) ? $post['body'] : ''; ?></textarea>
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
        <!--<script type="text/javascript" src="/view/javascript/ckeditor/ckeditor.js"></script> -->
<script src="//cdn.ckeditor.com/4.4.5/standard/ckeditor.js"></script>

<?php echo $footer; ?>
