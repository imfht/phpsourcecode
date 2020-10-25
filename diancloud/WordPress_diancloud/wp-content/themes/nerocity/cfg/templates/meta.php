<div class="meta">
    <div class="row">
        <?php global $post; ?>
        <div class="col-xs-4  col-sm-4 col-md-4 col-lg-4">
        <time datetime="<?php echo get_post_time( 'Y-m-d', false , $post -> ID  ); ?>"><i class="icon-calendar"></i><?php echo get_post_time( get_option( 'date_format' ), false , $post -> ID  ); ?></time>
        </div>
        <?php
            if( $post -> comment_status == 'open' ) {
                $nr = get_comments_number( $post -> ID );
                if( $nr == 1){
                    $comments = $nr . ' ' . __( 'Comment' , 'myThemes' );
                }
                else{
                    $comments = $nr . ' ' . __( 'Comments' , 'myThemes' );
                }
        ?>
                <div class="col-xs-4  col-sm-4 col-md-4 col-lg-4">
                    <a class="comments" href="<?php echo get_comments_link( $post -> ID ); ?>"><i class="icon-comment"></i><?php echo $comments; ?></a>
                </div>
        <?php
            }
        ?>
        <?php $name = get_the_author_meta( 'display_name' , $post -> post_author ); ?>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <a class="author" href="<?php echo get_author_posts_url( $post-> post_author ); ?>" title="<?php _e( 'Writed by' , 'myThemes' ); ?> <?php echo $name; ?>"><i class="icon-user-5"></i><?php echo $name; ?></a>
        </div>
    </div>
    <span class="meta-delimiter"></span>
</div>