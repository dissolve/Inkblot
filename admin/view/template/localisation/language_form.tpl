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
        <button type="submit" form="form-language" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn"><i class="fa fa-check-circle"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a></div>
      <h1 class="panel-title"><i class="fa fa-pencil-square fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
          <div class="col-sm-10">
            <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
            <?php if ($error_name) { ?>
            <span class="text-danger"><?php echo $error_name; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-code"><?php echo $entry_code; ?></label>
          <div class="col-sm-10">
            <input type="text" name="code" value="<?php echo $code; ?>" placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control" />
            <?php if ($error_code) { ?>
            <span class="text-danger"><?php echo $error_code; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label required" for="input-locale"><?php echo $entry_locale; ?></label>
          <div class="col-sm-10">
            <input type="text" name="locale" value="<?php echo $locale; ?>" placeholder="<?php echo $entry_locale; ?>" id="input-locale" class="form-control" />
            <?php if ($error_locale) { ?>
            <span class="text-danger"><?php echo $error_locale; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
          <div class="col-sm-10">
            <input type="text" name="image" value="<?php echo $image; ?>" placeholder="<?php echo $entry_image; ?>" id="input-image" class="form-control" />
            <?php if ($error_image) { ?>
            <span class="text-danger"><?php echo $error_image; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-directory"><?php echo $entry_directory; ?></label>
          <div class="col-sm-10">
            <input type="text" name="directory" value="<?php echo $directory; ?>" placeholder="<?php echo $entry_directory; ?>" id="input-directory" class="form-control" />
            <?php if ($error_directory) { ?>
            <span class="text-danger"><?php echo $error_directory; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-2 control-label" for="input-filename"><?php echo $entry_filename; ?></label>
          <div class="col-sm-10">
            <input type="text" name="filename" value="<?php echo $filename; ?>" placeholder="<?php echo $entry_filename; ?>" id="input-filename" class="form-control" />
            <?php if ($error_filename) { ?>
            <span class="text-danger"><?php echo $error_filename; ?></span>
            <?php } ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
          <div class="col-sm-10">
            <select name="status" id="input-status" class="form-control">
              <?php if ($status) { ?>
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
            <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>