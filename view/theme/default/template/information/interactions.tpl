<?php echo $header; ?>

<article id="" class="h-feed">

        <?php foreach($recent_interactions as $interaction){ ?>
        <div class="h-entry">
            <span class="h-card">
            <img class="logo u-photo" src="<?php echo $interaction['author_image']?>" alt="<?php echo $interaction['author_name']?>" width="48" />
            <a class="u-url p-name" href="<?php echo $interaction['author_url']?>"><?php echo $interaction['author_name']?></a> 
            </span>

            <?php 
switch($interaction['interaction_type']){
case 'like':
    echo 'liked a';
    break;
case 'reply':
    echo 'commented on a';
    break;
}

?>

            <a href="<?php echo $interaction['post']['permalink']?>">
<?php echo $interaction['post']['post_type']?></a>
        </div>
            <?php } //end foreach whitelist as entry ?>



</article>

<?php echo $footer; ?>
