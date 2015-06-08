<?php echo $header; ?>
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
 <script src="/libraries/cassis/cassis.js"></script>

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
                  <input type="hidden" name="scope" value="post edit delete contacts follow" />
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
          <input type="hidden" name="scope" value="post edit delete contacts follow" />
        </form>
      <?php } ?>
      <form action="<?php echo ($token?$action:''); ?>" method="post" enctype="multipart/form-data" id="form-post" class="form-horizontal">
            <div class="content">
                <div class="form-group">
                  <div class="col-sm-10">

		    <input type="hidden" name="mp-action" value="create" />
		    <input type="hidden" name="h" value="card" />
                <div class="type-select-wrap">

                    <input type="radio" name="mp-action" class="type-select" value="follow"     id="radio-follow"     <?php echo($mp_action == 'follow' ? '' :'checked')?> class="form-control" /><label class="type-select-label" for="radio-follow">Follow</label>
                    <input type="radio" name="mp-action" class="type-select" value="unfollow"     id="radio-unfollow"     <?php echo($mp_action == 'unfollow' ? '' :'checked')?> class="form-control" /><label class="type-select-label" for="radio-unfollow">Unfollow</label>
                  </div>
              </div>
            </div>
            <div class="content">

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-url">Url</label>
                  <div class="col-sm-10">
                    <input type="text" name="url" value="<?php echo isset($card) ? $card['url'] : ''; ?>" placeholder="http://site.com/" id="input-url" class="form-control" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-name">Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo isset($card) ? $card['name'] : ''; ?>" placeholder="Name" id="input-name" class="form-control" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-photo">Photo Url</label>
                  <div class="col-sm-10">
                    <?php if(isset($card)) { echo '<img src="'.$card['photo'].'" style="width:30px" />'; } ?>
                    <input type="text" name="photo" value="<?php echo isset($card) ? $card['photo'] : ''; ?>" placeholder="http://site.com/photourl" id="input-photo" class="form-control" />
                  </div>
                </div>


                <?php if(isset($syn_arr)){ ?>
                <div class="form-group">
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
                <div class="form-group">
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
<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>

<?php echo $footer; ?>
