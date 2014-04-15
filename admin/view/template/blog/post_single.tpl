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
        <a href="<?php echo $post['permalink']; ?>" data-toggle="tooltip" title="Edit" class="btn"><i class="fa fa-link"></i></a>
        <a href="<?php echo $post['edit']; ?>" data-toggle="tooltip" title="Edit" class="btn"><i class="fa fa-pencil-square-o"></i></a>
        <a href="<?php echo $back; ?>" data-toggle="tooltip" title="Back" class="btn"><i class="fa fa-reply"></i></a></div>
      <h1 class="panel-title"><i class="fa fa-file-text fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
    <div class="panel-body">
            <div class="content">
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="title">Title</label>
                  <div class="col-sm-10" id="title">
                    <?php echo $post['title'] ?>
                  </div>
                </div>
                <div style="clear:both"></div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="slug">Slug</label>
                  <div class="col-sm-10" id="slug">
                    <?php echo $post['slug'] ?>
                  </div>
                </div>
                <div style="clear:both"></div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="body">Body</label>
                  <div class="col-sm-10" id="body">
                    <?php echo $post['body_html'] ?>
                  </div>
                </div>
                <div style="clear:both"></div>

                <div class="form-group">
                  <label class="col-sm-2 control-label" for="replyto">Reply To</label>
                  <div class="col-sm-10" id="replyto">
                    <?php echo $post['replyto'] ?>
                  </div>
                </div>

        </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
