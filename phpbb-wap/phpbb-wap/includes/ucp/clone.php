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
	exit;
}

if ($userdata['user_level'] != ADMIN)
{
	trigger_error('您不是管理员！', E_USER_ERROR);
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('对不起，用户不存在', E_USER_ERROR);
}

page_header($page_title);

$template->set_filenames(array(
	'body' => 'ucp/clone.tpl')
);

$user_id = intval($_GET[POST_USERS_URL]);

$sql = "SELECT * FROM " . USERS_TABLE . " 
	WHERE user_id = $user_id";
	
if (!($result = $db->sql_query($sql)))
{
	trigger_error('Could not obtain user information', E_USER_WARNING);
}
$userinfo = $db->sql_fetchrow($result);
$user_password = $userinfo['user_password'];
$username = $userinfo['username'];
$user_qq = $userinfo['user_qq'];
$user_website = $userinfo['user_website'];

$template->assign_vars(array(
	'USERNAME' => $username)
);

$sql = "SELECT * FROM " . USERS_TABLE . " 
	WHERE user_password = '$user_password'";
	
if (!($result = $db->sql_query($sql)))
{
	trigger_error('Could not obtain user password information', E_USER_WARNING);
}
if ( $row = $db->sql_fetchrow($result) )
{
	$i = 0;
	do
	{
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
		$template->assign_block_vars('clone_password', array(
			'ROW_CLASS'			=> $row_class,
			'U_LINK_PASSWORD'	=> append_sid("ucp.php?mode=viewprofile&amp;u=".$row['user_id']),
			'LINK_PASSWORD'		=> $row['username'],
		));
		$i++;
	}
	while ( $row = $db->sql_fetchrow($result) );
	$db->sql_freeresult($result);
}
else
{
	$template->assign_block_vars('not_clone_password', array());
}

if ( !empty($user_qq) )
{
	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_qq = '$user_qq'";
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not obtain user password information', E_USER_WARNING);
	}
	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
			$template->assign_block_vars('clone_qq', array(
				'ROW_CLASS'			=> $row_class,
				'U_LINK_QQ'			=> append_sid("ucp.php?mode=viewprofile&amp;u=".$row['user_id']),
				'LINK_QQ'			=> $row['username'],
			));
			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	}
	else
	{
		$template->assign_block_vars('not_clone_qq', array());
	}
}
else
{
	$template->assign_block_vars('not_write_qq', array());
}

if ( !empty($user_website) )
{
	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE user_website = '$user_website'";
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not obtain user password information', E_USER_WARNING);
	}
	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
			$template->assign_block_vars('clone_website', array(
				'ROW_CLASS'			=> $row_class,
				'U_LINK_WEBSITE'	=> append_sid("ucp.php?mode=viewprofile&amp;u=".$row['user_id']),
				'LINK_WEBSITE'		=> $row['username'],
			));
			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	}
	else 
	{
		$template->assign_block_vars('not_clone_website', array());
	}
}
else
{
	$template->assign_block_vars('not_write_website', array());
}
$template->assign_vars(array(
	'U_PROFILE' => append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $_GET[POST_USERS_URL]))
);
$template->pparse('body');
page_footer();

?>