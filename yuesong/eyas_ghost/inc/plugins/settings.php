<?php 
// 禁用google字体，优化后台速度
if(!function_exists('ey_disable_googlefonts')):
function ey_disable_googlefonts( $translations, $text, $context, $domain ) {
    if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
    	$translations = 'off';
	}
	return $translations;
}
add_filter( 'gettext_with_context', 'ey_disable_googlefonts', 888, 4 );
endif;

// 修改wp后台底部版权信息
if(!function_exists('ey_modify_footer_admin')):
function ey_modify_footer_admin () {
	echo '网站后台管理系统';
}
add_filter('admin_footer_text', 'ey_modify_footer_admin');
endif;

// 移除顶部logo
if(!function_exists('ey_admin_bar_remove_logo')):
function ey_admin_bar_remove_logo() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('updates');
}
add_action('wp_before_admin_bar_render', 'ey_admin_bar_remove_logo', 0);
endif;

// 中文名图片上传
if(!function_exists('ey_upload_file_chinese_name')):
function ey_upload_file_chinese_name($filename) {  
	$parts = explode('.', $filename);  
	$filename = array_shift($parts);  
	$extension = array_pop($parts);  
	foreach ( (array) $parts as $part)  
	$filename .= '.' . $part;  
	    
	if(preg_match('/[一-龥]/u', $filename)){  
		$filename = md5($filename);  
	}  
	$filename .= '.' . $extension;  
	return $filename ;  
}  
add_filter('sanitize_file_name', 'ey_upload_file_chinese_name', 5,1);
endif;

//去除头部无用代码
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link'); 
// 禁用embed
remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

/*
 * 关闭XMLRPC
 */
add_filter('xmlrpc_enabled', '__return_false');


