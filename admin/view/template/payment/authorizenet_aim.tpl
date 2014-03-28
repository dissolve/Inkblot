<?php echo $header; ?><?php echo $menu; ?>
<div id="content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="pull-right">
        <button type="submit" form="form-authorizenet-aim" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn"><i class="fa fa-check-circle"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a></div>
      <h1 class="panel-title"><i class="fa fa-credit-card fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-authorizenet-aim" class="form-horizontal">
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-login"><?php echo $entry_login; ?></label>
          <div class="col-sm-10">
            <input type="text" name="authorizenet_aim_login" value="<?php echo $authorizenet_aim_login; ?>" placeholder="<?php echo $entry_login; ?>" id="input-login" class="form-control" />
            <?php if ($error_login) { ?>
            <span class="text-danger"><?php echo $error_login; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-key"><?php echo $entry_key; ?></label>
          <div class="col-sm-10">
            <input type="text" name="authorizenet_aim_key" value="<?php echo $authorizenet_aim_key; ?>" placeholder="<?php echo $entry_key; ?>" id="input-key" class="form-control" />
            <?php if ($error_key) { ?>
            <span class="text-danger"><?php echo $error_key; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-hash"><?php echo $entry_hash; ?></label>
          <div class="col-sm-10">
            <input type="text" name="authorizenet_aim_hash" value="<?php echo $authorizenet_aim_hash; ?>" placeholder="<?php echo $entry_hash; ?>" id="input-hash" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-server"><?php echo $entry_server; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_server" id="input-server" class="form-control">
              <?php if ($authorizenet_aim_server == 'live') { ?>
              <option value="live" selected="selected"><?php echo $text_live; ?></option>
              <?php } else { ?>
              <option value="live"><?php echo $text_live; ?></option>
              <?php } ?>
              <?php if ($authorizenet_aim_server == 'test') { ?>
              <option value="test" selected="selected"><?php echo $text_test; ?></option>
              <?php } else { ?>
              <option value="test"><?php echo $text_test; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_mode; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_mode" id="input-mode" class="form-control">
              <?php if ($authorizenet_aim_mode == 'live') { ?>
              <option value="live" selected="selected"><?php echo $text_live; ?></option>
              <?php } else { ?>
              <option value="live"><?php echo $text_live; ?></option>
              <?php } ?>
              <?php if ($authorizenet_aim_mode == 'test') { ?>
              <option value="test" selected="selected"><?php echo $text_test; ?></option>
              <?php } else { ?>
              <option value="test"><?php echo $text_test; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-method"><?php echo $entry_method; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_method" id="input-method" class="form-control">
              <?php if ($authorizenet_aim_method == 'authorization') { ?>
              <option value="authorization" selected="selected"><?php echo $text_authorization; ?></option>
              <?php } else { ?>
              <option value="authorization"><?php echo $text_authorization; ?></option>
              <?php } ?>
              <?php if ($authorizenet_aim_method == 'capture') { ?>
              <option value="capture" selected="selected"><?php echo $text_capture; ?></option>
              <?php } else { ?>
              <option value="capture"><?php echo $text_capture; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_total; ?></label>
          <div class="col-sm-10">
            <input type="text" name="authorizenet_aim_total" value="<?php echo $authorizenet_aim_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
            <span class="help-block"><?php echo $help_total; ?></span> </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_order_status_id" id="input-order-status" class="form-control">
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $authorizenet_aim_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_geo_zone_id" id="input-geo-zone" class="form-control">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $authorizenet_aim_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
          <div class="col-sm-10">
            <select name="authorizenet_aim_status" id="input-status" class="form-control">
              <?php if ($authorizenet_aim_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
          <div class="col-sm-10">
            <input type="text" name="authorizenet_aim_sort_order" value="<?php echo $authorizenet_aim_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 