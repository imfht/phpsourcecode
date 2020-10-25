<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

define('IN_PHPBB', true);
define('ROOT_PATH', './');
define('THIS_INDEX', true);

require(ROOT_PATH . 'common.php');
require(ROOT_PATH . 'mods/qqlogin/qc.class.php');

//session
$userdata = $session->start($user_ip, PAGE_INDEX);
init_userprefs($userdata);

//加载顶部页面
page_header();

$modules->display(MODULE_TOP); // 解析页面顶部内容
$modules->display(MODULE_MAIN);// 解析模块内容
$modules->display(MODULE_BOTTOM); // 解析界面底部内容

//加载模版
$template->set_filenames(array(
	'body' => 'index_body.tpl')
);

if ( $userdata['user_level'] == ADMIN )
{
	$template->assign_vars(array(
		'U_ADMIN_MODULE' 	=> 'admin/admin_module.php?page=' . $modules->page_id . '&sid=' . $userdata['session_id'],
		'U_ADMIN'			=> 'admin/index.php?sid=' . $userdata['session_id'])
	);
	$template->assign_block_vars('admin_module', array());
}

$template->assign_vars(array(
	'LOGO'		=> $board_config['site_logo'],
	'U_PEIVATE'	=> append_sid('privmsg.php?folder=inbox'),
	'U_LOGOUT'	=> append_sid('login.php?logout=true'),
	'U_QQ_LOGIN'=> $QQC->qq_loginurl(),
	'U_UCP'		=> append_sid('ucp.php?mode=viewprofile&u=' . $userdata['user_id']))
);

//解析模版内容
$template->pparse('body');
//加载底部页面
page_footer();
?>