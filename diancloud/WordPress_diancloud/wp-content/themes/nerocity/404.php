<?php get_header(); ?>

    <div class="mythemes-page-header">

      <div class="container">
        <div class="row">

          <div class="col-lg-12">
            <h1><?php _e( 'Not found results' , 'myThemes' ); ?></h1>
            <nav class="mythemes-nav-inline">
              <ul>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'go home' , 'myThemes' ); ?>"><i class="icon-home"></i> <?php _e( 'Home' , 'myThemes' ); ?></a></li>
                <li><?php _e( 'Error 404' , 'myThemes' ); ?></li>
              </ul>
            </nav>
          </div>

        </div>
      </div>

    </div>

    <div class="content">
        <div class="container">
            <div class="row">
            <?php
                /* GET LAYOUT DETAILS */
                $myThemes_layout = new mythemes_layout( );

                /* LEFT SIDEBAR */
                $myThemes_layout -> echoSidebar( 'left' );
            ?>

                <!-- CONTENT -->
                <section class="<?php echo $myThemes_layout -> contentClass(); ?>">

                <?php
                    /* LEFT WRAPPER */
                    echo $myThemes_layout ->  contentWrapper( 'left' );
                ?>
                    <article>
                        <p><?php _e( 'We apologize but this page, post or resource does not exist or can not be found. Perhaps it is necessary to change the call method to this page, post or resource.' , 'myThemes' ) ?></p>
                    </article>

                <?php
                    /* RIGHT WRAPPER */
                    echo $myThemes_layout ->  contentWrapper( 'right' );
                ?>

                </section>
            <?php

                /* RIGHT SIDEBAR */
                $myThemes_layout -> echoSidebar( 'right' );
            ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>