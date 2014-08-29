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
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="pull-right"><a href="<?php echo $insert; ?>" data-toggle="tooltip" title="Insert" class="btn"><i class="fa fa-plus-circle"></i></a>
        <button type="button" class="btn" data-toggle="tooltip" title="Delete" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-times-circle"></i></button>
      </div>
      <h1 class="panel-title"><i class="fa fa-bars fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                <td class="text-left"><?php if ($sort == 'title') { ?>
                  <a href="<?php echo $sort_title; ?>" class="<?php echo strtolower($order); ?>">Title</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_title; ?>">Title</a>
                  <?php } ?></td>
                <td class="text-left"><?php if ($sort == 'timestamp') { ?>
                  <a href="<?php echo $sort_timestamp; ?>" class="<?php echo strtolower($order); ?>">Timestamp</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_timestamp; ?>">Timestamp</a>
                  <?php } ?></td>
                <td class="text-right"><?php echo $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($contacts) { ?>
              <?php foreach ($contacts as $contact) { ?>
              <tr>
                <td class="text-center"><?php if (in_array($contacts['contact_id'], $selected)) { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $contact['contact_id']; ?>" checked="checked" />
                  <?php } else { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $contact['contact_id']; ?>" />
                  <?php } ?></td>
                <td class="text-left"><?php echo $contact['main_url']; ?></td>
                <td class="text-right">
                <a href="<?php echo $contact['view']; ?>" data-toggle="tooltip" title="View" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                <a href="<?php echo $contact['edit']; ?>" data-toggle="tooltip" title="Edit" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                </td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </form>
      <div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
