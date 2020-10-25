<?php 
/*
Plugin Name: WPJAM 304 Header
Plugin URI: http://blog.wpjam.com/m/wpjam-304-header/ 
Description: WordPress 中开启 304 Not Modified Header，提高网站效率
Version: 0.1
Author: Denis
Author URI: http://blog.wpjam.com/
*/

add_filter('wp_headers','wpjam_headers',10,2);
function wpjam_headers($headers,$wp){
    if(!is_user_logged_in() && empty($wp->query_vars['feed'])){
        $headers['Cache-Control']   = 'max-age:600';
        $headers['Expires']         = gmdate('D, d M Y H:i:s', time()+600) . " GMT";

        $wpjam_timestamp = get_lastpostmodified('GMT')>get_lastcommentmodified('GMT')?get_lastpostmodified('GMT'):get_lastcommentmodified('GMT');
        $wp_last_modified = mysql2date('D, d M Y H:i:s', $wpjam_timestamp, 0).' GMT';
        $wp_etag = '"' . md5($wp_last_modified) . '"';
        $headers['Last-Modified'] = $wp_last_modified;
        $headers['ETag'] = $wp_etag;

        // Support for Conditional GET
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
            $client_etag = stripslashes(stripslashes($_SERVER['HTTP_IF_NONE_MATCH']));
        else $client_etag = false;

        $client_last_modified = empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? '' : trim($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        // If string is empty, return 0. If not, attempt to parse into a timestamp
        $client_modified_timestamp = $client_last_modified ? strtotime($client_last_modified) : 0;

        // Make a timestamp for our most recent modification...
        $wp_modified_timestamp = strtotime($wp_last_modified);

        $exit_required = false;

        if ( ($client_last_modified && $client_etag) ?
                 (($client_modified_timestamp >= $wp_modified_timestamp) && ($client_etag == $wp_etag)) :
                 (($client_modified_timestamp >= $wp_modified_timestamp) || ($client_etag == $wp_etag)) ) {
            $status = 304;
            $exit_required = true;
        }

        if ( $exit_required ){
            if ( ! empty( $status ) ){
                status_header( $status );
            }
            foreach( (array) $headers as $name => $field_value ){
                @header("{$name}: {$field_value}");
            }

            if ( isset( $headers['Last-Modified'] ) && empty( $headers['Last-Modified'] ) && function_exists( 'header_remove' ) ){
                @header_remove( 'Last-Modified' );
            }
            
            exit();    
        } 
    } 
    return $headers;
}