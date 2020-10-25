<?php
/**
* @package phpBB-WAP
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

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('您选择的是游客或用户不存在', E_USER_ERROR);
}

if (!$profiledata = get_userdata($_GET[POST_USERS_URL]))
{
	trigger_error('无法取得用户数据！', E_USER_ERROR);
}

if ( $userdata['user_id'] != $profiledata['user_id'] )
{
	if ($userdata['user_level'] != ADMIN )
	{
		trigger_error('您没有权限管理该用户!<br />点击 <a href="' . append_sid(ROOT_PATH . 'ucp.php?mode=manage&' . POST_USERS_URL . '=' . $userdata['user_id']) . '">这里</a> 进入我的地盘管理', E_USER_ERROR);
	}
}

$sql = 'SELECT count(link_id) AS link_total
	FROM ' . LINKS_TABLE . '
	WHERE link_admin_user = ' . $profiledata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计友链数据', E_USER_WARNING);
}

$links = $db->sql_fetchrow($result);

if ($links['link_total'])
{
	$template->assign_block_vars('links',array(
		'LINKS_TOTAL' => $links['link_total'],
		'U_MANAGE' => append_sid('links.php?mode=manage'))
	);
}

if( ( $userdata['user_level'] == ADMIN ) )
{
	$template->assign_block_vars('admin', array());
	
	$user_clone 	= '<a href="' . append_sid('ucp.php?mode=clone&amp;u=' . $profiledata['user_id']) . '">相似的用户</a>';
	
	$user_ban		= '<a href="' . append_sid('ucp.php?mode=ban&amp;'. POST_USERS_URL. '='. $profiledata['user_id']) . '">黑名单管理</a>';
	
	$user_edit 		= '<a href="' . append_sid('admin/admin_users.php?mode=edit&amp;'. POST_USERS_URL. '='. $profiledata['user_id']. '&amp;sid='. $userdata['session_id']) . '">管理用户的信息</a>';

	if ($profiledata['user_level'] == USER)
	{
		$user_delete = '<a href="' . append_sid('ucp.php?mode=delete&amp;u=' . $profiledata['user_id']) . '">删除该用户</a>';
		$template->assign_block_vars('delete', array());


		if ($profiledata['user_active'] == 1)
		{
			$template->assign_block_vars('lock', array());
			$link_lock = '<a href="' . append_sid('ucp.php?mode=lock&amp;u=' . $profiledata['user_id']) . '">停用该用户</a>';
		}
		elseif ($profiledata['user_active'] == 0)
		{
			$template->assign_block_vars('lock', array());
			$link_lock = '【<a href="' . append_sid('ucp.php?mode=lock&amp;u=' . $profiledata['user_id']) . '">启用该账号</a>】';
		}
	}
	else
	{
		$link_lock = '';
		$user_delete = '';
	}

	$admin_link = '<a href="admin/index.php?sid=' . $userdata['session_id'] . '">进入超级管理员面板</a>';
	$template->assign_vars(array(
		'ADMIN_LINK' => $admin_link,
		'LINK_LOOK'  => $link_lock,
		'DELETE_USER' => $user_delete,
		'CLONE_USER' => $user_clone,
		'BAN_USER' => $user_ban,
		'EDIT_USER' => $user_edit,
		'USER_EDIT' => $user_edit,
		)
	);
}

page_header('管理我的地盘');

$template->set_filenames(array(
	'body' => 'ucp/ucp_manage.tpl')
);

$template->assign_vars(array(
	'U_ALBUM'				=> append_sid('album.php'),
	'U_MAIN_ADMIN'			=> append_sid('ucp.php?admin&mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_FRIENDS'				=> append_sid('ucp.php?mode=friends&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_EDITPROFILE' 		=> append_sid('ucp.php?mode=editprofile'),
	'U_EDITCONFIG' 			=> append_sid('ucp.php?mode=editconfig'),
	'U_EDITPROFILEINFO' 	=> append_sid('ucp.php?mode=editprofileinfo'),
	'U_GUESTBOOK'			=> append_sid('ucp.php?mode=guestbook&amp;' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_UCP_MAIN'			=> append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_VIEWPROFILE'			=> append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_UCP_MANAGE'			=> append_sid('ucp.php?mode=manage&' . POST_USERS_URL . '=' . $profiledata['user_id']))
);

$template->pparse('body');

page_footer();
?>