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

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['会员']['删除'] = $filename;
	return;
}

define('IN_PHPBB', true); 
define('ROOT_PATH', './../');
require('./pagestart.php');

if (!isset($_GET['delete']))
{
	$users_per_page = 25;
	$start = get_pagination_start($users_per_page);

	if( isset($_POST['sort']) )
	{
		$sort_method = $_POST['sort'];
	}
	else if( isset($_GET['sort']) )
	{
		$sort_method = $_GET['sort'];
	}
	else
	{
		$sort_method = 'user_posts';
	}

	if( isset($_POST['order']) )
	{
		$sort_order = $_POST['order'];
	}
	else if( isset($_GET['order']) )
	{
		$sort_order = $_GET['order'];
	}
	else
	{
		$sort_order = 'DESC';
	}


	$template->set_filenames(array(
		'body' => 'admin/users_delete_body.tpl')
	);

	$sql = 'SELECT count(user_id) as total 
		FROM ' . USERS_TABLE . ' 
		WHERE user_id > 0';
	if(!$result = $db->sql_query($sql))
	{
		trigger_error("Could not count users", E_USER_WARNING);
	}
	
	$row = $db->sql_fetchrow($result);
	
	$total_users = $row['total'];

	$template->assign_vars(array(
		'U_LIST_ACTION' 		=> append_sid('admin_users_delete.php'),
		'U_DELETE_ACTION' 		=> append_sid('admin_users_delete.php?delete'),
		'ID_SELECTED' 			=> ($sort_method == 'user_id') ? 'selected="selected"' : '',
		'USERNAME_SELECTED' 	=> ($sort_method == 'username') ? 'selected="selected"' : '',
		'POSTS_SELECTED' 		=> ($sort_method == 'user_posts') ? 'selected="selected"' : '',
		'LASTVISIT_SELECTED' 	=> ($sort_method == 'user_lastvisit') ? 'selected="selected"' : '',
		'ASC_SELECTED' 			=> ($sort_order != 'DESC') ? 'selected="selected"' : '',
		'DESC_SELECTED' 		=> ($sort_order == 'DESC') ? 'selected="selected"' : '',
		'TOTAL_USERS' 			=> $total_users)
	);

	$sql = 'SELECT user_id, username, user_regdate, user_lastvisit, user_posts, user_active
		FROM ' . USERS_TABLE . "
		WHERE user_id > 0
		ORDER BY $sort_method $sort_order 
		LIMIT $start, $users_per_page";
		
	if(!$result = $db->sql_query($sql))
	{
		trigger_error("Could not query Users information", E_USER_WARNING);
	}

	while( $row = $db->sql_fetchrow($result) )
	{
		$userrow[] = $row;
	}

	for ($i = 0; $i < $users_per_page; $i++)
	{
		if (empty($userrow[$i]))
		{
			break;
		}

		$number	= $i + 1 + $start;
		
		$row_class = (($i % 2) == 0) ? 'row1' : 'row_2';
		
		$template->assign_block_vars('userrow', array(
			'L_NUMBER'		=> $number,
			'ROW_CLASS' 	=> $row_class,
			'NUMBER' 		=> $userrow[$i]['user_id'],
			'USERNAME' 		=> ( $userrow[$i]['user_active'] ) ? '<b>' . $userrow[$i]['username'] . '</b>' : $userrow[$i]['username'],
			'U_ADMIN_USER' 	=> append_sid("admin_users.php?mode=edit&amp;" . POST_USERS_URL . "=" . $userrow[$i]['user_id']),
			'JOINED'	 	=> create_date($userdata['user_dateformat'], $userrow[$i]['user_regdate'], $board_config['board_timezone']),
			'LAST_VISIT' 	=> (!$userrow[$i]['user_lastvisit']) ? '' : create_date($userdata['user_dateformat'], $userrow[$i]['user_lastvisit'], $board_config['board_timezone']),
			'POSTS' 		=> $userrow[$i]['user_posts']) 
		);
	} 

	$template->assign_vars(array(
		'PAGINATION' => generate_pagination(append_sid("admin_users_delete.php?sort=$sort_method&amp;order=$sort_order"), $total_users, $users_per_page, $start)) 
	);

	$template->pparse('body');

	page_footer();

}
else
{
	if ( isset($_POST['user_id_list']) )
	{
		$users = $_POST['user_id_list'];
	}
	else 
	{
		trigger_error('您没有选择用户', E_USER_ERROR);
	}

	for($i = 0; $i < count($users); $i++)
	{
		
		$user_id = intval($users[$i]);
		
		if( $userdata['user_id'] == $user_id )
		{
			trigger_error('您不能删除你自己！', E_USER_ERROR);
		}
		
		$this_userdata = get_userdata($user_id);

		$sql = "SELECT g.group_id 
			FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g  
			WHERE ug.user_id = $user_id 
				AND g.group_id = ug.group_id 
				AND g.group_single_user = 1";
		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法获得这个用户的用户组信息', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		$sql = "UPDATE " . POSTS_TABLE . "
			SET poster_id = " . DELETED . ", post_username = '" . str_replace("\\'", "''", addslashes($this_userdata['username'])) . "' 
			WHERE poster_id = $user_id";
			
		if( !$db->sql_query($sql) )	
		{
			trigger_error('无法更新这个用户的帖子信息', E_USER_WARNING);
		}

		$sql = "UPDATE " . TOPICS_TABLE . "
			SET topic_poster = " . DELETED . " 
			WHERE topic_poster = $user_id";
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法更新这个用户的主题', E_USER_WARNING);
		}

		$sql = "UPDATE " . VOTE_USERS_TABLE . "
			SET vote_user_id = " . DELETED . "
			WHERE vote_user_id = $user_id";
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法更新这个用户的投票记录', E_USER_WARNING);
		}

		$sql = "UPDATE " . GROUPS_TABLE . "
			SET group_moderator = " . $userdata['user_id'] . "
			WHERE group_moderator = $user_id";
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法更新用户组的版主', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . USERS_TABLE . "
			WHERE user_id = $user_id";
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . USER_GROUP_TABLE . "
			WHERE user_id = $user_id";
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法从 user_group 表中删除此用户的记录', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . GROUPS_TABLE . "
			WHERE group_id = " . $row['group_id'];
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户在 groups 表的数据', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
			WHERE group_id = " . $row['group_id'];
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户的群组数据', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
			WHERE user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户在 topic_watch 表中的数据', E_USER_WARNING);
		}
			
		$sql = "DELETE FROM " . BANLIST_TABLE . "
			WHERE ban_userid = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户的黑名单数据', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . SESSIONS_TABLE . "
			WHERE session_user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户的session数据', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . SESSIONS_KEYS_TABLE . "
			WHERE user_id = $user_id";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除此用户的session key数据', E_USER_WARNING);
		}

		$sql = "SELECT privmsgs_id
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_from_userid = $user_id 
				OR privmsgs_to_userid = $user_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法删除此用户的私人信息', E_USER_WARNING);
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
				trigger_error('无法删除此用户的私人信息', E_USER_WARNING);
			}
		
			if ( !$db->sql_query($delete_text_sql) )
			{
				trigger_error('无法删除此用户的私人信息', E_USER_WARNING);
			}
		}
	}
	trigger_error('用户已成功删除', E_USER_ERROR);
}

?>