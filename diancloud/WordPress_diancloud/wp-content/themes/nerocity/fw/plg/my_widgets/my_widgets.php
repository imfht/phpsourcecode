<?php

function mythemes_autoload_widgets( $classname )
{
    my_widgets::load( $classname );
}

class my_widgets
{
    static $first_run = true;
    static $wdg = array();
    
    static function init()
    {            
        $currDir = get_template_directory() . '/fw/plg/my_widgets';
		$data = include $currDir . '/wdg.php';
        
        foreach( $data as $k ){
            self::$wdg[ "my_wdg_{$k}" ] = $currDir . "/my_wdg_{$k}.php";
            self::run( $k );
        }
    }
    
    static function load( $classname )
    {
        
        if( isset( self::$wdg[ $classname ] ) ) {
			include_once( self::$wdg[ $classname ] );
		}
    }
    
    static function run( $name )
    {
        if( self::$first_run ) {
            self::$first_run = false;

            spl_autoload_register( 'mythemes_autoload_widgets' );
        }
        
        register_widget( "my_wdg_{$name}" );
    }
}

my_widgets::init();

?>