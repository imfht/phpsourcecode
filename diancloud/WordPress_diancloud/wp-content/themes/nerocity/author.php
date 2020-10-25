<?php get_header(); ?>

    <?php global $wp_query; ?>

    <div class="mythemes-page-header">

        <div class="container">
            <div class="row">

                <div class="col-sm-8 col-md-9 col-lg-9">
                    <h1><?php echo __( 'Author' , 'myThemes' ); ?></h1>
                    <nav class="mythemes-nav-inline">
                        <ul>
                            <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php _e( 'go home' , 'myThemes' ); ?>"><i class="icon-home"></i> <?php _e( 'Home' , 'myThemes' ); ?></a></li>
                            <li><?php echo get_the_author_meta( 'display_name' , $post-> post_author ); ?></li>
                        </ul>
                    </nav>
                </div>

                <div class="col-sm-4 col-md-3 col-lg-3 mythemes-author-avatar">
                    <div class="author-details">
                        <?php
                            echo get_avatar( $post -> post_author , 100 );
                        ?>
                        <span class="found-posts">
                            <?php
                                echo $wp_query -> found_posts . ' ';

                                if( $wp_query -> found_posts == 1 ){
                                    _e( 'post' , 'myThemes' );
                                }
                                else{
                                    _e( 'posts' , 'myThemes' );
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