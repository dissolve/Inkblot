<?php echo $header; ?>
<div class="">

          <article id="note-<?php echo $note['note_id']?>" class="note-<?php echo $note['note_id']?> note type-note status-publish format-standard category-uncategorized">

    <header class="entry-meta comment_header">
        <div class="entry-meta">      

        </div><!-- .entry-meta -->
    </header>
  <div class='articlebody'>

    <?php if(!empty($note['title'])){ ?>
    <h1 class="entry-title p-name"><a href="<?php echo $note['permalink']?>" class="u-url url" title="Permalink to <?php echo $note['title']?>" rel="bookmark" ><?php echo $note['title']?></a></h1>
    <?php } ?>
      <div class="entry-content e-content p-note <?php echo (empty($note['title']) ? 'p-name' : '')?>">
       Sorry. This entry has been deleted.
      
      </div><!-- .entry-content -->
  </div>
  
  <footer class="entry-meta">



  </footer><!-- #entry-meta --></article><!-- #note-<?php echo $note['note_id']?> -->

</div>

<?php echo $footer; ?>
