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
  <!-- <div class="alert alert-info"><i class="fa fa-thumbs-o-up"></i> <?php echo $text_welcome; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div> -->
  <div class="row">
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="pull-right">
            <div class="btn-group" data-toggle="buttons">
              <a class="btn" href="<?php echo $new_post?>"><i class="fa fa-plus"></i></a>
            </div>
          </div>
          <h1 class="panel-title"><i class="fa fa-book fa-lg"></i> Articles</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table class="dashboard_stats">
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
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="pull-right">
            <div class="btn-group" data-toggle="buttons">
              <a class="btn" href="<?php echo $new_note?>"><i class="fa fa-plus"></i></a>
            </div>
          </div>
          <h1 class="panel-title"><i class="fa fa-files-o fa-lg"></i> Notes</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table class="dashboard_stats">
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
  </div><!-- row -->
  <div class="row">

    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="pull-right">
            <div class="btn-group" data-toggle="buttons">
              <a class="btn" href="<?php echo $new_note?>"><i class="fa fa-plus"></i></a>
            </div>
          </div>
          <h1 class="panel-title"><i class="fa fa-camera fa-lg"></i> Photos</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table class="dashboard_stats">
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
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="pull-right">
            <div class="btn-group" data-toggle="buttons">
              <a class="btn" href="<?php echo $new_note?>"><i class="fa fa-plus"></i></a>
            </div>
          </div>
          <h1 class="panel-title"><i class="fa fa-calendar fa-lg"></i> Events</h1>
        </div>
        <div class="panel-body">
          <div id="chart-sale" class="chart" style="width: 100%; height: 175px;">
          <table class="dashboard_stats">
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
