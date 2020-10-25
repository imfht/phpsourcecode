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

define('IN_PHPBB', true);
define('ROOT_PATH', './');
include(ROOT_PATH . 'common.php');

if ( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
	$forum_id = ( isset($_GET[POST_FORUM_URL]) ) ? intval($_GET[POST_FORUM_URL]) : intval($_POST[POST_FORUM_URL]);
}
else if ( isset($_GET['forum']))
{
	$forum_id = intval($_GET['forum']);
}
else
{
	$forum_id = '';
}

if ( !empty($forum_id) )
{
	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = $forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain forums information', E_USER_WARNING);
	}
}
else
{
	trigger_error('论坛不存在', E_USER_ERROR);
}

if ( !($forum_row = $db->sql_fetchrow($result)) )
{
	trigger_error('论坛不存在', E_USER_ERROR);
}

$userdata = $session->start($user_ip, $forum_id);
init_userprefs($userdata);

page_header($forum_row['forum_name'] . '_版务');

// 获取版主列表
$sql = "SELECT u.user_id, u.username, u.user_sig, u.user_nic_color 
	FROM " . AUTH_ACCESS_TABLE . " aa, " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g, " . USERS_TABLE . " u
	WHERE aa.forum_id = $forum_id 
		AND aa.auth_mod = " . TRUE . " 
		AND g.group_single_user = 1
		AND ug.group_id = aa.group_id 
		AND g.group_id = aa.group_id 
		AND u.user_id = ug.user_id 
	GROUP BY u.user_id, u.username  
	ORDER BY u.user_id";
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not query forum moderator information', E_USER_WARNING);
}

// 小组里的版主
/*
$sql = "SELECT g.group_id, g.group_name 
	FROM " . AUTH_ACCESS_TABLE . " aa, " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g 
	WHERE aa.forum_id = $forum_id
		AND aa.auth_mod = " . TRUE . " 
		AND g.group_single_user = 0
		AND g.group_type <> ". GROUP_HIDDEN ."
		AND ug.group_id = aa.group_id 
		AND g.group_id = aa.group_id 
	GROUP BY g.group_id, g.group_name  
	ORDER BY g.group_id";
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not query forum moderator information', E_USER_WARNING);
}

while( $row = $db->sql_fetchrow($result) )
{
	$moderators[] = $row['group_name'];
}
*/

$i = 0;
while( $row = $db->sql_fetchrow($result) )
{
	if (empty($row['user_sig']))
	{
		$signture = '这版主也太懒了，连个签名也懒得设置';
	}
	else
	{
		$signture = $row['user_sig'];
	}
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('moderators', array(
		'ROW_CLASS'		=> $row_class,
		'IMG_MOD'		=> make_style_image('level_mod', '版主', '版主'),
		'USERNAME'		=> $row['username'],
		'USERNIC_COLOR'	=> $row['user_nic_color'],
		'SIGNTURE'		=> $signture,
		'U_UCP'			=> append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['user_id']))
	);
	$i++;
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_moderator', array());
}

$template->assign_vars(array(
	'FORUM_NAME' 		=> $forum_row['forum_name'],
	'U_MANAGE_MODULE'	=> append_sid(ROOT_PATH . 'bbs/manage_module.php?' . POST_FORUM_URL . '=' .$forum_id),
	'U_FORUMCLASS'		=> append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id),
	'U_BACK'			=> append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id))
);

$template->set_filenames(array(
	'body' => 'forumcp_body.tpl')
);

$template->pparse('body');

page_footer();

?>