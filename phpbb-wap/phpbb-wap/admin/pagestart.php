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

if ( !defined('IN_PHPBB') )
{
	exit;
}

define('IN_ADMIN', true);
include(ROOT_PATH . 'common.php');

$userdata = $session->start($user_ip, PAGE_INDEX);
init_userprefs($userdata);

if (!$userdata['session_logged_in'])
{
	redirect(append_sid('login.php?redirect=admin/index.php', true));
}
else if ($userdata['user_level'] != ADMIN)
{
	trigger_error('您没有超级管理员权限', E_USER_NOTICE);
}

if ($_GET['sid'] != $userdata['session_id'])
{	
	redirect('index.php?sid=' . $userdata['session_id']);
}
/*
if (!$userdata['session_admin'])
{
	$query_string = (empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '?' . $_SERVER['QUERY_STRING'];
	$redirect_url = urlencode('admin/' . end(explode('/', $_SERVER['PHP_SELF'])) . $query_string .  '&admin=1');
	redirect(append_sid('login.php?redirect=' . $redirect_url, true));
}
*/

if (!$userdata['session_admin'])
{

	// 获取 &sid= 出现的位置
	// 如果查找到该字符串
	if (mb_strpos($_SERVER["REQUEST_URI"], '&sid=', 0, 'UTF-8') === true)
	{
		$end_sid_row = mb_strpos($_SERVER["REQUEST_URI"], '&sid=', 0, 'UTF-8');
	}
	// 否则算总长度
	else
	{
		$end_sid_row = mb_strlen($_SERVER["REQUEST_URI"], 'UTF-8');
	}

	// 还是用上面的方法把
	// $end_sid_row = mb_strpos($_SERVER["REQUEST_URI"], '&sid=', 'UTF-8');

	// 去除 &sid= 后面的部分
	$cut_notsid_str = mb_substr($_SERVER["REQUEST_URI"], 0, $end_sid_row, 'UTF-8');

	// 算出不包含&sid=字符串的部分的字符串长度
	//$num_request_uri = mb_strlen($cut_notsid_str, 'UTF-8');

	// 去除第一个字符 / ，得出 $redirect_url
	//$redirect_url = mb_substr($cut_notsid_str, 1, $num_request_uri, 'utf-8');

	// 是时候跳转到login.php了
	redirect(append_sid('login.php?redirect=' . $cut_notsid_str . '&admin=1', true));
}

page_header('超级管理面板');

?>