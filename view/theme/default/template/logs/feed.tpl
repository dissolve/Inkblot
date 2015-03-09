<!doctype html>
<html>
<head>
</head>
<body>
<div class="h-feed">
<?php foreach($feed as $entry){ ?>
    <div class="h-entry">
        <time class="dt-published" datetime="<?php echo $entry['published']?>"><?php echo $entry['published']?></time>
        <?php if(isset($entry['author_url'])){ ?>
            <span class="p-nickname p-name"><?php echo $entry['author_name']?></span>
        <?php } else { ?>
            <span class="p-nickname p-name"><?php echo $entry['author_name']?></span>
        <?php } ?>
        <span class="e-content p-name">
            <?php echo $entry['message']?>
        </span>
    <div>
<?php } ?>
</div>
</body></html>
