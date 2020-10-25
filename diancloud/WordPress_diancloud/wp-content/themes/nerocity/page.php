<?php get_header(); ?>

<?php
    if( have_posts() ){
        while( have_posts() ){
            the_post();
?>
            <div class="mythemes-page-header">

              <div class="container">
                <div class="row">

                  <div class="col-lg-12">
                    <h1><?php the_title(); ?></h1>
                    <nav class="mythemes-nav-inline">
                      <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'go home' , 'myThemes' ); ?>"><i class="icon-home"></i> <?php _e( 'Home' , 'myThemes' ); ?></a></li>
                        <li><?php the_title(); ?></li>
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
                        global $myThemes_layout;

                        /* GET LAYOUT DETAILS */
                        $myThemes_layout = new mythemes_layout( 'page' , $post -> ID );

                        /* LEFT SIDEBAR */
                        $myThemes_layout -> echoSidebar( 'left' );
                    ?>
                        <!-- CONTENT -->
                        <section class="<?php echo $myThemes_layout -> contentClass(); ?>">

                        <?php
                            /* LEFT WRAPPER */
                            echo $myThemes_layout ->  contentWrapper( 'left' );

                        ?>
                            <div <?php post_class( 'mythemes-page' ); ?>>

                                <?php 
                                    if( has_post_thumbnail() ){
                                ?>
                                        <div class="post-thumbnail">
                                            <?php echo get_the_post_thumbnail( $post -> ID , 'full-thumbnail' , esc_attr( $post -> post_title ) ); ?>
                                            <?php $caption = get_post( get_post_thumbnail_id() ) -> post_excerpt; ?>
                                            <?php if( !empty( $caption ) ) { ?>
                                                <footer><?php echo $caption; ?></footer>
                                            <?php } ?>
                                        </div>
                                <?php
                                    }
                                ?>

                                <!-- CONTENT -->
                                <?php the_content(); ?>

                                <?php echo '<div class="clearfix"></div>'; ?>

                                <?php wp_link_pages( array( 'before' => '<div><p style="color: #000000;">' . __( 'Pages', "myThemes" ) . ':', 'after' => '</p></div>' ) ); ?>

                            </div>

                            <!-- COMMENTS -->
                            <?php comments_template(); ?>

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

<?php
        } /* END PAGE */
    }
?>

<?php get_footer(); ?>