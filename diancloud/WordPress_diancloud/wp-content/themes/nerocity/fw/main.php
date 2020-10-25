<?php
    define( 'MYTHEMES_DEV_LOGO', get_template_directory_uri() .'/media/admin/images/mythemes-logo.png' );
    define( 'MYTHEMES_DEV_ICON', get_template_directory_uri() .'/media/admin/images/icon.png' );
    define( 'MYTHEMES_SHORT_PATH' , true );
    
    $mainDir = get_template_directory();
	
    include $mainDir . '/fw/mythemes.deb.class.php';
    include $mainDir . '/fw/mythemes.tools.class.php';
    include $mainDir . '/fw/tools.class.php';
    
    include $mainDir . '/fw/header.class.php';
    
    /* READ OPTIONS | META */
    include $mainDir . '/fw/sett.class.php';
    include $mainDir . '/fw/meta.class.php';

    include $mainDir . '/fw/cfg.php';
    include $mainDir . '/fw/mythemes.class.php';
    
    include $mainDir . '/fw/mythemes.layout.class.php';
	
    /* CUSTOM POSTS */
    include $mainDir . '/cfg/admin/resources/boxes.php';
    
    
    /* SET DEFAULT VALUES FOR SETTINGS FROM PAGES */
    include $mainDir . '/cfg/admin/default.php';
    
    /* LOAD THEME ADMIN DATA */
    if( is_admin() ){
        include $mainDir . '/fw/admin/ahtml.class.php';
        
        /* REGISTER PAGES */
        include $mainDir . '/cfg/admin/pages.php'; 
        include $mainDir . '/fw/admin/main.php';
    }
    
    /* load plugins */
    include $mainDir . '/fw/plg.php';
	
    /* INIT ACTIONS, SHORTCODES, AJAX */
    myThemes::init_actions();
    myThemes::init_filters();
    
    /* REGISTER ( MENUS | SIDEBARS ) */
    myThemes::reg_menus();
    myThemes::reg_sidebars();
?>