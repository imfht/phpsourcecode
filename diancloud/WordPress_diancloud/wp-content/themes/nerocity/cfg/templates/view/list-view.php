<?php global $post, $posts_total, $posts_index; ?>
<article <?php post_class( 'row' ); ?>>

    <?php
        $classes = 'post-content col-md-12 col-lg-12';

        if( has_post_thumbnail() ){
    ?>
            <div class="post-thumbnail col-md-4 col-lg-4">
                <div class="overflow-wrapper">
                    <a href="<?php echo get_permalink( $post -> ID ); ?>">
                        <?php echo get_the_post_thumbnail( $post -> ID , 'grid-thumbnail' , esc_attr( $post -> post_title ) , 'img-background effect-scale-rotate' ); ?>      
                    </a>
                </div>
            </div>
    <?php
            $classes = 'post-content col-md-8 col-lg-8';
        }
    ?>

    <div class="<?php echo $classes; ?>">

        <h2 class="post-title">
        <?php if( !empty( $post -> post_title ) ) { ?>
    
                <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( $post -> post_title ); ?>"><?php the_title(); ?></a>
    
            <?php } else { ?>
        
                <a href="<?php the_permalink() ?>"><?php _e( 'Read more about ..' , 'myThemes' ) ?></a>
        
            <?php } ?>
        </h2>

        <?php get_template_part( 'cfg/templates/meta' ); ?>

        <?php
            if( !empty( $post -> post_excerpt ) ){
                the_excerpt();
                echo '<a href="' . get_permalink( $post -> ID ) . '">' . __( 'Read More' , 'myThemes' ) . ' &rarr;</a>';
            }
            else{
                the_content( 'Read More &rarr;' );    
            }
            
        ?>

        <div class="clearfix"></div>
    </div>

    <!-- BOTTOM DELIMITER -->
    <?php if( $posts_total > $posts_index ){ ?>

        <div class="clearfix"></div>

        <div class="col-lg-12">
          <div class="post-delimiter"></div>  
        </div>

    <?php } ?>


</article>