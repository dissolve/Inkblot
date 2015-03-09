<!doctype html>
<html>
<head>
</head>
<body>
<?php foreach($feedlist as $feed){ ?>
    <a class="h-feed u-url" href="<?php echo $logger_endpoint?>?h=feed&url=<?php echo $feed['feed_url']?>"><?php echo $feed['feed_url']?></a><br>
<?php } ?>
</body></html>
