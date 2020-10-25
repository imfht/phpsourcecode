<?php
/*
Plugin Name: WP-Level-Useragent
Plugin URI: http://blog.wangjunfeng.com
Description: 一个简单的，可以显示评论者评论等级及UA信息的插件。
Version: 0.1.0
Author: Jeffery Wang
Author URI: http://blog.wangjunfeng.com/
*/

/* Copyright 2015  Jeffery Wang  (email: admin@wangjunfeng.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// 检验并设置常用常量
if (!defined('WP_CONTENT_URL')) {
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('WP_PLUGIN_URL')) {
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
}
if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

$default_options = array(
    'display_position' => 'after',
    'show_level' => '1',
    'show_os' => '1',
    'show_browser' => '1',
    'level_name' => array('潜水', '冒泡', '吐槽', '活跃', '话唠', '畅言', '专家', '大师', '传说', '神话'),
    'level_count' => array(5, 10, 15, 20, 25, 30, 35, 40, 45),
    'admin_email' => '',
);

// 插件配置信息
$jw_wlu_options = get_option('jw_wp_level_useragent_options');
$jw_wlu_options = $jw_wlu_options ? $jw_wlu_options : $default_options;
$jw_wlu_display_position = $jw_wlu_options['display_position'];
$jw_wlu_show_level = $jw_wlu_options['show_level'];
$jw_wlu_show_os = $jw_wlu_options['show_os'];
$jw_wlu_show_browser = $jw_wlu_options['show_browser'];
$jw_wlu_level_name = $jw_wlu_options['level_name'];
$jw_wlu_level_count = $jw_wlu_options['level_count'];
$jw_wlu_admin_email = $jw_wlu_options['admin_email'];

$css_url = WP_PLUGIN_URL . "/wp-level-useragent/css/ua.css";

include(WP_PLUGIN_DIR . '/wp-level-useragent/includes/jw-wlu-detect-os.php');
include(WP_PLUGIN_DIR . '/wp-level-useragent/includes/jw-wlu-detect-webbrowser.php');
include(WP_PLUGIN_DIR . '/wp-level-useragent/includes/jw-wlu-level.php');

// 主函数
function wp_level_useragent() {
    global $comment, $useragent, $jw_wlu_display_position;

    get_currentuserinfo();

    $useragent = wp_strip_all_tags($comment->comment_agent, false);
    if ($jw_wlu_display_position == "before") {
        display_level_useragent();
        ua_comment();
        add_filter('comment_text', 'wp_level_useragent');
    } elseif ($jw_wlu_display_position == "after") {
        ua_comment();
        display_level_useragent();
        add_filter('comment_text', 'wp_level_useragent');
    } elseif ($jw_wlu_display_position == "custom") {
        display_level_useragent();
    } else {

    }
}

/**
 * 插件启用时初始化
 */
register_activation_hook(__FILE__, 'jw_wlu_active');
function jw_wlu_active() {
    global $default_options;
    add_option('jw_wp_level_useragent_options', $default_options, '', 'yes');
}

/**
 * 添加需要的css文件
 */
function css() {
    wp_register_style('us_css', plugins_url('css/ua.css', __FILE__));
    wp_register_style('font_css', 'http://apps.bdimg.com/libs/fontawesome/4.2.0/css/font-awesome.min.css');
    if (!is_admin()) {
        wp_enqueue_style('us_css');
        wp_enqueue_style('font_css');
    }
}

if (!is_admin()) {
    add_action('wp_head', 'css');
}

/**
 * 展示函数
 */
function display_level_useragent() {
    global $comment, $jw_wlu_admin_email, $jw_wlu_show_level, $jw_wlu_show_os, $jw_wlu_show_browser;

    if ($comment->comment_type == 'trackback' || $comment->comment_type == 'pingback') {
        $ua = "";
    } else {
        $level = $webbrowser = "";
        if ($comment->comment_author_email != '' && $jw_wlu_show_level) {
            global $jw_wlu_level_name, $jw_wlu_level_count;
            $level = jw_wlu_user_level($comment->comment_author_email, $jw_wlu_admin_email, $jw_wlu_level_name, $jw_wlu_level_count);
        }
        $os = $jw_wlu_show_os ? jw_wlu_detect_os() : '';
        $webbrowser = $jw_wlu_show_browser ? jw_wlu_detect_webbrowser() : '';

        $ua = '<div class="wp-level-useragent">' . $level . $webbrowser . $os . '</div>';
    }

    if (empty($_POST['comment_post_ID'])) {
        echo $ua;
    }
}

function jw_level_useragent_output_custom() {
    global $jw_wlu_display_position, $useragent, $comment;

    if ($jw_wlu_display_position == "custom") {
        get_currentuserinfo();
        $useragent = wp_strip_all_tags($comment->comment_agent, false);
        display_level_useragent();
    }
}


function ua_comment() {
    global $comment;

    remove_filter('comment_text', 'wp_level_useragent');
    apply_filters('get_comment_text', $comment->comment_content);

    if (empty($_POST['comment_post_ID'])) {
        echo apply_filters('comment_text', $comment->comment_content);
    }
}


if ($jw_wlu_display_position != 'custom') {
    add_filter('comment_text', 'wp_level_useragent');
}

/**
 * 添加管理菜单
 */
add_action('admin_menu', 'jw_wlu_create_menu');
function jw_wlu_create_menu() {
    add_options_page('WP-Level-UserAgent', 'WP-Level-UserAgent', 'manage_options', __FILE__, 'jw_wlu_options');
}

function jw_wlu_options() {
    global $default_options;
    include_once(WP_PLUGIN_DIR . '/wp-level-useragent/includes/jw-wlu-options.php');
}

// 添加设置页面链接
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'jw_wlu_plugin_actlinks');
function jw_wlu_plugin_actlinks($links) {
    $settings_link = '<a href="options-general.php?page=wp-level-useragent%2Fwp-level-useragent.php">设置</a>';
    array_unshift($links, $settings_link);
    return $links;
}
