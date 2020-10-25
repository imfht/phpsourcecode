<?php 
function eyas_setup(){
	add_theme_support('post-thumbnails');
	register_nav_menu( 'main_menu', __( 'Main Menu', 'eyas' ) );//菜单
	register_nav_menu( 'right_menu', __( 'Right Menu', 'eyas' ) );//菜单
	// Home sidebar
	register_sidebar(array(
		'name' 		=> 'Home Sidebar',
		'id' 		=> 'home_sidebar',
		'description'=>'首页小工具',
		'class'		=> 'home-sidebar',
		'before_widget' => '<div id="%2$s" class="panel panel-default">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="panel-heading"><h3 class="panel-title">',
		'after_title'   => '</h3></div>',
		));
	// Single sidebar
	register_sidebar(array(
		'name' 		=> 'Single Sidebar',
		'id' 		=> 'single_sidebar',
		'description'=>'内页小工具',
		'class'		=> 'home-sidebar',
		'before_widget' => '<div id="%2$s" class="panel panel-default">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="panel-heading"><h3 class="panel-title">',
		'after_title'   => '</h3></div>',
		));
}
add_action( 'after_setup_theme', 'eyas_setup' );

include('inc/widget.php');			// add widget
include('inc/function.php');		//  theme function
// include('inc/post_type.php');		//custom post type and custom taxonomy
// include('inc/postviews/wp-postviews.php'); 	//postviews