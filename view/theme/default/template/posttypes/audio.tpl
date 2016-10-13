<?php if(!empty($post['title'])){ ?>
<h1 class="entry-title p-name">
    <a href="<?php echo $post['permalink']?>" class="u-url" title="Permalink to <?php echo $post['title']?>" rel="bookmark" >
        <?php echo $post['title']?>
    </a>
</h1>
<?php } ?>
<div class="entry-content e-content">
    <?php foreach($post['audio'] as $audio){ ?>
        <audio controls>
            <source class="u-audio" src="<?php echo $audio['path']?>" type="audio/mp4">
            <a href="<?php echo $audio['path']?>" >Link</a>
        <audio>
    <?php } ?>
    <?php echo $post['body_html'];?>
    <?php echo $post['syndication_extra'];?>
</div><!-- .entry-content -->
