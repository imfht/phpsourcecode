<?php get_header(); ?>
	
	    <div class="content" id="content">
        <div class="row">
          <div class="col-xs-12 col-md-8">
  	        <div class="items">
  	        	<?php if(have_posts()) :while(have_posts()) : the_post(); ?>
                <aside class="article-item">
                    <div class="panel panel-default">
                      <div class="panel-body">
                        <h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                        <?php if(has_post_thumbnail()): ?>
							<div class="thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
						<?php endif; ?>
                        <div class="bio"><?php the_abstract(get_the_content(),400); ?>
                        </div>
                      </div>
                      <div class="panel-footer">
                        <div class="attrs">
                          <span><i class="icon icon-eye"></i><?php the_views(); ?></span>
                          <span><i class="icon icon-clock"></i><?php the_date(); the_time(); ?></span>
                        </div>
                      </div>
                    </div>
                </aside>
                <?php endwhile;endif; ?>
                <!--/items-->
              <div class="pagination">
                <?php pagenav(); ?>
              </div>
  	        </div>
          </div>
          <div class="col-xs-12 col-md-4 hidden-xs">
            <?php get_sidebar('home'); ?>
          </div>
        </div>
	    </div>
<?php get_footer(); ?>