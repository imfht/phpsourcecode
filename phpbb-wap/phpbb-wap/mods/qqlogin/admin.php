<?php
/**
* @package phpBB-WAP MODS
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* 本文件是安装QQ登录的后台管理
*/

require ROOT_PATH . 'mods/qqlogin/functions.php';

$template->set_filenames(array(
	'body' => 'qqlogin_admin.tpl')
);

if (isset($_POST['appid']) && isset($_POST['appkey'])) 
{
	$appid 	= $_POST['appid'];
	$appkey = $_POST['appkey'];
	$callback = $_POST['callback'];
	
	sava_config($appid, $appkey, $callback);
	
	error_box('HEADER_BOX', 'ＱＱ登录信息已保存');	
}

$config = get_config();

$template->assign_vars(array(
	'APPID'				=> $config['appid'],
	'APPKEY'			=> $config['appkey'],
	'CALLBACK'			=> $config['callback'],
	'U_ADMIN_MODS'		=> append_sid('admin_mods.php'),
	'S_QQLOGIN_ACTION'  => append_sid(ROOT_PATH . 'admin/admin_mods.php?mode=admin&mods=qqlogin'))
);

$template->pparse('body');
?>