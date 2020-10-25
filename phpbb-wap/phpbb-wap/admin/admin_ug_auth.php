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
	$module['会员']['权限'] = $filename . '?mode=user';
	$module['小组']['权限'] = $filename . '?mode=group';
	return;
}

define('IN_PHPBB', true);
$no_page_header = true;

define('ROOT_PATH', './../');
require('./pagestart.php');

$params = array(
	'mode' 		=> 'mode',
	'user_id' 	=> POST_USERS_URL,
	'group_id' 	=> POST_GROUPS_URL,
	'adv' 		=> 'adv');

foreach($params as $var => $param)
{
	if ( !empty($_POST[$param]) || !empty($_GET[$param]) )
	{
		$$var = ( !empty($_POST[$param]) ) ? $_POST[$param] : $_GET[$param];
	}
	else
	{
		$$var = '';
	}
}

$user_id 	= intval($user_id);
$group_id 	= intval($group_id);
$adv 		= intval($adv);
$mode 		= htmlspecialchars($mode);

$forum_auth_fields = array(
	'auth_view', 'auth_read', 'auth_post', 'auth_reply', 'auth_edit',
	'auth_delete', 'auth_sticky', 'auth_announce', 'auth_vote',
	'auth_pollcreate', 'auth_attachments', 'auth_download'
);

$auth_field_match = array(
	'auth_view' 		=> AUTH_VIEW,
	'auth_read' 		=> AUTH_READ,
	'auth_post' 		=> AUTH_POST,
	'auth_reply' 		=> AUTH_REPLY,
	'auth_edit' 		=> AUTH_EDIT,
	'auth_delete' 		=> AUTH_DELETE,
	'auth_sticky' 		=> AUTH_STICKY,
	'auth_announce' 	=> AUTH_ANNOUNCE, 
	'auth_vote' 		=> AUTH_VOTE, 
	'auth_pollcreate' 	=> AUTH_POLLCREATE,
	'auth_attachments'	=> AUTH_ATTACH,
	'auth_download'		=> AUTH_DOWNLOAD
);

$field_names = array(
	'auth_view' 		=> '浏览权限',
	'auth_read' 		=> '阅读权限',
	'auth_post' 		=> '发表权限',
	'auth_reply' 		=> '回复权限',
	'auth_edit' 		=> '编辑权限',
	'auth_delete' 		=> '删除权限',
	'auth_sticky' 		=> '置顶权限',
	'auth_announce'		=> '公告权限', 
	'auth_vote' 		=> '投票权限', 
	'auth_pollcreate' 	=> '创建投票',
	'auth_attachments' 	=> '上传附件',
	'auth_download' 	=> '下载附件'
);

function check_auth($type, $key, $u_access, $is_admin)
{
	$auth_user = 0;

	if( count($u_access) )
	{
		for($j = 0; $j < count($u_access); $j++)
		{
			$result = 0;
			switch($type)
			{
				case AUTH_ACL:
					$result = $u_access[$j][$key];

				case AUTH_MOD:
					$result = $result || $u_access[$j]['auth_mod'];

				case AUTH_ADMIN:
					$result = $result || $is_admin;
					break;
			}

			$auth_user = $auth_user || $result;
		}
	}
	else
	{
		$auth_user = $is_admin;
	}

	return $auth_user;
}

// 内容已提交、和user模式user_id存在 或 小组
if ( isset($_POST['submit']) && ( ( $mode == 'user' && $user_id ) || ( $mode == 'group' && $group_id ) ) )
{
	$user_level = '';
	
	if ( $mode == 'user' )
	{
		$sql = 'SELECT g.group_id, u.user_level
			FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u, ' . GROUPS_TABLE . ' g
			WHERE u.user_id = ' . $user_id . ' 
				AND ug.user_id = u.user_id 
				AND g.group_id = ug.group_id 
				AND g.group_single_user = ' . true;
				
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not select info from user/user_group table', E_USER_WARNING);
		}

		$row 		= $db->sql_fetchrow($result);
		$group_id 	= $row['group_id'];
		$user_level	= $row['user_level'];
		$db->sql_freeresult($result);
	}

	if ( $mode == 'user' && $_POST['userlevel'] == 'admin' && $user_level != ADMIN )
	{

		if ( $userdata['user_id'] != $user_id )
		{
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_level = ' . ADMIN . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update user level', E_USER_WARNING);
			}

			$sql = 'DELETE FROM ' . AUTH_ACCESS_TABLE . "
				WHERE group_id = $group_id 
					AND auth_mod = 0";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t delete auth access info', E_USER_WARNING);
			}

			$sql = 'UPDATE ' . AUTH_ACCESS_TABLE . "
				SET auth_view = 0, auth_read = 0, auth_post = 0, auth_reply = 0, auth_edit = 0, auth_delete = 0, auth_sticky = 0, auth_announce = 0
				WHERE group_id = $group_id"; 
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t update auth access', E_USER_WARNING);
			}
		}

		$message = '用户权限设定已经更新<br />点击 <a href="' . append_sid('admin_ug_auth.php?mode=' . $mode) . '">这里</a> 返回上一页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
		trigger_error($message);
	}
	else
	{
		if ( $mode == 'user' && $_POST['userlevel'] == 'user' && $user_level == ADMIN )
		{

			if ( $userdata['user_id'] != $user_id )
			{
				$sql = 'UPDATE ' . AUTH_ACCESS_TABLE . "
					SET auth_view = 0, auth_read = 0, auth_post = 0, auth_reply = 0, auth_edit = 0, auth_delete = 0, auth_sticky = 0, auth_announce = 0
					WHERE group_id = $group_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not update auth access', E_USER_WARNING);
				}

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_level = ' . USER . "
					WHERE user_id = $user_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not update user level', E_USER_WARNING);
				}
			}

			$message = '权限设定已经更新<br />点击 <a href="' . append_sid('admin_ug_auth.php?mode=' . $mode) . '">这里</a> 返回上一页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
		}
		else
		{
	
			$change_mod_list = ( isset($_POST['moderator']) ) ? $_POST['moderator'] : array();

			if ( empty($adv) )
			{
				$sql = 'SELECT f.* 
					FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . ' c
					WHERE f.cat_id = c.cat_id
					ORDER BY c.cat_order, f.forum_order ASC';
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t obtain forum information', E_USER_WARNING);
				}

				$forum_access = $forum_auth_level_fields = array();
				while( $row = $db->sql_fetchrow($result) )
				{
					$forum_access[] = $row;
				}
				$db->sql_freeresult($result);

				for($i = 0; $i < count($forum_access); $i++)
				{
					$forum_id = $forum_access[$i]['forum_id'];

					for($j = 0; $j < count($forum_auth_fields); $j++)
					{
						$forum_auth_level_fields[$forum_id][$forum_auth_fields[$j]] = $forum_access[$i][$forum_auth_fields[$j]] == AUTH_ACL;
					}
				}

				foreach($_POST['private'] as $forum_id => $value)
				{
					foreach($forum_auth_level_fields[$forum_id] as $auth_field => $exists)
					{
						if ($exists)
						{
							$change_acl_list[$forum_id][$auth_field] = $value;
						}
					}
				}
			}
			else
			{
				$change_acl_list = array();
				for($j = 0; $j < count($forum_auth_fields); $j++)
				{
					$auth_field = $forum_auth_fields[$j];

					foreach($_POST['private_' . $auth_field] as $forum_id => $value)
					{
						$change_acl_list[$forum_id][$auth_field] = $value;
					}
				}
			}

			$sql = 'SELECT f.* 
				FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . ' c
				WHERE f.cat_id = c.cat_id
				ORDER BY c.cat_order, f.forum_order';
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t obtain forum information', E_USER_WARNING);
			}

			$forum_access = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$forum_access[] = $row;
			}
			$db->sql_freeresult($result);

			$sql = ( $mode == 'user' ) ? 'SELECT aa.* FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . ' ug, ' . GROUPS_TABLE. " g WHERE ug.user_id = $user_id AND g.group_id = ug.group_id AND aa.group_id = ug.group_id AND g.group_single_user = " . TRUE : 'SELECT * FROM ' . AUTH_ACCESS_TABLE . " WHERE group_id = $group_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t obtain user/group permissions', E_USER_WARNING);
			}

			$auth_access = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$auth_access[$row['forum_id']] = $row;
			}
			$db->sql_freeresult($result);

			$forum_auth_action = array();
			$update_acl_status = array();
			$update_mod_status = array();

			for($i = 0; $i < count($forum_access); $i++)
			{
				$forum_id = $forum_access[$i]['forum_id'];

				if ( 
					( isset($auth_access[$forum_id]['auth_mod']) && $change_mod_list[$forum_id] != $auth_access[$forum_id]['auth_mod'] ) || 
					( !isset($auth_access[$forum_id]['auth_mod']) && !empty($change_mod_list[$forum_id]) ) 
				)
				{
					$update_mod_status[$forum_id] = $change_mod_list[$forum_id];

					if ( !$update_mod_status[$forum_id] )
					{
						$forum_auth_action[$forum_id] = 'delete';
					}
					else if ( !isset($auth_access[$forum_id]['auth_mod']) )
					{
						$forum_auth_action[$forum_id] = 'insert';
					}
					else
					{
						$forum_auth_action[$forum_id] = 'update';
					}
				}

				for($j = 0; $j < count($forum_auth_fields); $j++)
				{
					$auth_field = $forum_auth_fields[$j];

					if( $forum_access[$i][$auth_field] == AUTH_ACL && isset($change_acl_list[$forum_id][$auth_field]) )
					{
						if ( ( empty($auth_access[$forum_id]['auth_mod']) && 
							( isset($auth_access[$forum_id][$auth_field]) && $change_acl_list[$forum_id][$auth_field] != $auth_access[$forum_id][$auth_field] ) || 
							( !isset($auth_access[$forum_id][$auth_field]) && !empty($change_acl_list[$forum_id][$auth_field]) ) ) ||
							!empty($update_mod_status[$forum_id])
						)
						{
							$update_acl_status[$forum_id][$auth_field] = ( !empty($update_mod_status[$forum_id]) ) ? 0 :  $change_acl_list[$forum_id][$auth_field];

							if ( isset($auth_access[$forum_id][$auth_field]) && empty($update_acl_status[$forum_id][$auth_field]) && $forum_auth_action[$forum_id] != 'insert' && $forum_auth_action[$forum_id] != 'update' )
							{
								$forum_auth_action[$forum_id] = 'delete';
							}
							else if ( !isset($auth_access[$forum_id][$auth_field]) && !( $forum_auth_action[$forum_id] == 'delete' && empty($update_acl_status[$forum_id][$auth_field]) ) )
							{
								$forum_auth_action[$forum_id] = 'insert';
							}
							else if ( isset($auth_access[$forum_id][$auth_field]) && !empty($update_acl_status[$forum_id][$auth_field]) ) 
							{
								$forum_auth_action[$forum_id] = 'update';
							}
						}
						else if ( ( empty($auth_access[$forum_id]['auth_mod']) && 
							( isset($auth_access[$forum_id][$auth_field]) && $change_acl_list[$forum_id][$auth_field] == $auth_access[$forum_id][$auth_field] ) ) && $forum_auth_action[$forum_id] == 'delete' )
						{
							$forum_auth_action[$forum_id] = 'update';
						}
					}
				}
			}

			$delete_sql = '';
			foreach($forum_auth_action as $forum_id => $action)
			{
				if ( $action == 'delete' )
				{
					$delete_sql .= ( ( $delete_sql != '' ) ? ', ' : '' ) . $forum_id;
				}
				else
				{
					if ( $action == 'insert' )
					{
						$sql_field = '';
						$sql_value = '';
						
						foreach($update_acl_status[$forum_id] as $auth_type => $value)
						{
							$sql_field .= ( ( $sql_field != '' ) ? ', ' : '' ) . $auth_type;
							$sql_value .= ( ( $sql_value != '' ) ? ', ' : '' ) . $value;
						}
						$sql_field .= ( ( $sql_field != '' ) ? ', ' : '' ) . 'auth_mod';
						$sql_value .= ( ( $sql_value != '' ) ? ', ' : '' ) . ( ( !isset($update_mod_status[$forum_id]) ) ? 0 : $update_mod_status[$forum_id]);

						$sql = 'INSERT INTO ' . AUTH_ACCESS_TABLE . " (forum_id, group_id, $sql_field) 
							VALUES ($forum_id, $group_id, $sql_value)";
					}
					else
					{
						$sql_values = '';
						foreach ($update_acl_status[$forum_id] as $auth_type => $value)
						{
							$sql_values .= ( ( $sql_values != '' ) ? ', ' : '' ) . $auth_type . ' = ' . $value;
						}
						$sql_values .= ( ( $sql_values != '' ) ? ', ' : '' ) . 'auth_mod = ' . ( ( !isset($update_mod_status[$forum_id]) ) ? 0 : $update_mod_status[$forum_id]);

						$sql = 'UPDATE ' . AUTH_ACCESS_TABLE . " 
							SET $sql_values 
							WHERE group_id = $group_id 
								AND forum_id = $forum_id";
					}
					if( !($result = $db->sql_query($sql)) )
					{
						trigger_error('Couldn\'t update private forum permissions', E_USER_WARNING);
					}
				}
			}

			if ( $delete_sql != '' )
			{
				$sql = 'DELETE FROM ' . AUTH_ACCESS_TABLE . " 
					WHERE group_id = $group_id 
						AND forum_id IN ($delete_sql)";
				if( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t delete permission entries', E_USER_WARNING);
				}
			}

			$l_auth_return = ( $mode == 'user' ) ? '点击 %s这里%s 返回用户权限设定' : '点击 %s这里%s 返回小组权限设定';
			$message = '权限更新成功<br />' . sprintf($l_auth_return, '<a href="' . append_sid('admin_ug_auth.php?mode=' . $mode) . '">', '</a>') . '<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
		}

		$sql = 'SELECT u.user_id 
			FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u  
			WHERE ug.group_id = aa.group_id 
				AND u.user_id = ug.user_id 
				AND ug.user_pending = 0
				AND u.user_level NOT IN (' . MOD . ', ' . ADMIN . ') 
			GROUP BY u.user_id 
			HAVING SUM(aa.auth_mod) > 0';
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t obtain user/group permissions', E_USER_WARNING);
		}

		$set_mod = '';
		while( $row = $db->sql_fetchrow($result) )
		{
			$set_mod .= ( ( $set_mod != '' ) ? ', ' : '' ) . $row['user_id'];
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT u.user_id 
			FROM ( ( ' . USERS_TABLE . ' u  
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug ON ug.user_id = u.user_id ) 
			LEFT JOIN ' . AUTH_ACCESS_TABLE . ' aa ON aa.group_id = ug.group_id ) 
			WHERE u.user_level NOT IN (' . USER . ', ' . ADMIN . ')
			GROUP BY u.user_id 
			HAVING SUM(aa.auth_mod) = 0';

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t obtain user/group permissions', E_USER_WARNING);
		}

		$unset_mod = '';
		while( $row = $db->sql_fetchrow($result) )
		{
			$unset_mod .= ( ( $unset_mod != '' ) ? ', ' : '' ) . $row['user_id'];
		}
		$db->sql_freeresult($result);

		if ( $set_mod != '' )
		{
			$sql = 'UPDATE ' . USERS_TABLE . ' 
				SET user_level = ' . MOD . " 
				WHERE user_id IN ($set_mod)";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t update user level', E_USER_WARNING);
			}
		}

		if ( $unset_mod != '' )
		{
			$sql = 'UPDATE ' . USERS_TABLE . ' 
				SET user_level = ' . USER . " 
				WHERE user_id IN ($unset_mod)";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t update user level', E_USER_WARNING);
			}
		}

		$sql = 'SELECT user_id FROM ' . USER_GROUP_TABLE . "
			WHERE group_id = $group_id";
		$result = $db->sql_query($sql);

		$group_user = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$group_user[$row['user_id']] = $row['user_id'];
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT ug.user_id, COUNT(auth_mod) AS is_auth_mod 
			FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . ' ug 
			WHERE ug.user_id IN (' . implode(', ', $group_user) . ') 
				AND aa.group_id = ug.group_id 
				AND aa.auth_mod = 1
			GROUP BY ug.user_id';
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain moderator status', E_USER_WARNING);
		}

		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['is_auth_mod'])
			{
				unset($group_user[$row['user_id']]);
			}
		}
		$db->sql_freeresult($result);

		if (count($group_user))
		{
			$sql = 'UPDATE ' . USERS_TABLE . ' 
				SET user_level = ' . USER . ' 
				WHERE user_id IN (' . implode(', ', $group_user) . ') AND user_level = ' . MOD;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update user level', E_USER_WARNING);
			}
		}

		trigger_error($message);
	}
}
else if ( ( $mode == 'user' && ( isset($_POST['username']) || $user_id ) ) || ( $mode == 'group' && $group_id ) )
{
	if ( isset($_POST['username']) )
	{
		$this_userdata = get_userdata($_POST['username'], true);
		if ( !is_array($this_userdata) )
		{
			trigger_error('您指定的用户不存在', E_USER_ERROR);
		}
		$user_id = $this_userdata['user_id'];
	}

	$sql = 'SELECT f.* 
		FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . ' c
		WHERE f.cat_id = c.cat_id
		ORDER BY c.cat_order, f.forum_order ASC';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Couldn\'t obtain forum information', E_USER_WARNING);
	}

	$forum_access = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$forum_access[] = $row;
	}
	$db->sql_freeresult($result);

	if( empty($adv) )
	{
		for($i = 0; $i < count($forum_access); $i++)
		{
			$forum_id = $forum_access[$i]['forum_id'];

			$forum_auth_level[$forum_id] = AUTH_ALL;

			for($j = 0; $j < count($forum_auth_fields); $j++)
			{
				$forum_access[$i][$forum_auth_fields[$j]] . ' :: ';
				if ( $forum_access[$i][$forum_auth_fields[$j]] == AUTH_ACL )
				{
					$forum_auth_level[$forum_id] = AUTH_ACL;
					$forum_auth_level_fields[$forum_id][] = $forum_auth_fields[$j];
				}
			}
		}
	}

	$sql = 'SELECT u.user_id, u.username, u.user_level, g.group_id, g.group_name, g.group_single_user, ug.user_pending FROM ' . USERS_TABLE . ' u, ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug WHERE ';
	$sql .= ( $mode == 'user' ) ? "u.user_id = $user_id AND ug.user_id = u.user_id AND g.group_id = ug.group_id" : "g.group_id = $group_id AND ug.group_id = g.group_id AND u.user_id = ug.user_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Couldn\'t obtain user/group information', E_USER_WARNING);
	}
	$ug_info = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$ug_info[] = $row;
	}
	$db->sql_freeresult($result);

	$sql = ( $mode == 'user' ) ? 'SELECT aa.*, g.group_single_user FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . ' ug, ' . GROUPS_TABLE. " g WHERE ug.user_id = $user_id AND g.group_id = ug.group_id AND aa.group_id = ug.group_id AND g.group_single_user = 1" : 'SELECT * FROM ' . AUTH_ACCESS_TABLE . " WHERE group_id = $group_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Couldn\'t obtain user/group permissions', E_USER_WARNING);
	}

	$auth_access = array();
	$auth_access_count = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$auth_access[$row['forum_id']][] = $row; 
		$auth_access_count[$row['forum_id']]++;
	}
	$db->sql_freeresult($result);

	$is_admin = ( $mode == 'user' ) ? ( ( $ug_info[0]['user_level'] == ADMIN && $ug_info[0]['user_id'] != ANONYMOUS ) ? 1 : 0 ) : 0;

	for($i = 0; $i < count($forum_access); $i++)
	{
		$forum_id = $forum_access[$i]['forum_id'];

		unset($prev_acl_setting);

		for($j = 0; $j < count($forum_auth_fields); $j++)
		{
			$key = $forum_auth_fields[$j];
			$value = $forum_access[$i][$key];

			switch( $value )
			{
				case AUTH_ALL:
				case AUTH_REG:
					$auth_ug[$forum_id][$key] = 1;
					break;

				case AUTH_ACL:
					$auth_ug[$forum_id][$key] = ( !empty($auth_access_count[$forum_id]) ) ? check_auth(AUTH_ACL, $key, $auth_access[$forum_id], $is_admin) : 0;
					$auth_field_acl[$forum_id][$key] = $auth_ug[$forum_id][$key];

					if ( isset($prev_acl_setting) )
					{
						if ( $prev_acl_setting != $auth_ug[$forum_id][$key] && empty($adv) )
						{
							$adv = 1;
						}
					}

					$prev_acl_setting = $auth_ug[$forum_id][$key];

					break;

				case AUTH_MOD:
					$auth_ug[$forum_id][$key] = ( !empty($auth_access_count[$forum_id]) ) ? check_auth(AUTH_MOD, $key, $auth_access[$forum_id], $is_admin) : 0;
					break;

				case AUTH_ADMIN:
					$auth_ug[$forum_id][$key] = $is_admin;
					break;

				default:
					$auth_ug[$forum_id][$key] = 0;
					break;
			}
		}

		$auth_ug[$forum_id]['auth_mod'] = ( !empty($auth_access_count[$forum_id]) ) ? check_auth(AUTH_MOD, 'auth_mod', $auth_access[$forum_id], 0) : 0;

	}

	$i = 0;
	@reset($auth_ug);

	foreach($auth_ug as $forum_id => $user_ary)
	{
		if ( empty($adv) )
		{
			if ( $forum_auth_level[$forum_id] == AUTH_ACL )
			{
				$allowed = 1;

				for($j = 0; $j < count($forum_auth_level_fields[$forum_id]); $j++)
				{
					if ( !$auth_ug[$forum_id][$forum_auth_level_fields[$forum_id][$j]] )
					{
						$allowed = 0;
					}
				}

				$optionlist_acl = '<select name="private[' . $forum_id . ']">';

				if ( $is_admin || $user_ary['auth_mod'] )
				{
					$optionlist_acl .= '<option value="1">允许进入</option>';
				}
				else if ( $allowed )
				{
					$optionlist_acl .= '<option value="1" selected="selected">允许进入</option><option value="0">禁止进入</option>';
				}
				else
				{
					$optionlist_acl .= '<option value="1">允许进入</option><option value="0" selected="selected">禁止进入</option>';
				}

				$optionlist_acl .= '</select>';
			}
			else
			{
				$optionlist_acl = '不可用';
			}
		}
		else
		{
			for($j = 0; $j < count($forum_access); $j++)
			{
				if ( $forum_access[$j]['forum_id'] == $forum_id )
				{
					for($k = 0; $k < count($forum_auth_fields); $k++)
					{
						$field_name = $forum_auth_fields[$k];

						if( $forum_access[$j][$field_name] == AUTH_ACL )
						{
							$optionlist_acl_adv[$forum_id][$k] = '<select name="private_' . $field_name . '[' . $forum_id . ']">';

							if( isset($auth_field_acl[$forum_id][$field_name]) && !($is_admin || $user_ary['auth_mod']) )
							{
								if( !$auth_field_acl[$forum_id][$field_name] )
								{
									$optionlist_acl_adv[$forum_id][$k] .= '<option value="1">是</option><option value="0" selected="selected">否</option>';
								}
								else
								{
									$optionlist_acl_adv[$forum_id][$k] .= '<option value="1" selected="selected">是</option><option value="0">否</option>';
								}
							}
							else
							{
								if( $is_admin || $user_ary['auth_mod'] )
								{
									$optionlist_acl_adv[$forum_id][$k] .= '<option value="1">是</option>';
								}
								else
								{
									$optionlist_acl_adv[$forum_id][$k] .= '<option value="1">是</option><option value="0" selected="selected">否</option>';
								}
							}

							$optionlist_acl_adv[$forum_id][$k] .= '</select>';

						}
						else
						{
							$optionlist_acl_adv[$forum_id][$k] = '不可用';
						}
					}
				}
			}
		}

		$optionlist_mod = '<select name="moderator[' . $forum_id . ']">';
		$optionlist_mod .= ( $user_ary['auth_mod'] ) ? '<option value="1" selected="selected">是</option><option value="0">否</option>' : '<option value="1">是</option><option value="0" selected="selected">否</option>';
		$optionlist_mod .= '</select>';

		$template->assign_block_vars('forums', array(
			'FORUM_NAME' => $forum_access[$i]['forum_name'],

			'U_FORUM_AUTH' => append_sid('admin_forumauth.php?f=' . $forum_access[$i]['forum_id']),

			'S_MOD_SELECT' => $optionlist_mod)
		);

		$s_column_span = 2;

		if( !$adv )
		{
			$template->assign_block_vars('forums.aclvalues', array(
				'S_ACL_SELECT' 		=> $optionlist_acl,
				'L_UG_ACL_TYPE' 	=> '基本权限')
			);
			$s_column_span++;
		}
		else
		{
			for($j = 0; $j < count($forum_auth_fields); $j++)
			{
				$cell_title = $field_names[$forum_auth_fields[$j]];

				$template->assign_block_vars('forums.aclvalues', array(
					'S_ACL_SELECT' 	=> $optionlist_acl_adv[$forum_id][$j],
					'L_UG_ACL_TYPE' => $cell_title)
				);
				$s_column_span++;
			}
		}

		$i++;
	}
//	@reset($auth_user);
	
	if ( $mode == 'user' )
	{
		$t_username = $ug_info[0]['username'];
		$s_user_type = ( $is_admin ) ? '<select name="userlevel"><option value="admin" selected="selected">管理员</option><option value="user">会员</option></select>' : '<select name="userlevel"><option value="admin">管理员</option><option value="user" selected="selected">会员</option></select>';
	}
	else
	{
		$t_groupname = $ug_info[0]['group_name'];
	}

	$name = array();
	$id = array();
	for($i = 0; $i < count($ug_info); $i++)
	{
		if( ( $mode == 'user' && !$ug_info[$i]['group_single_user'] ) || $mode == 'group' )
		{
			$name[] = ( $mode == 'user' ) ? $ug_info[$i]['group_name'] :  $ug_info[$i]['username'];
			$id[] = ( $mode == 'user' ) ? intval($ug_info[$i]['group_id']) : intval($ug_info[$i]['user_id']);
		}
	}

	$t_usergroup_list = $t_pending_list = '';
	if( count($name) )
	{
		for($i = 0; $i < count($ug_info); $i++)
		{
			$ug = ( $mode == 'user' ) ? 'group&amp;' . POST_GROUPS_URL : 'user&amp;' . POST_USERS_URL;

			if (!$ug_info[$i]['user_pending'])
			{
				$t_usergroup_list .= ( ( $t_usergroup_list != '' ) ? ', ' : '' ) . '<a href="' . append_sid("admin_ug_auth.php?mode=$ug=" . $id[$i]) . '">' . $name[$i] . '</a>';
			}
			else
			{
				$t_pending_list .= ( ( $t_pending_list != '' ) ? ', ' : '' ) . '<a href="' . append_sid("admin_ug_auth.php?mode=$ug=" . $id[$i]) . '">' . $name[$i] . '</a>';
			}
		}
	}

	$t_usergroup_list 	= ($t_usergroup_list == '') ? '无' : $t_usergroup_list;
	$t_pending_list 	= ($t_pending_list == '') ? '无' : $t_pending_list;

	page_header();

	$template->set_filenames(array(
		'body' => 'admin/auth_ug_body.tpl')
	);

	$adv_switch = ( empty($adv) ) ? 1 : 0;
	$u_ug_switch = ( $mode == 'user' ) ? POST_USERS_URL . '=' . $user_id : POST_GROUPS_URL . '=' . $group_id;
	$switch_mode = append_sid("admin_ug_auth.php?mode=$mode&amp;" . $u_ug_switch . "&amp;adv=$adv_switch");
	$switch_mode_text = ( empty($adv) ) ? '高级模式' : '简洁模式';
	$u_switch_mode = '<a href="' . $switch_mode . '">' . $switch_mode_text . '</a>';

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="adv" value="' . $adv . '" />';
	$s_hidden_fields .= ( $mode == 'user' ) ? '<input type="hidden" name="' . POST_USERS_URL . '" value="' . $user_id . '" />' : '<input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';

	if ( $mode == 'user' )
	{
		$template->assign_block_vars('switch_user_auth', array());

		$template->assign_vars(array(
			'USERNAME' 					=> $t_username,
			'USER_LEVEL' 				=> '网站权限 : ' . $s_user_type,
			'USER_GROUP_MEMBERSHIPS'	=> '小组权限 : ' . $t_usergroup_list)
		);
	}
	else
	{
		$template->assign_block_vars('switch_group_auth', array());

		$template->assign_vars(array(
			'USERNAME' 				=> $t_groupname,
			'GROUP_MEMBERSHIP' 		=> '小组的成员 : ' . $t_usergroup_list . '<br />等待审核的成员 : ' . $t_pending_list)
		);
	}

	$template->assign_vars(array(
		'L_USER_OR_GROUPNAME' 		=> ( $mode == 'user' ) ? '用户名' : '小组名称',
		'L_AUTH_TITLE' 				=> ( $mode == 'user' ) ? '用户权限' : '小组权限',

		'U_USER_OR_GROUP' 			=> append_sid('admin_ug_auth.php'),
		'U_UG_SELECT'	 			=> append_sid('admin_ug_auth.php?mode=group'),
		'U_SWITCH_MODE' 			=> $u_switch_mode,

		'S_COLUMN_SPAN' 			=> $s_column_span,
		'S_AUTH_ACTION' 			=> append_sid('admin_ug_auth.php'), 
		'S_HIDDEN_FIELDS' 			=> $s_hidden_fields)
	);
}
else
{

	page_header();

	$template->set_filenames(array(
		'body' => ( $mode == 'user' ) ? 'admin/auth_select_user.tpl' : 'admin/auth_select_body.tpl')
	);

	if ( $mode == 'user' )
	{
		$template->assign_vars(array(
			'U_SEARCH_USER' => append_sid(ROOT_PATH . 'search.php?mode=searchuser'))
		);
	}
	else
	{
		$sql = 'SELECT group_id, group_name
			FROM ' . GROUPS_TABLE . '
			WHERE group_single_user <> ' . TRUE;
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Couldn\'t get group list', E_USER_WARNING);
		}
	
		$select_list = '还没有创建任何小组';
		if ( $row = $db->sql_fetchrow($result) )
		{
			$select_list = '<select name="' . POST_GROUPS_URL . '">';
			do
			{
				$select_list .= '<option value="' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
			}
			while ( $row = $db->sql_fetchrow($result) );
			$select_list .= '</select>';
			
			$template->assign_block_vars('not_group', array());
		}

		$template->assign_vars(array(
			'S_AUTH_SELECT' => $select_list)
		);
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';

	$l_type = ( $mode == 'user' ) ? 'USER' : 'AUTH';

	$template->assign_vars(array(
		'L_' . $l_type . '_TITLE' 		=> ( $mode == 'user' ) ? '用户权限' : '小组权限',
		'L_' . $l_type . '_SELECT' 		=> ( $mode == 'user' ) ? '选择用户' : '选择小组',
		'L_LOOK_UP' 					=> ( $mode == 'user' ) ? '查看用户' : '查看小组',

		'S_HIDDEN_FIELDS' 				=> $s_hidden_fields, 
		'S_' . $l_type . '_ACTION' 		=> append_sid('admin_ug_auth.php'))
	);

}

$template->pparse('body');

page_footer();

?>