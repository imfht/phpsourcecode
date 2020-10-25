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

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['小组']['小组'] = $filename;

	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

if ( isset($_POST[POST_GROUPS_URL]) || isset($_GET[POST_GROUPS_URL]) )
{
	$group_id = ( isset($_POST[POST_GROUPS_URL]) ) ? intval($_POST[POST_GROUPS_URL]) : intval($_GET[POST_GROUPS_URL]);
}
else
{
	$group_id = 0;
}

if ( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

if ( isset($_POST['edit']) || isset($_POST['new']) )
{
	$template->set_filenames(array(
		'body' => 'admin/group_edit_body.tpl')
	);

	if ( isset($_POST['edit']) )
	{

		$sql = 'SELECT *
			FROM ' . GROUPS_TABLE . '
			WHERE group_single_user <> ' . TRUE . '
			AND group_id = ' . $group_id;
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Error getting group information', E_USER_WARNING);
		}

		if ( !($group_info = $db->sql_fetchrow($result)) )
		{
			trigger_error('不存在的小组', E_USER_ERROR);
		}

		$mode = 'editgroup';
		$template->assign_block_vars('group_edit', array());

	}
	else if ( isset($_POST['new']) )
	{
		$group_info = array (
			'group_name' 		=> '',
			'group_description' => '',
			'group_moderator' 	=> '',
			'group_type' 		=> GROUP_OPEN,
			'guestbook_enable'	=> '',
			'group_logo'		=> '');
		$group_open = ' checked="checked"';
		$group_guestbook = 'checked="checked"';

		$mode = 'newgroup';

	}

	if ($group_info['group_moderator'] != '')
	{
		$sql = 'SELECT user_id, username
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . $group_info['group_moderator'];
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain user info for moderator list', E_USER_WARNING);
		}

		if ( !($row = $db->sql_fetchrow($result)) )
		{
			trigger_error('Could not obtain user info for moderator list', E_USER_WARNING);
		}

		$group_moderator = $row['username'];
	}
	else
	{
		$group_moderator = '';
	}

	$group_open 		= ( $group_info['group_type'] == GROUP_OPEN ) ? ' checked="checked"' : '';
	$group_closed 		= ( $group_info['group_type'] == GROUP_CLOSED ) ? ' checked="checked"' : '';
	$group_hidden 		= ( $group_info['group_type'] == GROUP_HIDDEN ) ? ' checked="checked"' : '';
	$group_guestbook 	= ( $group_info['guestbook_enable'] == 1 ) ? 'checked="checked"' : '';

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';

	$template->assign_vars(array(
		'L_TITLE' 						=> ( isset($_POST['new']) ) ? '新建小组' : '编辑小组', 
		'GROUP_NAME' 					=> $group_info['group_name'],
		'GROUP_DESCRIPTION' 			=> $group_info['group_description'], 
		'GROUP_LOGO'					=> $group_info['group_logo'],
		'GROUP_MODERATOR' 				=> $group_moderator, 

		'U_ADMIN_GROUPS'				=> append_sid('admin_groups.php'),
		'U_SEARCH_USER' 				=> append_sid('../search.php?mode=searchuser'), 

		'S_GROUP_OPEN_TYPE' 			=> GROUP_OPEN,
		'S_GROUP_CLOSED_TYPE' 			=> GROUP_CLOSED,
		'S_GROUP_HIDDEN_TYPE' 			=> GROUP_HIDDEN,
		'S_GROUP_OPEN_CHECKED' 			=> $group_open,
		'S_GROUP_CLOSED_CHECKED' 		=> $group_closed,
		'S_GROUP_HIDDEN_CHECKED' 		=> $group_hidden,
		'S_GROUP_GB_ENABLE' 			=> $group_guestbook,
		'S_GROUP_ACTION' 				=> append_sid('admin_groups.php'),
		'S_HIDDEN_FIELDS' 				=> $s_hidden_fields)
	);

	$template->pparse('body');

}
else if ( isset($_POST['group_update']) )
{
	if ( isset($_POST['group_delete']) )
	{
		$sql = 'SELECT auth_mod 
			FROM ' . AUTH_ACCESS_TABLE . ' 
			WHERE group_id = ' . $group_id;
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not select auth_access', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);
		if (intval($row['auth_mod']) == 1)
		{
			$sql = 'SELECT user_id 
				FROM ' . USER_GROUP_TABLE . '
				WHERE group_id = ' . $group_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not select user_group', E_USER_WARNING);
			}

			$rows = $db->sql_fetchrowset($result);
			for ($i = 0; $i < count($rows); $i++)
			{
				$sql = 'SELECT g.group_id 
					FROM ' . AUTH_ACCESS_TABLE . ' a, ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
					WHERE (a.auth_mod = 1) 
						AND (g.group_id = a.group_id) 
						AND (a.group_id = ug.group_id) 
						AND (g.group_id = ug.group_id) 
						AND (ug.user_id = ' . intval($rows[$i]['user_id']) . ') 
						AND (ug.group_id <> ' . $group_id . ')';
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not obtain moderator permissions', E_USER_WARNING);
				}

				if ($db->sql_numrows($result) == 0)
				{
					$sql = 'UPDATE ' . USERS_TABLE . ' 
						SET user_level = ' . USER . ' 
						WHERE user_level = ' . MOD . ' AND user_id = ' . intval($rows[$i]['user_id']);
					
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update moderator permissions', E_USER_WARNING);
					}
				}
			}
		}

		$sql = 'DELETE FROM ' . GROUPS_TABLE . '
			WHERE group_id = ' . $group_id;
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not update group', E_USER_WARNING);
		}

		$sql = 'DELETE FROM ' . USER_GROUP_TABLE . '
			WHERE group_id = ' . $group_id;
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not update user_group', E_USER_WARNING);
		}

		$sql = 'DELETE FROM ' . AUTH_ACCESS_TABLE . '
			WHERE group_id = ' . $group_id;
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not update auth_access', E_USER_WARNING);
		}

		$message = '删除成功<br />点击 <a href="' . append_sid('admin_groups.php') . '">这里</a> 返回小组列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

		trigger_error($message);
	}
	else
	{
		$group_type 			= isset($_POST['group_type']) ? intval($_POST['group_type']) : GROUP_OPEN;
		$group_guestbook 		= (isset($_POST['group_guestbook'])) ? ($_POST['group_guestbook'] == 0) ? 0 : 1 : 1;
		$group_name 			= isset($_POST['group_name']) ? htmlspecialchars(trim($_POST['group_name'])) : '';
		$group_description 		= isset($_POST['group_description']) ? trim($_POST['group_description']) : '';
		$group_moderator 		= isset($_POST['username']) ? $_POST['username'] : '';
		$delete_old_moderator 	= isset($_POST['delete_old_moderator']) ? true : false;
		$group_logo 			= isset($_POST['group_logo']) ? htmlspecialchars(trim($_POST['group_logo'])) : '';

		if ( $group_name == '' )
		{
			trigger_error('小组的名称不能为空', E_USER_ERROR);
		}
		else if ( $group_moderator == '' )
		{
			trigger_error('小组不能没有版主', E_USER_ERROR);
		}
		
		$this_userdata = get_userdata($group_moderator, true);
		$group_moderator = $this_userdata['user_id'];

		if ( !$group_moderator )
		{
			trigger_error('你设定的版主不是本站会员', E_USER_ERROR);
		}
				
		if( $mode == 'editgroup' )
		{
			$sql = 'SELECT *
				FROM ' . GROUPS_TABLE . '
				WHERE group_single_user <> ' . TRUE . '
				AND group_id = ' . $group_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Error getting group information', E_USER_WARNING);
			}

			if( !($group_info = $db->sql_fetchrow($result)) )
			{
				trigger_error('小组不存在', E_USER_ERROR);
			}
		
			if ( $group_info['group_moderator'] != $group_moderator )
			{
				if ( $delete_old_moderator )
				{
					$sql = 'DELETE FROM ' . USER_GROUP_TABLE . '
						WHERE user_id = ' . $group_info['group_moderator'] . ' 
							AND group_id = ' . $group_id;
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update group moderator', E_USER_WARNING);
					}
				}

				$sql = 'SELECT user_id 
					FROM ' . USER_GROUP_TABLE . " 
					WHERE user_id = $group_moderator 
						AND group_id = $group_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Failed to obtain current group moderator info', E_USER_WARNING);
				}

				if ( !($row = $db->sql_fetchrow($result)) )
				{
					$sql = 'INSERT INTO ' . USER_GROUP_TABLE . ' (group_id, user_id, user_pending)
						VALUES (' . $group_id . ', ' . $group_moderator . ', 0)';
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update group moderator', E_USER_WARNING);
					}
				}
			}

			$sql = 'UPDATE ' . GROUPS_TABLE . "
				SET group_type = $group_type, group_name = '" . $db->sql_escape($group_name) . "', group_description = '" . $db->sql_escape($group_description) . "', group_moderator = $group_moderator, guestbook_enable = $group_guestbook, group_logo = '" . $db->sql_escape($group_logo) . "' 
				WHERE group_id = $group_id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not update group', E_USER_WARNING);
			}
	
			$message = '小组信息成功更新<br />点击 <a href="' . append_sid('admin_groups.php') . '">这里</a> 返回小组列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
		}
		else if( $mode == 'newgroup' )
		{
			$sql = 'INSERT INTO ' . GROUPS_TABLE . " (group_type, group_name, group_description, group_moderator, group_single_user, guestbook_enable, group_logo) 
				VALUES ($group_type, '" . $db->sql_escape($group_name) . "', '" . $db->sql_escape($group_description) . "', $group_moderator, '0', $group_guestbook, '" . $db->sql_escape($group_logo) . "')";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not insert new group', E_USER_WARNING);
			}
			$new_group_id = $db->sql_nextid();

			$sql = 'INSERT INTO ' . USER_GROUP_TABLE . " (group_id, user_id, user_pending)
				VALUES ($new_group_id, $group_moderator, 0)";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not insert new user-group info', E_USER_WARNING);
			}
			
			$message = '创建成功<br />点击 <a href="' . append_sid('admin_groups.php') . '">这里</a> 返回小组列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);

		}
		else
		{
			trigger_error('请指定小组的选项', E_USER_ERROR);
		}
	}
}
else
{
	$sql = 'SELECT group_id, group_name
		FROM ' . GROUPS_TABLE . '
		WHERE group_single_user <> ' . TRUE . '
		ORDER BY group_name';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain group list', E_USER_WARNING);
	}

	$select_list = '';
	if ( $row = $db->sql_fetchrow($result) )
	{
		$select_list .= '<select name="' . POST_GROUPS_URL . '">';
		do
		{
			$select_list .= '<option value="' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
		}
		while ( $row = $db->sql_fetchrow($result) );
		$select_list .= '</select>';
	}

	$template->set_filenames(array(
		'body' => 'admin/group_select_body.tpl')
	);

	$template->assign_vars(array(
		'S_GROUP_ACTION' 		=> append_sid('admin_groups.php'),
		'S_GROUP_SELECT' 		=> $select_list)
	);

	if ( $select_list != '' )
	{
		$template->assign_block_vars('select_box', array());
	}

	$template->pparse('body');
}

page_footer();

?>