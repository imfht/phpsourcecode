<?php
    class sett{
        
        static $deff;
        
		/* READ DEFAULT VALUES */
        static function deff( $optName )
        {
            if( isset( self::$deff[ $optName ] ) ){
                return self::$deff[ $optName ];
            }else{
                return null;
            }
        }
        
		/* READ SETTINGS VALUE ( STRING FORMAT ) */
        static function get( $optName , $strip = false )
        {   
            if( $strip )
                return stripcslashes ( get_theme_mod( $optName , self::deff( $optName ) ) );
            else
                return get_theme_mod( $optName , self::deff( $optName ) );
        }
        
        static function set2( $optName, $value ) 
        {
            return set_theme_mod( $optName, $value );
        }
		
        static function remove2( $optName )
        {
                return remove_theme_mod( $optName );
        }
		
        static function set( $optName , $value )
        {
            $mk = mktime();
            
            $v = get_theme_mod( $optName , $mk );
            
            if( $mk == $v && !isset( self::$deff[ $optName ] )){
                if( !empty( $value ) ){
                    set_theme_mod( $optName , $value );
                }
            }
            else{
                set_theme_mod( $optName , $value );
            }
            
            if( $value != self::deff( $optName ) ){
                set_theme_mod( $optName , $value );
            }
        }
        
        static function drop()
        {
            $key = isset( $_POST[ 'key' ] ) ? $_POST[ 'key' ] : exit;
            $option = isset( $_POST[ 'option' ] ) ? $_POST[ 'option' ] : exit;
            $names = isset( $_POST[ 'names' ] ) && !empty( $_POST[ 'names' ] ) && is_array( $_POST[ 'names' ] ) ?  $_POST[ 'names' ] : array();
            
            $result = get_theme_mod( $option );
            
            
            if( isset( $result[ $key ] ) ){
                unset( $result[ $key ] );
            }
            
            set_theme_mod( $option , $result );
            
            foreach( $names as $index => & $n ){
                if( substr( $n[ 'name' ] , 0 , strlen( 'mytheme-' ) ) == 'mytheme-' ){
                    remove_theme_mod( $n[ 'name' ] );
                }
            }
            
            exit();
        }
        
        static function toggleSave()
        {
            $key = isset( $_POST[ 'option' ] ) && $_POST[ 'option' ] ? $_POST[ 'option' ] : exit;

            if( get_theme_mod( $key ) ){
                remove_theme_mod( $key );
                $value = 0;
                $message = __( 'Successful disabled option!' , 'myThemes' );
            }else{
                set_theme_mod( $key , 1 );
                $value = 1;
                $message = __( 'Successful enabled option!', 'myThemes' );
            }
            
            echo str_replace( '"' , "'" , json_encode( array(
                'message' => $message,
                'label' => ahtml::getLogicButtonValue( array( 'value' => $value ) ),
                'value' => $value,
                'nofade' => 0
            )));
            
            exit();
        }
    };
?>