<div class="sidebar-block search">
  <div class="panel panel-default">
    <div class="panel-body">
      <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <div class="input-group">
          <input type="text" class="form-control" value="<?php echo get_search_query(); ?>" name="s">
          <span class="input-group-btn">
            <input class="btn btn-default" type="submit" value="搜索">
          </span>
        </div><!-- /input-group -->
      </form>
    </div>
  </div>
</div>
<div class="sidebar-block qrcode">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">页面二维码</h3>
    </div>
    <div class="panel-body">
      <?php the_qrcode_image(); ?>
    </div>
  </div>
</div>
<div class="sidebar-block laste">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">最近更新</h3>
    </div>
    <div class="panel-body">
      <?php the_last_posts(10); ?>
    </div>
  </div>
</div>
<div class="sidebar-block laste">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">随机文章</h3>
    </div>
    <div class="panel-body">
      <?php the_random_posts(10); ?>
    </div>
  </div>
</div>