<?php
global $wpdb;
$cfg = array(
    
    'sidebars-list' => array(
        'main-sidebar'          => __( 'Main Sidebar' , 'myThemes' ),
        'front-page-sidebar'    => __( 'Front Page Sidebar' , 'myThemes' ),
        'page-sidebar'          => __( 'Page Sidebar' , 'myThemes' ),
        'single-sidebar'        => __( 'Single Sidebar' , 'myThemes' ),
        'additional-sidebar'    => __( 'Additional Sidebar' , 'myThemes' )
    ),
    
    /* MENUS */
    'menus' => array(
        'header' => __( 'Header menu' , 'myThemes' )
    ),
    
    /* SIDEBARS */
    'sidebars' => array(
        array(
            'name' => __( 'Main Sidebar' , 'myThemes' ),
            'id' => 'main-sidebar',
            'description' => __( 'Main Sidebar - is used by default for next templates: 404, Archive, Author, Category, Search and Tag.' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title"><i></i>',
            'after_title' => '</h4>',
        ),
        array(
            'name' => __( 'Single Sidebar' , 'myThemes' ),
            'id' => 'single-sidebar',
            'description' => __( 'Single Sidebar - is used by default for single post.' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title"><i></i>',
            'after_title' => '</h4>',
        ),
        array(
            'name' => __( 'Page Sidebar' , 'myThemes' ),
            'id' => 'page-sidebar',
            'description' => __( 'Single Sidebar - is used by default for page.' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title"><i></i>',
            'after_title' => '</h4>',
        ),
        array(
            'name' => __( 'Front Page Sidebar' , 'myThemes' ),
            'id' => 'front-page-sidebar',
            'description' => __( 'Front Page Sidebar - is used by default for Front Page.' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title"><i></i>',
            'after_title' => '</h4>',
        ),
        array(
            'name' => __( 'Additional Sidebar' , 'myThemes' ),
            'id' => 'additional-sidebar',
            'description' => __( 'Additional Sidebar - is used for additional cases.' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title"><i></i>',
            'after_title' => '</h4>',
        ),

        /* HEADER SIDEBARS */
        array(
            'name' => __( 'Header - First Front Page Sidebar' , 'myThemes' ),
            'id' => 'front-page-header-first',
            'description' => __( 'Content for left front page header Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ),
        array(
            'name' => __( 'Header - Second Front Page Sidebar' , 'myThemes' ),
            'id' => 'front-page-header-second',
            'description' => __( 'Content for middle front page header Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ),
        array(
            'name' => __( 'Header - Third Front Page Sidebar' , 'myThemes' ),
            'id' => 'front-page-header-third',
            'description' => __( 'Content for right front page header Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        ),

        /* FOOTER SIDEBARS */
        array(
            'name' => __( 'Footer - First Sidebar' , 'myThemes' ),
            'id' => 'footer-first',
            'description' => __( 'Content for first footer Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5>',
            'after_title' => '</h5>',
        ),
        array(
            'name' => __( 'Footer - Second Sidebar' , 'myThemes' ),
            'id' => 'footer-second',
            'description' => __( 'Content for second footer Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5>',
            'after_title' => '</h5>',
        ),
        array(
            'name' => __( 'Footer - Third Sidebar' , 'myThemes' ),
            'id' => 'footer-third',
            'description' => __( 'Content for third footer Sidebar' , 'myThemes' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5>',
            'after_title' => '</h5>',
        )
    )
);
?>