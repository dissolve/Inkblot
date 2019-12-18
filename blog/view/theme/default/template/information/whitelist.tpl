<?php echo $header; ?>

          <article id="" class="article">

      <div class="entry-content e-content">
            <?php foreach($whitelist as $entry){ ?>
            <div class="h-card <?php echo ($entry['public'] ? '' : 'private') ?>">
                <a class="u-url" href="http://<?php echo $entry['domain']?>" rel="contact"><?php echo $entry['domain']?></a>
                <?php if($is_owner){ ?>
                    <a href="<?php echo $entry['delete']?>">Delete</a>

                    <?php if($entry['public']) { ?>
                        <a href="<?php echo $entry['make_private']?>">Make Private</a>
                    <?php } else { ?>
                        <a href="<?php echo $entry['make_public']?>">Make Public</a>
                    <?php }  ?>
                <?php } // end is_owner ?>
            </div>
            <?php } //end foreach whitelist as entry ?>

      </div><!-- .entry-content -->
  
  <footer class="entry-meta">
        <div class="entry-meta">      
        </div><!-- .entry-meta -->
  


  </footer><!-- #entry-meta --></article>

<?php echo $footer; ?>
