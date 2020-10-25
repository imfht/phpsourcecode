<?php
/*
Plugin Name: 将文章上传或推送到微信
Plugin URI:
Description: 1，先在左侧“设置”菜单下“PTW设置”页面按照要求填写各项设置，可以设置上传或推送；2，在文章列表中选择需要上传的文章（一篇或多篇）；3，在“批量操作”下拉框中选择“推送到微信”选项，然后点击“应用”按钮，
等待页面出现上传成功的提示。注意：1，文章中必须有缩略图或图片。2，视频、音频等其他多媒体文件不会被上传。
Version:     1.0.1
Author:      Jacky Yang
Author URI:
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset
*/

if (!class_exists('YTD_Custom_Bulk_Action')) {

    class YTD_Custom_Bulk_Action {

        public function __construct() {
            if(is_admin()) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php' ;
                require 'Curl.php';
				require 'Wechat.php';
                // admin actions/filters
                add_action( 'admin_menu',           array( &$this, 'ptw_admin_menu' ), 110 );
                add_action('admin_footer-edit.php', array(&$this, 'custom_bulk_admin_footer'));
                add_action('load-edit.php',         array(&$this, 'custom_bulk_action'));
                add_action('admin_notices',         array(&$this, 'custom_bulk_admin_notices'));
            }
        }

        /**
         * 钩子函数：添加设置菜单
         */
        public function ptw_admin_menu() {
            add_options_page( 'Push To Wechat设置', 'PTW设置', 'manage_options',
                __FILE__, array( &$this, 'ptw_opt_page' ) );
        }

        /**
         * 回调函数：设置页面
         */
        public function ptw_opt_page() {
            $opts = [];
            // 数据库更新标记，控制页面显示更新消息
            $updated = false;

            if (!empty(trim($_POST['mp_app_id']))) {
                $opts['mp_app_id'] = trim($_POST['mp_app_id']);
            }
            if (!empty(trim($_POST['mp_app_key']))) {
                $opts['mp_app_key'] = trim($_POST['mp_app_key']);
            }
			// if (!empty(trim($_POST['wx_header']))) {
			// 	$opts['wx_header'] = trim(stripslashes($_POST['wx_header']));
			// }
			if (!empty(trim($_POST['qr_url']))) {
				$opts['qr_url'] = trim($_POST['qr_url']);
			}
			if (!empty(trim($_POST['push_type']))) {
				$opts['push_type'] = trim($_POST['push_type']);
			}
			if (!empty(trim($_POST['wx_wxid']))) {
				$opts['wx_wxid'] = trim($_POST['wx_wxid']);
			}

            // 将设置写入数据库
            if (!empty($opts)) {
                // 首次安装插件时将opts写入数据库
                if (false == get_option('ptw_opts')) {
                    update_option('ptw_opts', $opts);
                } else {
                    $opts_a = get_option('ptw_opts');
//                    echo '数据库中的opts: ' , print_r($opts_a);
//                    echo '<br>';
//                    echo '输入-数据库=差集: ', var_dump(array_diff_assoc($opts, $opts_a));
//                    echo '<br>';
//                    echo '数据库-输入=差集: ', var_dump(array_diff_assoc($opts_a, $opts));
//                    echo '<br>';

                    // 比较输入的opts和数据库中的$opts_a是否相同，若相同则不更新数据库
                    if (!empty(array_diff_assoc($opts, $opts_a)) or !empty(array_diff_assoc($opts_a, $opts))) {
//                        error_log('update_option');
                        update_option('ptw_opts', $opts);
                        $updated = true;
                    }
//                    error_log('not update_option');
                }
            }
            require 'options-page.php';
        }

        /**
         * Step 1: add the custom Bulk Action to the select menus
         */
        public function custom_bulk_admin_footer() {
            global $post_type;

            if($post_type == 'post') {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('<option>').val('wechat').text('<?= '推送到微信'?>').appendTo("select[name='action']");
                        jQuery('<option>').val('wechat').text('<?= '推送到微信'?>').appendTo("select[name='action2']");
                    });
                </script>
                <?php
            }
        }

        /**
         * Step 2: handle the custom Bulk Action
         *
         * Based on the post http://wordpress.stackexchange.com/questions/29822/custom-bulk-action
         */
        public function custom_bulk_action() {
            global $typenow;
            $post_type = $typenow;

            if($post_type == 'post') {
                // get the action
                // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
                $wp_list_table = _get_list_table('WP_Posts_List_Table');
                $action = $wp_list_table->current_action();
                $allowed_actions = array("wechat");

                if(!in_array($action, $allowed_actions)) return;

                // security check
                check_admin_referer('bulk-posts');

                // make sure ids are submitted. depending on the resource type, this may be 'media' or 'ids'
                if(isset($_REQUEST['post'])) {
                    $post_ids = array_map('intval', $_REQUEST['post']);
                }

                if(empty($post_ids)) return;

                // this is based on wp-admin/edit.php
                $sendback = remove_query_arg(array('wechated', 'untrashed', 'deleted', 'ids'), wp_get_referer());

                if ( !$sendback ) {
                    $sendback = admin_url( "edit.php?post_type=$post_type" );
                }

                $pagenum = $wp_list_table->get_pagenum();
                $sendback = add_query_arg('paged', $pagenum, $sendback);

                switch($action) {
                    // 对已经增加的action进行添加相应的操作
                    case 'wechat':
                        // 将post推送到微信
                        if (!Wechat::push_to_wechat($post_ids)) {
                            wp_die(__('Error pushing to wechat.'));
                        }
                        $sendback = add_query_arg( array('wechated' => count($post_ids),
                            'ids' => join(',', $post_ids) ), $sendback );
                    break;
                    default: return;
                }

                $sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );

                wp_redirect($sendback);
                exit();
            }
        }

        /**
         * Step 3: display an admin notice on the Posts page after pushing
         */
        public function custom_bulk_admin_notices() {
            global $post_type, $pagenow;

            if($pagenow == 'edit.php' && $post_type == 'post' && isset($_REQUEST['wechated']) && (int) $_REQUEST['wechated']) {
                $message = sprintf( _n( '1 post pushed to wechat.', '%s posts pushed to wechat.',
                    $_REQUEST['wechated'] ), number_format_i18n( $_REQUEST['wechated'] ) );
                echo "<div class=\"updated\"><p>{$message}</p></div>";
            }
        }

    }
}

new YTD_Custom_Bulk_Action();
