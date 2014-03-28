<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<script type="text/javascript" src="view/javascript/jquery/jquery-2.0.3.min.js"></script>
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<script src="view/javascript/jquery/datetimepicker/moment.min.js" type="text/javascript"></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
<?php foreach ($styles as $style) { ?>
<link type="text/css" href="<?php echo $style['href']; ?>" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<script src="view/javascript/common.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
</head>
<body>
<div id="container">
<header id="header" class="navbar navbar-static-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <?php if ($logged) { ?>
      <a type="button" id="button-menu" class="pull-left"><span class="fa fa-bars"></span></a>
      <?php } ?>
      <a href="<?php echo $home; ?>" class="navbar-brand"><img src="view/image/logo.png" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" /></a> </div>
    <?php if ($logged) { ?>
    <ul class="nav pull-right">
      <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><span class="label label-danger pull-left"><?php echo $alerts; ?></span> <i class="fa fa-bell fa-lg"></i></a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="dropdown-header"><?php echo $text_order; ?></li>
          <li><a href="<?php echo $order_status; ?>"><?php echo $text_order_status; ?> <span class="label label-warning pull-right"><?php echo $order_status_total; ?></span></a></li>
          <li><a href="<?php echo $complete_status; ?>"><?php echo $text_complete_status; ?> <span class="label label-success pull-right"><?php echo $complete_status_total; ?></span></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?> <span class="label label-danger pull-right"><?php echo $return_total; ?></span></a></li>
          <li class="divider"></li>
          <li class="dropdown-header"><?php echo $text_customer; ?></li>
          <li><a href="<?php echo $online; ?>"><?php echo $text_online; ?> <span class="label label-success pull-right"><?php echo $online_total; ?></span></a></li>
          <li><a href="<?php echo $customer_approval; ?>"><?php echo $text_approval; ?> <span class="label label-danger pull-right"><?php echo $customer_total; ?></span></a></li>
          <li class="divider"></li>
          <li class="dropdown-header"><?php echo $text_product; ?></li>
          <li><a href=""><?php echo $text_stock; ?> <span class="label label-danger pull-right"><?php echo $product_total; ?></a></li>
          <li><a href="<?php echo $review; ?>"><?php echo $text_review; ?> <span class="label label-danger pull-right"><?php echo $review_total; ?></span></a></li>
          <li class="divider"></li>
          <li class="dropdown-header"><?php echo $text_affiliate; ?></li>
          <li><a href="<?php echo $affiliate_approval; ?>"><?php echo $text_approval; ?> <span class="label label-danger pull-right"><?php echo $affiliate_total; ?></span></a></li>
        </ul>
      </li>
      <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog fa-lg"></i></a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="dropdown-header"><?php echo $text_store; ?> <i class="fa fa-shopping-cart"></i></li>
          <?php foreach ($stores as $store) { ?>
          <li><a href="<?php echo $store['href']; ?>" target="_blank"><?php echo $store['name']; ?></a></li>
          <?php } ?>
          <li class="divider"></li>
          <li class="dropdown-header"><?php echo $text_help; ?> <i class="fa fa-question-circle"></i></li>
          <li><a href="http://www.opencart.com" target="_blank"><?php echo $text_homepage; ?></a></li>
          <li><a href="http://docs.opencart.com" target="_blank"><?php echo $text_documentation; ?></a></li>
          <li><a href="http://forum.opencart.com" target="_blank"><?php echo $text_support; ?></a></li>
        </ul>
      </li>
      <li class="dropdown">
        <?php if ($image) { ?>
        <a class="dropdown-toggle" data-toggle="dropdown"><img src="<?php echo $image; ?>" alt="<?php echo $username; ?>" title="<?php echo $username; ?>" /></a>
        <?php } else { ?>
        <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-lg"></i></a>
        <?php } ?>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="dropdown-header"><?php echo $text_logged; ?></li>
          <li><a href="<?php echo $profile; ?>"><?php echo $text_profile; ?></a></li>
          <li><a href="<?php echo $setting; ?>"><?php echo $text_setting; ?></a></li>
          <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
        </ul>
      </li>
    </ul>
    <?php } ?>
  </div>
</header>
