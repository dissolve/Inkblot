<!doctype html>
<html>
<head>
</head>
<body>
<table>
<tr>
<th>Retry</th>
<th>Dismiss</th>
<th>Approve</th>
<th>Code</th>
<th>Status</th>
<th>Source</th>
</tr>
<?php foreach($list as $entry){ ?>
<tr>
<td><a href="<?php echo $entry['action_retry']?>">Retry</a></td>
<td><a href="<?php echo $entry['action_dismiss']?>">Dismiss</a></td>
<td><a href="<?php echo $entry['action_approve']?>">Approve</a></td>
<td><?php echo $entry['status_code']?></td>
<td><?php echo $entry['status']?></td>
<td><a href="<?php echo $entry['source_url']?>" target="_blank"><?php echo parse_url($entry['source_url'], PHP_URL_HOST)?></a></td>
</tr>
<?php } ?>
        
</body></html>
