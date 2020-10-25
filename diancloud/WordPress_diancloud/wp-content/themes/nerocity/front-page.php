<?php get_header(); ?>

    <div class="content">
        <div class="container">

            <?php
                ob_start();
                get_sidebar( 'front-page-header-first' );
                $first = ob_get_clean();

                ob_start();
                get_sidebar( 'front-page-header-second' );
                $second = ob_get_clean();

                ob_start();
                get_sidebar( 'front-page-header-third' );
                $third = ob_get_clean();

                $sidebar_content = $first . $second . $third;

                if( !empty( $sidebar_content ) ){
            ?>

                    <!-- FEATURES -->
                    <aside class="row mythemes-header-items">

                        <!-- FEATURE 1 -->
                        <div class="col-md-4 col-lg-4 header-item">
                            <?php echo $first; ?>
                        </div>

                        <!-- FEATURE 2 -->
                        <div class="col-md-4 col-lg-4 header-item">
                            <?php echo $second; ?>
                        </div>

                        <!-- FEATURE 3 -->
                        <div class="col-md-4 col-lg-4 header-item">
                            <?php echo $third; ?>
                        </div>
                    </aside>
                    <div class="row">
                        <div class="col-lg-12 mythemes-delimiter"><div class="delimiter-item after-header-widgets"></div></div>
                    </div>

            <?php
                }
            ?>

            <div class="row">

            <?php
                if( get_option( 'show_on_front' ) == 'page' ){

                    /* GET LAYOUT DETAILS */
                    $myThemes_layout = new mythemes_layout( 'front-page' );

                    /* LEFT SIDEBAR */
                    $myThemes_layout -> echoSidebar( 'left' );
            ?>
                    <!-- CONTENT -->
                    <section class="<?php echo $myThemes_layout -> contentClass(); ?>">

                    <?php

                        /* LEFT WRAPPER */
                        echo $myThemes_layout ->  contentWrapper( 'left' );

                        /* CONTENT WRAPPER */
                        if( get_option( 'page_on_front' ) ){

                            $classes = implode( ' ' , get_post_class( 'mythemes-page' , (int)get_option( 'page_on_front' ) ) );

                            echo '<div class="' . $classes . '">';

                            $wp_query = new WP_Query( array(
                                'p' => get_option( 'page_on_front' ),
                                'post_type' => 'page'
                            ) );

                            if( count( $wp_query -> posts ) ){
                                foreach( $wp_query -> posts as $post ){

                                    $wp_query -> the_post();

                                    if( has_post_thumbnail() ){ ?>

                                        <div class="post-thumbnail">
                                            <?php echo get_the_post_thumbnail( $post -> ID , 'full-thumbnail' , esc_attr( $post -> post_title ) ); ?>
                                            <?php $caption = get_post( get_post_thumbnail_id() ) -> post_excerpt; ?>
                                            <?php
                                                if( !empty( $caption ) ) {
                                            ?>
                                                    <footer class="wp-caption">
                                                        <?php echo $caption; ?>
                                                    </footer>
                                            <?php
                                                }
                                            ?>
                                        </div>

                                    <?php }

                                    the_content();

                                    echo '<div class="clearfix"></div>';

                                    wp_link_pages( array( 'before' => '<div><p style="color: #000000;">' . __( 'Pages', "myThemes" ) . ':', 'after' => '</p></div>' ) );
                                }
                            }

                            echo '</div>';
                        }
                        else{
                            /* BLOG ON FRONT */
                            if( have_posts() ){
                                $posts_total = count( $wp_query -> posts );
                                $posts_index = 0;
                                while( have_posts() ){
                                    $posts_index++;
                                    the_post();
                                    get_template_part( 'cfg/templates/view/list-view' );
                                }
                            }
                            else{
                                echo '<h3>' . __( 'Not found results' , 'myThemes' ) . '</h3>';
                                echo '<p>' . __( 'We apologize but this page, post or resource does not exist or can not be found. Perhaps it is necessary to change the call method to this page, post or resource.' , 'myThemes' ) . '</p>';
                            }
                        }
                    ?>

                    <?php
                        /* RIGHT WRAPPER */
                        echo $myThemes_layout ->  contentWrapper( 'right' );
                    ?>
                    </section>
            <?php
                    /* RIGHT SIDEBAR */
                    $myThemes_layout -> echoSidebar( 'right' );

                }else{
                    get_template_part( 'cfg/templates/loop' );
                }
            ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>