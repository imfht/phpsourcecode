<?php get_header(); ?>

    <div class="mythemes-page-header">

      <div class="container">
        <div class="row">

          <div class="col-sm-8 col-md-9 col-lg-9">
            <?php
                global $wp_query;

                $day_list    = '';
                $month_list  = '';
                $year_list   = '';


                if ( is_day() ) {

                    $day        = get_the_date( );
                    $month      = get_the_date( 'F' );
                    $year       = get_the_date( 'Y' );

                    $day_list   = '<li><time datetime="' . get_the_date( 'Y-m-d' ) . '">'  . get_the_date( 'd' ) . '</a></li>';
                    $month_list = '<li><a href="' . get_month_link( $year, $month ) . '" title="' . __( 'Monthly archives' , 'myThemes' ) . ': ' . get_the_date( 'F Y' ) . '">'  . $month . '</a></li>';
                    $year_list  = '<li><a href="' . get_year_link( $year ) . '" title="' . __( 'Yearly archives' , 'myThemes' ) . ': ' . get_the_date( 'Y' ) . '">'  . $year . '</a></li>';

                    echo '<h1>' . __( 'Daily archives' , 'myThemes' ) . ' "' . $day . '"</h1>';
                }else if ( is_month() ) {

                    $month      = get_the_date( 'F' );
                    $year       = get_the_date( 'Y' );

                    $month_list = '<li><time datetime="' . get_the_date( 'Y-m-d' ) . '">' . $month . '</a></li>';
                    $year_list  = '<li><a href="' . get_year_link( $year ) . '" title="' . __( 'Yearly archives' , 'myThemes' ) . ': ' . get_the_date( 'Y' ) . '">'  . $year . '</a></li>';

                    echo '<h1>' . __( 'Monthly archives' , 'myThemes' ) . ' "' . $month . '"</h1>';
                }else if ( is_year() ) {

                    $year       = get_the_date( 'Y' );
                    $year_list  = '<li><time datetime="' . get_the_date( 'Y-m-d' ) . '">'  . $year . '</a></li>';

                    echo '<h1>' . __( 'Yearly archives' , 'myThemes' ) . ' "' . $year . '"</h1>';
                }else {

                    $year       = __( 'Blog archives' , 'myThemes' );
                    $year_list  = '<li>' . $year . '</li>';

                    echo '<h1>' . $year . '</h1>';
                }
            ?>
            <nav class="mythemes-nav-inline">
              <ul>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'go home' , 'myThemes' ); ?>"><i class="icon-home"></i> <?php _e( 'Home' , 'myThemes' ); ?></a></li>
                <?php echo $year_list; ?>
                <?php echo $month_list; ?>
                <?php echo $day_list; ?>
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