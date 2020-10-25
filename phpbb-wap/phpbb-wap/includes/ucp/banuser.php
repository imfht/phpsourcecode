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

if ($userdata['user_level'] == USER || $userdata['user_level'] == ANONYMOUS)
{
	trigger_error('您没有权限操作', E_USER_ERROR);
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('您没有选择用户', E_USER_ERROR);
}

$user = intval($_GET[POST_USERS_URL]);

if (!$banuser = get_userdata($user))
{
	trigger_error('无法取得用户数据！', E_USER_ERROR);
}

$sql = 'SELECT ban_userid 
	FROM ' . BANLIST_TABLE . '
	WHERE ban_userid = ' . $user;

if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法查询用户的黑名单信息', E_USER_WARNING);
}

if ($userdata['user_level'] == MOD && $userdata['user_level'] >= $banuser['user_level'])
{
	trigger_error('版主只能对普通用户进行设置黑名单', E_USER_ERROR);
}

if($db->sql_numrows($result))
{
	if ( isset($_POST['cancel']) )
	{
		redirect(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $user, true));
	}

	$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

	if( !$confirm )
	{
		$page_title = '解除黑名单';

		page_header($page_title);
		
		$template->set_filenames(array(
			'confirm' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '黑名单',
			'MESSAGE_TEXT'		=> '请确认是否解除 ' . $banuser['username'] . ' 的黑名单？',
			'L_YES' 			=> '是',
			'L_NO' 				=> '否',
			'S_CONFIRM_ACTION' 	=> append_sid('ucp.php?mode=ban&' . POST_USERS_URL . '=' . $user))
		);

		$template->pparse('confirm');

		page_footer();
	}

	$sql = 'DELETE FROM ' . BANLIST_TABLE . ' 
		WHERE ban_userid = ' . $user;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法删除黑名单数据', E_USER_WARNING);
	}

	$message = '已解除 ' . $banuser['username'] . ' 的黑名单<br /> 点击 <a href="' . append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $user) . '">这里</a> 返回 ' . $banuser['username'] . ' 的个人中心<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';
	trigger_error($message);
}
else
{
	if ( isset($_POST['cancel']) )
	{
		redirect(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $user, true));
	}

	$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

	if( !$confirm )
	{
		$page_title = '设置黑名单';

		page_header($page_title);
		
		$template->set_filenames(array(
			'confirm' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '黑名单',
			'MESSAGE_TEXT'		=> '请确认是把 ' . $banuser['username'] . ' 列为黑名单？',
			'L_YES' 			=> '是',
			'L_NO' 				=> '否',
			'S_CONFIRM_ACTION' 	=> append_sid('ucp.php?mode=ban&' . POST_USERS_URL . '=' . $user))
		);

		$template->pparse('confirm');

		page_footer();
	}

	$sql = 'INSERT INTO ' . BANLIST_TABLE . ' (ban_userid) 
		VALUES (' . $user . ')';

	if (!$db->sql_query($sql))
	{
		trigger_error('无法插入黑名单数据', E_USER_WARNING);
	}

	$message = $banuser['username'] . ' 已被列为网站黑名单<br /> 点击 <a href="' . append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $user) . '">这里</a> 返回 ' . $banuser['username'] . ' 的个人中心<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';
	trigger_error($message);
}

?>