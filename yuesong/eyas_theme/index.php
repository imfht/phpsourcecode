<?php get_header(); ?>
<div class="jumbotron" style="background-image: url(<?php bloginfo('template_directory'); ?>/images/title-bg.png);">
  <div class="jumbotron-content">
    <h1><?php bloginfo(); ?></h1>
    <p>博客就是一个收藏夹</p>
    <p><a class="btn btn-primary btn-lg" href="<?php echo get_page_link(25); ?>" role="button">关于我</a></p>
  </div>
</div>
<div class="container">
  <div class="content" id="content">
    <div class="row">
      <div class="col-xs-12 col-md-8 post-container">
        <div class="items">
            <?php if(have_posts()) :while(have_posts()) : the_post(); ?>
            <aside class="article-item">
                <div class="panel panel-default">
                    <div class="panel-body">
                      <?php if(has_post_thumbnail()): ?>
                      <div class="thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
                      <?php endif; ?>
                      <h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                      <div class="bio"><p>
			<?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 330,"..."); ?></p>
                      </div>
                    </div>
                    <div class="panel-footer">
                      <div class="attrs">
                        <span><i class="icon icon-eye"></i><?php the_views(); ?></span>
                        <span><i class="icon icon-clock"></i><?php echo get_the_date('Y-m-d H:i:s'); ?></span>
                      </div>
                    </div>
                  </div>
            </aside>
            <?php endwhile;endif; ?><!--/items-->
          <div class="pagination">
            <?php pagination($query_string); ?>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-md-4 hidden-xs sidebar">
          <div class="sidebar-block most">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">页面二维码</h3>
              </div>
              <div class="panel-body">
                <div id="page-erweima"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-block most">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">博客目录</h3>
              </div>
              <div class="panel-body">
                <ul class="list-group">
                  <?php the_category_list(); ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="sidebar-block most">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">访问最多</h3>
              </div>
              <div class="panel-body">
                <?php the_most_postviews(); ?>
              </div>
            </div>
          </div>
          <div class="sidebar-block most">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">最近更新</h3>
              </div>
              <div class="panel-body">
                <?php the_laest_post(); ?>
              </div>
            </div>
          </div>
          <div class="sidebar-block most">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">随机文章</h3>
              </div>
              <div class="panel-body">
                <?php the_random_posts(); ?>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
