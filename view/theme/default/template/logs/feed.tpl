<!doctype html>
<html>
<head>
</head>
<body>
<div class="h-feed">
<?php foreach($feed as $entry){ ?>
    <div class="h-entry">
        <time class="dt-published" datetime="<?php echo $entry['published']?>"><?php echo $entry['published']?></time>
        <span class="p-author h-card">
        <?php if(isset($entry['author']['url'])){ ?>
            <span class="p-nickname p-name"><?php echo $entry['author']['name']?></span>
        <?php } else { ?>
            <span class="p-nickname p-name"><?php echo $entry['author']['name']?></span>
        <?php } ?>
        </span>
        <span class="e-content p-name">
            <?php echo $entry['message']?>
        </span>
    </div>
<?php } ?>
    <a href="<?php echo $prev?>" class="u-prev">Prev</a>
</div>
</body></html>
