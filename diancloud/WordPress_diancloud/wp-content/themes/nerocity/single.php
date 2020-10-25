<?php get_header(); ?>


    <div class="content">
        <div class="container">
            <div class="row">

            <?php
                global $post;

                /* GET LAYOUT DETAILS */
                $myThemes_layout = new mythemes_layout( 'single' , $post -> ID );

                /* LEFT SIDEBAR */
                $myThemes_layout -> echoSidebar( 'left' );
            ?>
                <!-- CONTENT -->
                <section class="<?php echo $myThemes_layout -> contentClass(); ?>">

                <?php
                    /* LEFT WRAPPER */
                    echo $myThemes_layout ->  contentWrapper( 'left' );

                    if( have_posts() ){
                        while( have_posts() ){
                            the_post();    
                ?>
                            <article <?php post_class( 'row-fluid' ); ?>>

                                <?php
                                    $classes = 'no-thumbnail';



                                    if( has_post_thumbnail()  ){
                                ?>
                                        <div class="post-thumbnail">
                                        
                                        <?php
                                            echo get_the_post_thumbnail( $post -> ID , 'full-thumbnail' , esc_attr( $post -> post_title ) );
                                            $caption = get_post( get_post_thumbnail_id() ) -> post_excerpt;

                                            if( !empty( $caption ) ) {
                                                ?>
                                                    <footer><?php echo $caption; ?></footer>
                                                <?php
                                            }
                                        ?>
                                        </div>
                                <?php
                                        $classes = '';
                                    }
                                ?>

                                <!-- TITLE -->
                                <h1 class="post-title <?php echo $classes; ?>"><?php the_title(); ?></h1>
                          
                                <!-- TOP META : AUTHOR / TIME / COMMENTS -->
                                <?php get_template_part( 'cfg/templates/meta' ); ?>

                                <!-- CONTENT -->
                                <?php the_content(); ?>

                                <?php echo '<div class="clearfix"></div>'; ?>

                                <?php get_template_part( 'cfg/templates/bottom-meta' ); ?>
                            </article>

                            <!-- COMMENTS -->
                            <?php comments_template(); ?>

                <?php
                        } /* END ARTICLE */
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
            ?>
            
            </div>
        </div>
    </div>

<?php get_footer(); ?>