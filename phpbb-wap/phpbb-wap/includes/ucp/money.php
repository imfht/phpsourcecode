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

$point_name = $board_config['points_name'];

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('用户不存在', E_USER_ERROR);
}

$user = intval($_GET[POST_USERS_URL]);

if ( $user == $userdata['user_id'] )
{
	trigger_error('您不能转给您自己！', E_USER_ERROR);
}

$sql = "SELECT username 
	FROM " . USERS_TABLE . " 
	WHERE user_id = '$user'";
	
if ( !$result = $db->sql_query($sql) )
{
	trigger_error('Could not obtain user information for sendpassword', E_USER_WARNING);
}

if ( !$row = $db->sql_fetchrow($result) )
{
	trigger_error('用户不存在', E_USER_ERROR);
}

$username = $row['username'];

if ( isset($_POST['submit']) && !empty($_POST['money_send']) )
{
	$money = intval($_POST['money_send']);
	
	if ( $userdata['user_level'] != ADMIN )
	{
		$money = abs($money);
	}

	if ( $money > $userdata['user_points'] && $userdata['user_level'] != ADMIN )
	{
		trigger_error('您没有足够的' . $point_name, E_USER_ERROR);
	}

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = user_points + $money
		WHERE user_id = $user";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not update users table', E_USER_WARNING);
	}

	if ( $userdata['user_level'] != ADMIN )
	{
		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - $money
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not update users table', E_USER_WARNING);
		}
	}

	trigger_error('已转给' . $username . $money . $point_name, E_USER_ERROR);
}
else
{
	$page_title = $point_name . '转账';
	page_header($page_title);

	$template->set_filenames(array(
		'body' => 'ucp/send_money.tpl')
	);

	if ( isset($_POST['submit']) && empty($_POST['money_send']) )
	{
		error_box('ERROR_BOX', '<p>数量不能为空</p>');
	}

	$template->assign_vars(array(
		'USERNAME' 			=> $username,
		'U_USER_PROFILE'	=> append_sid('ucp.php?mode=viewprofile&amp;u=' . $user),
		'USER_MONEY' 		=> $userdata['user_points'],
		'POINT_NAME' 		=> $point_name,
		'S_POST_ACTION'		=> append_sid("ucp.php?mode=money&amp;u=$user"))
	);

	$template->pparse('body');

	page_footer();
}
?>