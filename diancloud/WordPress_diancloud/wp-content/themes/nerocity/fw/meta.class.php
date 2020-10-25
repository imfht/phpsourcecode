<?php
class meta{

    static function get( $postID , $metakey , $index = -1 )
    {   
        $metakey = 'mythemes-' . $metakey;

        $meta = get_post_meta( $postID , $metakey , true );

        if( $index  != -1 ){
            if( isset( $meta[ $index ] ) ){
                $meta = $meta[ $index ];
            }else{
                $meta = null;
            }
        }

        return $meta;
    }
    
    static function dget( $postID , $metakey , $default )
    {
        $metakey = 'mythemes-' . $metakey;

        $meta = get_post_meta( $postID , $metakey , true );

        if( empty( $meta ) ){
            $meta = $default;
        }

        return $meta;
    }

    static function set( $postID , $metakey , $value )
    {   
        $metakey = 'mythemes-' . $metakey;
        return update_post_meta( $postID , $metakey , $value );
    }
}
?>
