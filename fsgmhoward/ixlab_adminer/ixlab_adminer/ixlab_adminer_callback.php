<?php
if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

/**
 * 在禁用插件的时候删除数据表。实际上在>=1.7版本的插件都已经没有安装这个数据表了
 *
 * @throws Exception
 */
function callback_inactive()
{
    global $m;
    $m->query("DROP TABLE IF EXISTS `".DB_PREFIX."ixlab_adminer`");
}
