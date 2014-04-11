<?php echo $header; ?><?php echo $menu; ?>
<div id="content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_install) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_install; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="alert alert-info"><i class="fa fa-thumbs-o-up"></i> <?php echo $text_welcome; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <div class="row">
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-xs-3"><span class="text-muted"><i class="fa fa-shopping-cart fa-4x"></i></span></div>
            <div class="col-xs-9">
              <?php echo $text_new_order; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-xs-3"><span class="text-muted"><i class="fa fa-user fa-4x"></i></span></div>
            <div class="col-xs-9">
              <h3 class="text-success"><?php echo $customer_total; ?></h3>
              <?php echo $text_new_customer; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-xs-3"><span class="text-muted"><i class="fa fa-credit-card fa-4x"></i></span></div>
            <div class="col-xs-9">
              <span class="label <?php echo $class; ?> pull-right"><?php echo $sale_percentage; ?>%</span>
              <h3 class="text-success"><?php echo $sale_total; ?></h3>
              <?php echo $text_total_sale; ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-xs-3"><span class="text-muted"><i class="fa fa-eye fa-4x"></i></span></div>
            <div class="col-xs-9">
              <?php echo $text_online; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="pull-right">
            <div class="btn-group" data-toggle="buttons">
              <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-calendar"></i></button>
              <ul id="range" class="dropdown-menu dropdown-menu-right">
                <li><a href="day"><?php echo $text_day; ?></a></li>
                <li><a href="week"><?php echo $text_week; ?></a></li>
                <li class="active"><a href="month"><?php echo $text_month; ?></a></li>
                <li><a href="year"><?php echo $text_year; ?></a></li>
              </ul>
            </div>
          </div>
          <h1 class="panel-title"><i class="fa fa-bar-chart-o fa-lg"></i> <?php echo $text_analytics; ?></h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;"></div>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><i class="fa fa-eye fa-lg"></i> <?php echo $text_online; ?></h1>
        </div>
        <div class="panel-body">
          <div id="chart-online" class="chart" style="width: 100%; height: 175px;"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><i class="fa fa-calendar-o fa-lg"></i> <?php echo $text_activity; ?></h1>
        </div>
        <ul class="list-group">
          <?php if ($activities) { ?>
          <?php foreach ($activities as $activity) { ?>
          <li class="list-group-item"> <?php echo $activity['comment']; ?><br />
            <small class="text-muted"><i class="fa fa-clock-o"></i> <?php echo $activity['date_added']; ?></small></li>
          <?php } ?>
          <?php } else { ?>
          <li class="list-group-item text-center"><?php echo $text_no_results; ?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title"><i class="fa fa-shopping-cart fa-lg"></i> <?php echo $text_last_order; ?></h1>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <td class="text-right"><?php echo $column_order_id; ?></td>
                <td><?php echo $column_customer; ?></td>
                <td><?php echo $column_status; ?></td>
                <td><?php echo $column_date_added; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
                <td class="text-right"><?php echo $column_action; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) { ?>
              <?php foreach ($orders as $order) { ?>
              <tr>
                <td class="text-right"><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['customer']; ?></td>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo $order['date_added']; ?></td>
                <td class="text-right"><?php echo $order['total']; ?></td>
                <td class="text-right"><a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script> 
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script> 
<?php echo $footer; ?>
