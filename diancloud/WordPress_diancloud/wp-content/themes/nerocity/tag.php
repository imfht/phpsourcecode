<?php get_header(); ?>

    <?php global $wp_query; ?>
    
    <div class="mythemes-page-header">

      <div class="container">
        <div class="row">

          <div class="col-sm-8 col-md-9 col-lg-9">
            <h1 class="mythemes-title"><?php _e( 'Results for tag' , 'myThemes' ); ?> "<?php echo urldecode( get_query_var( 'tag' ) ); ?>"</h1>
            <nav class="mythemes-nav-inline">
              <ul>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'go home' , 'myThemes' ); ?>"><i class="icon-home"></i> <?php _e( 'Home' , 'myThemes' ); ?></a></li>
                <li><?php echo urldecode( get_query_var( 'tag' ) ); ?></li>
              </ul>
            </nav>
          </div>

          <div class="col-sm-4 col-md-3 col-lg-3 mythemes-posts-found">
                <div class="found-details">
                    <span>
                        <?php
                            echo $wp_query -> found_posts . ' ';

                            if( $wp_query -> found_posts == 1 ){
                                _e( 'Article' , 'myThemes' );
                            }
                            else{
                                _e( 'Articles' , 'myThemes' );
                            }
                        ?>
                    </span>
                </div>
          </div>

        </div>
      </div>

    </div>

    <div class="content">
        <div class="container">
            <div class="row">

                <?php get_template_part( 'cfg/templates/loop' ); ?>

            </div>
        </div>
    </div>

<?php get_footer(); ?>