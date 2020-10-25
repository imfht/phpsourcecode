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
	die('Hacking attempt');
}

if ($userdata['user_level'] != ADMIN)
{
	trigger_error('您不是管理员！', E_USER_ERROR);
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('用户不存在', E_USER_ERROR);
}

$user_id = intval($_GET[POST_USERS_URL]);
$profiledata = get_userdata($_GET[POST_USERS_URL]);

if ($profiledata['user_active'] == 1)
{
	if ($profiledata['user_level'] != USER)
	{
		trigger_error('您不能停用该会员！', E_USER_ERROR);
	}
	$sql = "UPDATE " . USERS_TABLE . " SET user_active = 0 WHERE user_id = $user_id";
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法禁用会员', E_USER_WARNING);
	} else {
		redirect(append_sid("ucp.php?mode=viewprofile&u=$user_id", true));
	}
} elseif ($profiledata['user_active'] == 0) {
	$sql = "UPDATE " . USERS_TABLE . " SET user_active = 1 WHERE user_id = $user_id";
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法激活用户', E_USER_WARNING);
	} else {
		redirect(append_sid("ucp.php?mode=viewprofile&u=$user_id", true));
	}
}

?>