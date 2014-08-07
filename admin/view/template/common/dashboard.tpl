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
              <a href="<?php echo $new_post?>"><h3>New Article</h3></a>
            </div>
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
              <a href="<?php echo $new_note?>" ><h3>New Note</h3></a>
              </div>
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
              <a href="<?php echo $new_post?>" ><h3>New Article</h3></a>
              </div>
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
              <a href="<?php echo $new_note?>" ><h3>New Note</h3></a>
              </div>
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
          <h1 class="panel-title"><i class="fa fa-bar-chart-o fa-lg"></i> Articles</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table>
          <tr>
            <th>Title</th>
            <th>Comments</th>
            <th>Likes</th>
            <th>Mentions</th>
          </tr>
          <?php foreach($posts as $post){?>
            <tr>
            <td><a href="<?php echo $post['view']?>"><?php echo $post['title']?></a></td>
            <td><?php echo $post['comment_count']?></td>
            <td><?php echo $post['like_count']?></td>
            <td><?php echo $post['mention_count']?></td>
            </tr>
          <?php } ?>
          </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
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
          <h1 class="panel-title"><i class="fa fa-bar-chart-o fa-lg"></i> Notes</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table>
          <tr>
            <th>Title</th>
            <th>Comments</th>
            <th>Likes</th>
            <th>Mentions</th>
          </tr>
          <?php foreach($notes as $note){?>
            <tr>
            <td><a href="<?php echo $note['view']?>"><?php echo $note['title']?></a></td>
            <td><?php echo $note['comment_count']?></td>
            <td><?php echo $note['like_count']?></td>
            <td><?php echo $note['mention_count']?></td>
            </tr>
          <?php } ?>
          </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
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
          <h1 class="panel-title"><i class="fa fa-bar-chart-o fa-lg"></i> Photos</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table>
          <tr>
            <th>Title</th>
            <th>Comments</th>
            <th>Likes</th>
            <th>Mentions</th>
          </tr>
          <?php foreach($photos as $photo){?>
            <tr>
            <td><a href="<?php echo $photo['view']?>"><?php echo $photo['title']?></a></td>
            <td><?php echo $photo['comment_count']?></td>
            <td><?php echo $photo['like_count']?></td>
            <td><?php echo $photo['mention_count']?></td>
            </tr>
          <?php } ?>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div><!-- row -->
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script> 
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script> 
<?php echo $footer; ?>
