<?php

if (!defined('APP_IN')) exit('Access Denied');
$m_name = '缓存管理';
$ac_arr = array('del'=>'清除缓存');
$ac = isset($_REQUEST['a']) &&isset($ac_arr[$_REQUEST['a']]) ?$_REQUEST['a'] : 'default';
if ($ac == 'del')
{
$fzz = new fzz_cache;
$fzz->clear_all();
if( !($fzz->_isset( "common_cache")) ){
$fzz->set("common_cache",display_common_cache(),CACHETIME);
}
}
else
{
showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].('成功'),ADMIN_PAGE."?m=main");

?>