<?php
/*
 * Adminer Plugin
 * Generated at [[REPLACE_GENERATION_DATETIME]]
 * Kernel Version: [[REPLACE_KERNEL_VERSION]]
 */
if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/*
Plugin Name: Adminer数据库编辑器
Version: [[REPLACE_CURRENT_VERSION]]
Description: Adminer数据库编辑器,由[[REPLACE_KERNEL_VERSION]]版本修改而来
Author: FSGMHoward@IXNet
Author Email: howard@ixnet.work
Author URL: https://www.ixnet.work/
Plugin URL: https://blog.ixnet.work/2016/01/22/adminer/
For: 不限
*/

function ixlab_adminer_navi()
{
    echo '<li ';
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'ixlab_adminer') {
        echo 'class="active"';
    }
    echo '><a href="index.php?plugin=ixlab_adminer"><span class="glyphicon glyphicon-briefcase"></span> Adminer数据库编辑器</a></li>';
}

addAction('navi_3', 'ixlab_adminer_navi');
addAction('navi_9', 'ixlab_adminer_navi');
