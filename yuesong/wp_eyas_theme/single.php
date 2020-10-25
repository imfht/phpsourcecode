<?php get_header(); ?>
<div class="content" id="content">
	<div class="row">
		<ol class="breadcrumb">
			<?php show_breadcrumb(); ?>
		</ol>
	</div>
        <div class="row">
          <div class="col-xs-12 col-md-8">
          	<?php if(have_posts()) :while(have_posts()) : the_post(); ?>
  	        <div class="thumbnail">
  	        	<?php if(has_post_thumbnail()): ?>
					<?php the_post_thumbnail(); ?>
				<?php endif; ?>
                <div class="caption">
                    <div class="single-header">
                        <h1 class="title"><?php the_title(); ?></h1>
                        <div class="attrs">
                          <span><span class="glyphicon glyphicon-eye-open"></span><?php the_views(); ?></span>
                          <span><span class="glyphicon glyphicon-time"></span><?php the_date(); the_time(); ?></span>
                          <span><span class="glyphicon glyphicon-bookmark"></span><?php the_post_term(); ?></span>
                          <span><span class="glyphicon glyphicon-tags"></span><?php the_post_term("post_tag"); ?></span>
                        </div>
                    </div>
                    <div class="text">
						<?php the_content(); ?>
                    </div>
                    

                </div>
            </div>
            <?php endwhile;endif; ?>
            <div class="comments-container">
              <?php comments_template('/comments.php'); ?>
            </div>
          </div>
          <div class="col-xs-12 col-md-4 hidden-xs">
          	<?php get_sidebar('single'); ?>
          </div>
        </div>
	    </div>






<?php get_footer(); ?>