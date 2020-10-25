<?php
class mythemes_tools
{   
    /* WEB RESOURCES */
    static function is_url( $url ){
    
        $self_host = str_replace( '.' , '\.' ,  $_SERVER[ 'HTTP_HOST' ] );
    
        /* PROTOCOL */
        $regex = "((https?|ftp)\:\/\/)?";

        /* USER AND PASS */
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

        /* HOST OR IP */
        $regex .= "(" . $self_host . "|([a-z0-9-.]*)\.([a-z]{2,3}))";

        /* PORT */
        $regex .= "(\:[0-9]{2,5})?";

        /* PATH */
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";

        /* GET QUERY */
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";

        /* ANCHOR */
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?";

        $rett = false;
        
        if( preg_match( "/^$regex$/", $url ) ){
            $rett = true;
        }

        return $rett;
    }

    /* WORDPRESS RESOURCES */
    static function get_posts( $args = array() ){

        $deff = array(
            'post_type' => 'page',
            'posts_per_page' => 40,
            'post_status' => 'publish'
        );

        if( empty( $args ) ){
            $args = $deff;
        }
        else{
            if( !isset( $args[ 'post_type' ] ) ){
                $args[ 'post_type' ] = $deff[ 'post_type' ];
            }

            if( !isset( $args[ 'posts_per_page' ] ) ){
                $args[ 'posts_per_page' ] = $deff[ 'posts_per_page' ];
            }

            if( !isset( $args[ 'post_status' ] ) ){
                $args[ 'post_status' ] = $deff[ 'post_status' ];
            }
        }

        $query = new WP_Query( $args );

        $rett = array( 0 => __( ' - Select from list - ' , 'myThemes' ) );

        if( count( $query -> posts ) ){
            foreach( $query -> posts as $p ){
                $rett[ $p -> ID ] = $p -> post_title;
            }
        }

        return $rett;
    }

    /* COLORS */    
    static function hex2rgb( $hex ){
        $hex = str_replace( "#", "", $hex );

        if( strlen( $hex ) == 3 ) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }

        $rgb = array( $r, $g, $b );
        return implode( ",", $rgb );
    }

    static function brightness( $hex, $steps ){
        /* Steps should be between -255 and 255. Negative = darker, positive = lighter */
        $steps = max( -255, min( 255, $steps ) );

        /* Format the hex color string */
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $hex = 
            str_repeat( substr( $hex, 0, 1 ), 2) .
            str_repeat( substr( $hex, 1, 1 ), 2 ) .
            str_repeat( substr( $hex, 2, 1 ), 2 );
        }

        /* Get decimal values */
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );

        // Adjust number of steps and keep it inside 0 to 255
        $r = max( 0, min( 255, $r + $steps ) );
        $g = max( 0, min( 255, $g + $steps ) );  
        $b = max( 0, min( 255, $b + $steps ) );

        $r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
        $g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
        $b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

        return '#' . $r_hex . $g_hex . $b_hex;
    }
}
?>