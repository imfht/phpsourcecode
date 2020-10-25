<?php
    include get_template_directory() . '/fw/main.php';

    add_image_size( 'list-thumbnail' , 720 , 360 , true );
    add_image_size( 'grid-thumbnail' , 555 , 440 , true );

    add_action( 'after_setup_theme', array( 'myThemes' , 'setup' ) );
    add_action( 'widgets_init' ,  array( 'myThemes' , 'reg_sidebars' ) );
    add_action( 'wp_enqueue_scripts', array( 'myThemes' , 'init_scripts' ) );
    add_filter( 'wp_title', array( 'myThemes' , 'title' ) , 10, 2 );
    add_action( 'wp_head', array( 'myThemes' , 'favicon' ) );

    add_filter('the_excerpt_rss', array( 'myThemes' , 'rssThumbnail' ) );
    add_filter('the_content_feed', array( 'myThemes' , 'rssThumbnail' ) );
?>