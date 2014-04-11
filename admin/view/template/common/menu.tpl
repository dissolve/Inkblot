<nav id="column-left">
  <ul id="menu">
    <li>
      <!--<div id="search">
        <button type="button" class="btn btn-link"><i class="fa fa-search fa-fw"></i></button>
        <input type="text" name="search" value="" placeholder="<?php echo $text_search; ?>" />
      </div>-->
    </li>
    <li id="dashboard"><a href="<?php echo $home; ?>"><i class="fa fa-home fa-fw fa-lg"></i> <span>Dashboard</span></a></li>
    <li id="catalog"><a class="parent"><i class="fa fa-tags fa-fw fa-lg"></i> <span>Blog</span></a>
      <ul>
        <li><a href="<?php echo $post; ?>">Posts</a></li>
        <li><a href="<?php echo $note; ?>">Notes</a></li>
        <li><a href="<?php echo $category; ?>">Categories</a></li>
        <li><a href="<?php echo $comments; ?>">Comments</a></li>
        <li><a href="<?php echo $pages; ?>">Pages</a></li>
        <!--<li><a class="parent">attr</a>
          <ul>
            <li><a href="<?php echo $attribute; ?>">attr1</a></li>
          </ul>
        </li>-->
      </ul>
    </li>
    <li id="system"><a class="parent"><i class="fa fa-cog fa-fw fa-lg"></i> <span>Settings</span></a>
      <ul>
        <li><a href="<?php echo $setting; ?>">Settings</a></li>
        <li><a href="<?php echo $user; ?>">About Me</a></li>
        <li><a href="<?php echo $error_log; ?>">Error Log</a></li>
      </ul>
    </li>
  </ul>
</nav>
