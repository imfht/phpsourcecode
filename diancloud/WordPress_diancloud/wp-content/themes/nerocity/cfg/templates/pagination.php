<?php 
global $wp_query;

$total = $wp_query -> max_num_pages;
    
if ( $total > 1 )  {

    $ps = get_option( 'permalink_structure' );

    if( empty( $ps ) ){
        $format = '&paged=%#%';

        if( get_option( 'show_on_front' ) == 'posts' ){
            $format = '?paged=%#%';    
        }
    } else {
        $format = 'page/%#%/';
    }

    $pagination = array(
        'base'          => get_pagenum_link( 1 ) . '%_%',
        'format'        => $format,
        'current'       => max( 1, get_query_var( 'paged' ) ),
        'total'         => $total,
        'show_all'      => false,
        'end_size'      => 1,
        'mid_size'      => 2,
        'prev_next'     => true,
        'prev_text'     => '&larr;',
        'next_text'     => '&rarr;',
        'type'          => 'list',
        'add_args'      => false,
        'add_fragment'
    );

    if( isset( $_GET[ 's' ] ) ){ /* IF IS SEARCH PAGE */
        $pagination[ 'format' ]             = '&paged=%#%';
        $pagination[ 'add_args' ]           = array();
        $pagination[ 'add_args' ][ 's' ]    = get_query_var( 's' );
    }
    
    $pgn = paginate_links( $pagination );
    
    if( !empty( $pgn ) ){
?>
        <div class="row">
            <div class="col-lg-12">
                <div class="pagination aligncenter">
                    <nav class="mythemes-nav-inline">
                        <?php echo $pgn ?>
                    </nav>
                </div>
            </div>
        </div>
<?php       
    }
}
?>