<?php
/**
 * Created by PhpStorm.
 * User: wangjunfeng
 * Date: 15-6-13
 * Time: 下午7:46
 */
//防止有人恶意访问此文件，所以在没有 WP_UNINSTALL_PLUGIN 常量的情况下结束程序
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

//移除配置
delete_option('jw_wp_level_useragent_options');
