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
	die("Hacking attempt");
	exit;
}
if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('对不起，用户不存在', E_USER_ERROR);
}
	
if ( $userdata['user_level'] == ADMIN )
{

	$confirm = isset($_POST['confirm']) ? true : false;
	$user_id = intval($_GET[POST_USERS_URL]);

	if (!($this_userdata = get_userdata($user_id)))
	{
		trigger_error('对不起，用户不存在', E_USER_ERROR);
	}
	if ( $this_userdata['user_level'] != USER )
	{
		trigger_error('您不能删除管理员！', E_USER_ERROR);
	}
	if( $userdata['user_id'] == $user_id )
	{
		trigger_error('您不能删除您自己！', E_USER_ERROR);
	}

	if ( isset($_POST['cancel']) )
	{
		redirect(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $_GET[POST_USERS_URL], true));
	}
		
	if ( $confirm )
	{
		
		$sql = "SELECT g.group_id 
			FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g  
			WHERE ug.user_id = $user_id 
				AND g.group_id = ug.group_id 
				AND g.group_single_user = 1";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain group information for this user', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		$sql = "UPDATE " . POSTS_TABLE . "
			SET poster_id = " . DELETED . ", post_username = '" . str_replace("\\'", "''", addslashes($this_userdata['username'])) . "' 
			WHERE poster_id = $user_id";
		if( !$db->sql_query($sql) )	
		{
			trigger_error('Could not update posts for this user', E_USER_WARNING);
			}

		$sql = "UPDATE " . TOPICS_TABLE . "
			SET topic_poster = " . DELETED . " 
			WHERE topic_poster = $user_id";
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not update topics for this user', E_USER_WARNING);
		}

		$sql = "UPDATE " . VOTE_USERS_TABLE . "
			SET vote_user_id = " . DELETED . "
			WHERE vote_user_id = $user_id";
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not update votes for this user', E_USER_WARNING);
		}

		$sql = "UPDATE " . GROUPS_TABLE . "
			SET group_moderator = " . $userdata['user_id'] . "
			WHERE group_moderator = $user_id";
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not update group moderators', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . USERS_TABLE . "
			WHERE user_id = $user_id";
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete user', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . USER_GROUP_TABLE . "
			WHERE user_id = $user_id";
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete user from user_group table', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . GROUPS_TABLE . "
			WHERE group_id = " . $row['group_id'];
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete group for this user', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
			WHERE group_id = " . $row['group_id'];
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete group for this user', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
			WHERE user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete user from topic watch table', E_USER_WARNING);
		}
				
		$sql = "DELETE FROM " . BANLIST_TABLE . "
			WHERE ban_userid = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete user from banlist table', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . SESSIONS_TABLE . "
			WHERE session_user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete sessions for this user', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . SESSIONS_KEYS_TABLE . "
			WHERE user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not delete auto-login keys for this user', E_USER_WARNING);
		}

		$sql = "SELECT privmsgs_id
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_from_userid = $user_id 
				OR privmsgs_to_userid = $user_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not select all users private messages', E_USER_WARNING);
		}

		while ( $row_privmsgs = $db->sql_fetchrow($result) )
		{
			$mark_list[] = $row_privmsgs['privmsgs_id'];
		}
			
		if ( count($mark_list) )
		{
			$delete_sql_id = implode(', ', $mark_list);

			$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
				WHERE privmsgs_text_id IN ($delete_sql_id)";
			$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_id IN ($delete_sql_id)";
		
			if ( !$db->sql_query($delete_sql) )
			{
				trigger_error('Could not delete private message info', E_USER_WARNING);
			}
			
			if ( !$db->sql_query($delete_text_sql) )
			{
				trigger_error('Could not delete private message text', E_USER_WARNING);
			}
		}

		trigger_error('删除失败！', E_USER_ERROR);

	}
	else
	{

		page_header($page_title);

		$template->set_filenames(array(
			'confirm_body' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '确认',
			'MESSAGE_TEXT' 		=> '请问是否删除该用户？',

			'L_YES' 			=> '是',
			'L_NO' 				=> '否',

			'S_CONFIRM_ACTION' 	=> append_sid("ucp.php?mode=delete&amp;u=$user_id"))
		);

		$template->pparse('confirm_body');

		page_footer();

	}

}
else
{
	trigger_error('您没有权限删除管理员', E_USER_ERROR);
}

?>