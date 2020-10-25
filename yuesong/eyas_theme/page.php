<?php get_header(); ?>

<div class="container">
    <div class="content">
    	<div class="row">
      		<div class="col-xs-12 col-md-8 ">
		    	<?php if(have_posts()) :while(have_posts()) : the_post(); ?>
		        <div class="thumbnail post-container">
				  <?php if(has_post_thumbnail()): ?>
		          <?php the_post_thumbnail(); ?>
		          <?php endif; ?>
				  <div class="caption">
				    <div class="single-header">
				      <h1 class="title"><?php the_title(); ?></h1>
				      <div class="attrs">
				        <span><i class="icon-eye"></i><?php the_views(); ?></span>
				        <span><i class="icon-clock"></i><?php echo get_the_date('Y-m-d H:i:s'); ?></span>
				      </div>
				    </div>
				    <div class="text">
				    	<?php the_content(); ?>
					</div>
				  </div>
				</div>
				<?php endwhile;endif; ?>
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