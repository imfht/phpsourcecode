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
	$file = basename(__FILE__);
	$module['论坛']['管理'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

require(ROOT_PATH . 'includes/attach/functions_attach.php');
require(ROOT_PATH . 'includes/functions/admin.php');

$forum_auth_ary = array(
	'auth_view' 		=> AUTH_ALL, 
	'auth_read' 		=> AUTH_ALL, 
	'auth_post' 		=> AUTH_REG, 
	'auth_reply' 		=> AUTH_REG, 
	'auth_edit'			=> AUTH_REG, 
	'auth_delete' 		=> AUTH_REG, 
	'auth_sticky' 		=> AUTH_MOD, 
	'auth_announce' 	=> AUTH_MOD,
	'auth_marrow'		=> AUTH_MOD, 
	'auth_vote' 		=> AUTH_REG, 
	'auth_pollcreate' 	=> AUTH_REG,
	'auth_attachments'	=> AUTH_REG,
	'auth_download'		=> AUTH_REG
);

if( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = "";
}

function get_info($mode, $id)
{
	global $db;

	switch($mode)
	{
		case 'category':
			$table 		= CATEGORIES_TABLE;
			$idfield 	= 'cat_id';
			$namefield 	= 'cat_title';
			break;

		case 'forum':
			$table 		= FORUMS_TABLE;
			$idfield 	= 'forum_id';
			$namefield 	= 'forum_name';
			break;

		default:
			trigger_error("Wrong mode for generating select list", E_USER_WARNING);
			break;
	}

	$sql = "SELECT count(*) as total
		FROM $table";
	if( !$result = $db->sql_query($sql) )
	{
		trigger_error("Couldn't get Forum/Category information", E_USER_WARNING);
	}
	$count = $db->sql_fetchrow($result);
	$count = $count['total'];

	$sql = "SELECT *
		FROM $table
		WHERE $idfield = $id"; 

	if( !$result = $db->sql_query($sql) )
	{
		trigger_error("Couldn't get Forum/Category information", E_USER_WARNING);
	}

	if( $db->sql_numrows($result) != 1 )
	{
		trigger_error("Forum/Category doesn't exist or multiple forums/categories with ID $id", E_USER_WARNING);
	}

	$return = $db->sql_fetchrow($result);
	$return['number'] = $count;
	return $return;
}

function get_list($mode, $id, $select)
{
	global $db;

	switch($mode)
	{
		case 'category':
			$table 		= CATEGORIES_TABLE;
			$idfield 	= 'cat_id';
			$namefield 	= 'cat_title';
			break;

		case 'forum':
			$table 		= FORUMS_TABLE;
			$idfield 	= 'forum_id';
			$namefield 	= 'forum_name';
			break;

		default:
			trigger_error("Wrong mode for generating select list", E_USER_WARNING);
			break;
	}

	$sql = "SELECT *
		FROM $table";
	if( $select == 0 )
	{
		$sql .= " WHERE $idfield <> $id";
	}

	if( !$result = $db->sql_query($sql) )
	{
		trigger_error("Couldn't get list of Categories/Forums", E_USER_WARNING);
	}

	$catlist = "";

	while( $row = $db->sql_fetchrow($result) )
	{
		$s = "";
		if ($row[$idfield] == $id)
		{
			$s = " selected=\"selected\"";
		}
		$catlist .= "<option value=\"$row[$idfield]\"$s>" . $row[$namefield] . "</option>\n";
	}

	return($catlist);
}

function renumber_order($mode, $cat = 0)
{
	global $db;

	switch($mode)
	{
		case 'category':
			$table 		= CATEGORIES_TABLE;
			$idfield 	= 'cat_id';
			$orderfield = 'cat_order';
			$cat = 0;
			break;

		case 'forum':
			$table 		= FORUMS_TABLE;
			$idfield 	= 'forum_id';
			$orderfield	= 'forum_order';
			$catfield 	= 'cat_id';
			break;

		default:
			trigger_error("Wrong mode for generating select list", E_USER_WARNING);
			break;
	}

	$sql = "SELECT * FROM $table";
	if( $cat != 0)
	{
		$sql .= " WHERE $catfield = $cat";
	}
	$sql .= " ORDER BY $orderfield ASC";


	if( !$result = $db->sql_query($sql) )
	{
		trigger_error("Couldn't get list of Categories", E_USER_WARNING);
	}

	$i = 10;
	$inc = 10;

	while( $row = $db->sql_fetchrow($result) )
	{
		$sql = "UPDATE $table
			SET $orderfield = $i
			WHERE $idfield = " . $row[$idfield];
		if( !$db->sql_query($sql) )
		{
			trigger_error("Couldn't update order fields", E_USER_WARNING);
		}
		$i += 10;
	}

}

if( isset($_POST['addforum']) || isset($_POST['addcategory']) )
{
	$mode = ( isset($_POST['addforum']) ) ? "addforum" : "addcat";

	if( $mode == "addforum" )
	{
		list($cat_id) = each($_POST['addforum']);
		$cat_id = intval($cat_id);
		$forumname = stripslashes($_POST['forumname'][$cat_id]);
	}
}
$show_index = false;
if( !empty($mode) ) 
{
	switch($mode)
	{
		case 'addforum':
		case 'editforum':

			if ($mode == 'editforum')
			{

				$l_title		= '编辑论坛';
				$newmode 		= 'modforum';
				$buttonvalue 	= '更新';

				$forum_id 		= intval($_GET[POST_FORUM_URL]);

				$row 			= get_info('forum', $forum_id);

				$cat_id 		= $row['cat_id'];
				$forumname 		= $row['forum_name'];
				$forum_money 	= $row['forum_money'];
				$forumdesc 		= $row['forum_desc'];
				$forumstatus 	= $row['forum_status'];
				$forumicon 		= $row['forum_icon'];

				if( $row['prune_enable'] )
				{
					$prune_enabled = 'checked="checked"';
					$sql = "SELECT *
						FROM " . PRUNE_TABLE . "
						WHERE forum_id = $forum_id";
					if(!$pr_result = $db->sql_query($sql))
					{
						trigger_error('Auto-Prune: Couldn\'t read auto_prune table.', E_USER_WARNING);
					}
					$pr_row = $db->sql_fetchrow($pr_result);
				}
				else
				{
					$prune_enabled 	= '';
					$pr_row 		= array('forum_status' => 0);
				}
			}
			else
			{
				$l_title 		= '创建论坛';
				$newmode 		= 'createforum';
				$buttonvalue 	= '创建';

				$forumdesc 		= '';
				$forumicon 		= '';
				$forum_money 	= 0;
				$forumstatus 	= FORUM_UNLOCKED;
				$forum_id 		= ''; 
				$prune_enabled 	= '';
				$pr_row 		= array('forum_status' => 0);
			}

			$catlist = get_list('category', $cat_id, TRUE);

			if ( $pr_row['forum_status'] == FORUM_LOCKED )
			{
				$statuslist = '<option value="' . FORUM_UNLOCKED . '">开放</option>';
				$statuslist .= '<option value="' . FORUM_LOCKED . '“ selected="selected">锁定</option>'; 
			}
			else if ($pr_row['forum_status'] == FORUM_UNLOCKED)
			{
				$statuslist = '<option value="' . FORUM_UNLOCKED . '" selected="selected">开放</option>';
				$statuslist .= '<option value="' . FORUM_LOCKED . '" >锁定</option>'; 
			}
			else
			{
				$statuslist = '<option value="' . FORUM_UNLOCKED . '" selected="selected">开放</option>';
				$statuslist .= '<option value="' . FORUM_LOCKED . '">锁定</option>'; 
			}

			$template->set_filenames(array(
				"body" => "admin/forum_edit_body.tpl")
			);

			$s_hidden_fields = '<input type="hidden" name="mode" value="' . $newmode .'" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

			$template->assign_vars(array(
				'S_FORUM_ACTION' 		=> append_sid("admin_forums.php"),
				'S_HIDDEN_FIELDS' 		=> $s_hidden_fields,
				'S_SUBMIT_VALUE' 		=> $buttonvalue, 
				'S_CAT_LIST' 			=> $catlist,
				'S_STATUS_LIST' 		=> $statuslist,
				'S_PRUNE_ENABLED' 		=> $prune_enabled,
				'S_FORUM_POSTCOUNT' 	=> ( isset($row) && isset($row['forum_postcount']) && ($row['forum_postcount'] == 0) ) ? '' : 'checked="checked"',

				'L_TITLE' 				=> $l_title, 
				
				'U_ADMIN_FORUMS' 		=> append_sid("admin_forums.php"),

				'PRUNE_DAYS' 			=> ( isset($pr_row['prune_days']) ) ? $pr_row['prune_days'] : 7,
				'PRUNE_FREQ' 			=> ( isset($pr_row['prune_freq']) ) ? $pr_row['prune_freq'] : 1,
				'FORUM_NAME' 			=> $forumname,
				'FORUM_MONEY' 			=> $forum_money,
				'F_ICON' 				=> $forumicon,
				'DESCRIPTION' 			=> $forumdesc)
			);
			$template->pparse("body");
			break;

		case 'createforum':

			if( trim($_POST['forumname']) == '' )
			{
				trigger_error('Can\'t create a forum without a name', E_USER_ERROR);
			}

			$sql = 'SELECT MAX(forum_order) AS max_order
				FROM ' . FORUMS_TABLE . '
				WHERE cat_id = ' . intval($_POST[POST_CAT_URL]);
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Couldn\'t get order number from forums table', E_USER_WARNING);
			}
			$row = $db->sql_fetchrow($result);

			$max_order = $row['max_order'];
			$next_order = $max_order + 10;
			
			$sql = 'SELECT MAX(forum_id) AS max_id
				FROM ' . FORUMS_TABLE;
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Couldn\'t get order number from forums table', E_USER_WARNING);
			}
			$row = $db->sql_fetchrow($result);

			$max_id = $row['max_id'];
			$next_id = $max_id + 1;

			$field_sql = '';
			$value_sql = '';
			
			foreach($forum_auth_ary as $field => $value)
			{
				$field_sql .= ", $field";
				$value_sql .= ", $value";

			}

			$sql = 'INSERT INTO ' . FORUMS_TABLE . " (forum_id, forum_name, forum_icon, cat_id, forum_desc, forum_order, forum_status, prune_enable, forum_money, forum_postcount" . $field_sql . ")
				VALUES ('" . $next_id . "', '" . $db->sql_escape($_POST['forumname']) . "', '" . $db->sql_escape($_POST['forumicon']) . "', " . intval($_POST[POST_CAT_URL]) . ", '" . $db->sql_escape($_POST['forumdesc']) . "', $next_order, " . intval($_POST['forumstatus']) . ", " . intval($_POST['prune_enable']) . ",  " . intval($_POST['forum_money']) . ", " . intval($_POST['forum_postcount']) . $value_sql . ")";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Couldn\'t insert row in forums table', E_USER_WARNING);
			}

			if( $_POST['prune_enable'] )
			{

				if( $_POST['prune_days'] == '' || $_POST['prune_freq'] == '')
				{
					trigger_error('您已经开启论坛清理功能, 但并未完成相关设定. 请回到上一步设定相关的项目', E_USER_ERROR);
				}

				$sql = 'INSERT INTO ' . PRUNE_TABLE . " (forum_id, prune_days, prune_freq)
					VALUES('" . $next_id . "', " . intval($_POST['prune_days']) . ", " . intval($_POST['prune_freq']) . ")";
				if( !$db->sql_query($sql) )
				{
					trigger_error('Couldn\'t insert row in prune table', E_USER_WARNING);
				}
			}

			$message = '创建成功<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛列表管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			break;

		case 'modforum':

			if( isset($_POST['prune_enable']))
			{
				if( $_POST['prune_enable'] != 1 )
				{
					$_POST['prune_enable'] = 0;
				}
			}

			$sql = 'UPDATE ' . FORUMS_TABLE . "
				SET forum_name = '" . $db->sql_escape($_POST['forumname']) . "', cat_id = " . intval($_POST[POST_CAT_URL]) . ", forum_desc = '" . $db->sql_escape($_POST['forumdesc']) . "', forum_icon = '" . $db->sql_escape($_POST['forumicon']) . "', forum_status = " . intval($_POST['forumstatus']) . ", prune_enable = " . intval($_POST['prune_enable']) . ", forum_money = " . intval($_POST['forum_money']) . ", forum_postcount = " . intval($_POST['forum_postcount']) . "
				WHERE forum_id = " . intval($_POST[POST_FORUM_URL]);
			if( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t update forum information', E_USER_WARNING);
			}

			if( $_POST['prune_enable'] == 1 )
			{
				if( $_POST['prune_days'] == '' || $_POST['prune_freq'] == '' )
				{
					trigger_error('您已经开启论坛清理功能, 但并未完成相关设定. 请回到上一步设定相关的项目', E_USER_ERROR);
				}

				$sql = 'SELECT *
					FROM ' . PRUNE_TABLE . '
					WHERE forum_id = ' . intval($_POST[POST_FORUM_URL]);
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Couldn\'t get forum Prune Information', E_USER_WARNING);
				}

				if( $db->sql_numrows($result) > 0 )
				{
					$sql = 'UPDATE ' . PRUNE_TABLE . '
						SET	prune_days = ' . intval($_POST['prune_days']) . ',	prune_freq = ' . intval($_POST['prune_freq']) . '
				 		WHERE forum_id = ' . intval($_POST[POST_FORUM_URL]);
				}
				else
				{
					$sql = 'INSERT INTO ' . PRUNE_TABLE . ' (forum_id, prune_days, prune_freq)
						VALUES(' . intval($_POST[POST_FORUM_URL]) . ', ' . intval($_POST['prune_days']) . ', ' . intval($_POST['prune_freq']) . ')';
				}

				if( !$result = $db->sql_query($sql) )
				{
					trigger_error('Couldn\'t Update Forum Prune Information', E_USER_WARNING);
				}
			}

			$message = '论坛更新成功！<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			break;
			
		case 'addcat':

			if( trim($_POST['categoryname']) == '')
			{
				trigger_error('Can\'t create a category without a name', E_USER_ERROR);
			}

			$sql = 'SELECT MAX(cat_order) AS max_order
				FROM ' . CATEGORIES_TABLE;
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Couldn\'t get order number from categories table', E_USER_WARNING);
			}
			$row 		= $db->sql_fetchrow($result);

			$max_order 	= $row['max_order'];
			$next_order	= $max_order + 10;

			$sql = 'INSERT INTO ' . CATEGORIES_TABLE . " (cat_title, cat_order, cat_icon) 
				VALUES ('" . $db->sql_escape($_POST['categoryname']) . "', $next_order, '')";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t insert row in categories table', E_USER_WARNING);
			}

			$message = '论坛分类成功创建！<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			break;
			
		case 'editcat':

			$newmode 		= 'modcat';
			$buttonvalue 	= '保存';
			$cat_id 		= intval($_GET[POST_CAT_URL]);
			$row 			= get_info('category', $cat_id);
			$cat_title 		= $row['cat_title'];
			$caticon 		= $row['cat_icon'];

			$template->set_filenames(array(
				'body' => 'admin/category_edit_body.tpl')
			);

			$s_hidden_fields = '<input type="hidden" name="mode" value="' . $newmode . '" /><input type="hidden" name="' . POST_CAT_URL . '" value="' . $cat_id . '" />';

			$template->assign_vars(array(
				'L_TITLE'			=> '编辑论坛分类',
				'CAT_TITLE' 		=> $cat_title,
				'CAT_ICON' 			=> $caticon,
				'U_ADMIN_FORUMS' 	=> append_sid('admin_forums.php'),
				'S_HIDDEN_FIELDS' 	=> $s_hidden_fields, 
				'S_SUBMIT_VALUE' 	=> $buttonvalue, 
				'S_FORUM_ACTION' 	=> append_sid('admin_forums.php'))
			);

			$template->pparse('body');
			break;

		case 'modcat':

			$sql = 'UPDATE ' . CATEGORIES_TABLE . "
				SET cat_title = '" . $db->sql_escape($_POST['cat_title']) . "', cat_icon = '" . $db->sql_escape($_POST['cat_icon']) . "' 
				WHERE cat_id = " . intval($_POST[POST_CAT_URL]);
			if( !$db->sql_query($sql) )
			{
				trigger_error("Couldn't update forum information", E_USER_WARNING);
			}

			$message = '论坛更新完成<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);

			break;
			
		case 'deleteforum':

			$forum_id = intval($_GET[POST_FORUM_URL]);

			$select_to = '<select name="to_id">';
			$select_to .= '<option value="-1">删除所有</option>';
			$select_to .= get_list('forum', $forum_id, 0);
			$select_to .= '</select>';

			$buttonvalue = '移动并删除';

			$newmode = 'movedelforum';

			$foruminfo = get_info('forum', $forum_id);
			$name = $foruminfo['forum_name'];

			$template->set_filenames(array(
				'body' => 'admin/forum_delete_body.tpl')
			);

			$s_hidden_fields = '<input type="hidden" name="mode" value="' . $newmode . '" /><input type="hidden" name="from_id" value="' . $forum_id . '" />';

			$template->assign_vars(array(
				'NAME' => $name, 
				
				'U_ADMIN_FORUMS' 	=> append_sid('admin_forums.php'),

				'S_HIDDEN_FIELDS' 	=> $s_hidden_fields,
				'S_FORUM_ACTION' 	=> append_sid('admin_forums.php'), 
				'S_SELECT_TO' 		=> $select_to,
				'S_SUBMIT_VALUE' 	=> $buttonvalue)
			);

			$template->pparse('body');
			break;

		case 'movedelforum':

			$from_id = intval($_POST['from_id']);
			$to_id = intval($_POST['to_id']);
			$delete_old = intval($_POST['delete_old']);

			if($to_id == -1)
			{
				$sql = 'SELECT v.vote_id 
					FROM ' . VOTE_DESC_TABLE . ' v, ' . TOPICS_TABLE . " t 
					WHERE t.forum_id = $from_id 
						AND v.topic_id = t.topic_id";
				if (!($result = $db->sql_query($sql)))
				{
					trigger_error("Couldn't obtain list of vote ids", E_USER_WARNING);
				}

				if ($row = $db->sql_fetchrow($result))
				{
					$vote_ids = '';
					do
					{
						$vote_ids .= (($vote_ids != '') ? ', ' : '') . $row['vote_id'];
					}
					while ($row = $db->sql_fetchrow($result));

					$sql = "DELETE FROM " . VOTE_DESC_TABLE . " 
						WHERE vote_id IN ($vote_ids)";
					$db->sql_query($sql);

					$sql = "DELETE FROM " . VOTE_RESULTS_TABLE . " 
						WHERE vote_id IN ($vote_ids)";
					$db->sql_query($sql);

					$sql = "DELETE FROM " . VOTE_USERS_TABLE . " 
						WHERE vote_id IN ($vote_ids)";
					$db->sql_query($sql);
				}
				$db->sql_freeresult($result);
				
				require(ROOT_PATH . 'includes/functions/prune.php');
				prune($from_id, 0, true); 
			}
			else
			{
				$sql = "SELECT *
					FROM " . FORUMS_TABLE . "
					WHERE forum_id IN ($from_id, $to_id)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't verify existence of forums", E_USER_WARNING);
				}

				if($db->sql_numrows($result) != 2)
				{
					trigger_error("Ambiguous forum ID's", E_USER_WARNING);
				}
				$sql = "UPDATE " . TOPICS_TABLE . "
					SET forum_id = $to_id
					WHERE forum_id = $from_id";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't move topics to other forum", E_USER_WARNING);
				}
				$sql = "UPDATE " . POSTS_TABLE . "
					SET	forum_id = $to_id
					WHERE forum_id = $from_id";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't move posts to other forum", E_USER_WARNING);
				}
				sync('forum', $to_id);
			}

			$sql = "SELECT ug.user_id 
				FROM " . AUTH_ACCESS_TABLE . " a, " . USER_GROUP_TABLE . " ug 
				WHERE a.forum_id <> $from_id 
					AND a.auth_mod = 1
					AND ug.group_id = a.group_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't obtain moderator list", E_USER_WARNING);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				$user_ids = '';
				do
				{
					$user_ids .= (($user_ids != '') ? ', ' : '' ) . $row['user_id'];
				}
				while ($row = $db->sql_fetchrow($result));

				$sql = "SELECT ug.user_id 
					FROM " . AUTH_ACCESS_TABLE . " a, " . USER_GROUP_TABLE . " ug 
					WHERE a.forum_id = $from_id 
						AND a.auth_mod = 1 
						AND ug.group_id = a.group_id
						AND ug.user_id NOT IN ($user_ids)";
				if( !$result2 = $db->sql_query($sql) )
				{
					trigger_error("Couldn't obtain moderator list", E_USER_WARNING);
				}
					
				if ($row = $db->sql_fetchrow($result2))
				{
					$user_ids = '';
					do
					{
						$user_ids .= (($user_ids != '') ? ', ' : '' ) . $row['user_id'];
					}
					while ($row = $db->sql_fetchrow($result2));

					$sql = "UPDATE " . USERS_TABLE . " 
						SET user_level = " . USER . " 
						WHERE user_id IN ($user_ids) 
							AND user_level <> " . ADMIN;
					$db->sql_query($sql);
				}
				$db->sql_freeresult($result);

			}
			$db->sql_freeresult($result2);

			$sql = "DELETE FROM " . FORUMS_TABLE . "
				WHERE forum_id = $from_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't delete forum", E_USER_WARNING);
			}
			
			$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
				WHERE forum_id = $from_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't delete forum", E_USER_WARNING);
			}
			
			$sql = "DELETE FROM " . PRUNE_TABLE . "
				WHERE forum_id = $from_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't delete forum prune information!", E_USER_WARNING);
			}

			$message = '论坛更新完成<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);

			break;
			
		case 'deletecat':

			$cat_id 			= intval($_GET[POST_CAT_URL]);

			$buttonvalue 		= '移动并删除';
			$newmode 			= 'movedelcat';
			$catinfo 			= get_info('category', $cat_id);
			$name 				= $catinfo['cat_title'];
			$caticon 			= $catinfo['cat_icon'];

			if ($catinfo['number'] == 1)
			{
				$sql = "SELECT count(*) as total
					FROM ". FORUMS_TABLE;
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't get Forum count", E_USER_WARNING);
				}
				$count = $db->sql_fetchrow($result);
				$count = $count['total'];

				if ($count > 0)
				{
					trigger_error('在删除这个分类之前，您必须先删除分类底下的所有论坛', E_USER_ERROR);
				}
				else
				{
					$select_to = '没有选择移动的位置';
				}
			}
			else
			{
				$select_to = '<select name="to_id">';
				$select_to .= get_list('category', $cat_id, 0);
				$select_to .= '</select>';
			}

			$template->set_filenames(array(
				"body" => "admin/forum_delete_body.tpl")
			);

			$s_hidden_fields = '<input type="hidden" name="mode" value="' . $newmode . '" /><input type="hidden" name="from_id" value="' . $cat_id . '" />';

			$template->assign_vars(array(
				'NAME' 				=> $name, 
				
				'U_ADMIN_FORUMS' 	=> append_sid("admin_forums.php"), 
				
				'S_HIDDEN_FIELDS' 	=> $s_hidden_fields,
				'S_FORUM_ACTION' 	=> append_sid("admin_forums.php"), 
				'S_SELECT_TO'		=> $select_to,
				'S_SUBMIT_VALUE' 	=> $buttonvalue)
			);

			$template->pparse("body");
			break;

		case 'movedelcat':

			$from_id = intval($_POST['from_id']);
			$to_id = intval($_POST['to_id']);

			if (!empty($to_id))
			{
				$sql = "SELECT *
					FROM " . CATEGORIES_TABLE . "
					WHERE cat_id IN ($from_id, $to_id)";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't verify existence of categories", E_USER_WARNING);
				}
				if($db->sql_numrows($result) != 2)
				{
					trigger_error("Ambiguous category ID's", E_USER_WARNING);
				}

				$sql = "UPDATE " . FORUMS_TABLE . "
					SET cat_id = $to_id
					WHERE cat_id = $from_id";
				if( !$result = $db->sql_query($sql) )
				{
					trigger_error("Couldn't move forums to other category", E_USER_WARNING);
				}
			}

			$sql = "DELETE FROM " . CATEGORIES_TABLE ."
				WHERE cat_id = $from_id";
				
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't delete category", E_USER_WARNING);
			}

			$message = '论坛更新完成！<br />点击 <a href="' . append_sid('admin_forums.php') . '">这里</a> 返回论坛管理<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			break;

		case 'forum_order':

			$move = intval($_GET['move']);
			$forum_id = intval($_GET[POST_FORUM_URL]);

			$forum_info = get_info('forum', $forum_id);

			$cat_id = $forum_info['cat_id'];

			$sql = "UPDATE " . FORUMS_TABLE . "
				SET forum_order = forum_order + $move
				WHERE forum_id = $forum_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't change category order", E_USER_WARNING);
			}

			renumber_order('forum', $forum_info['cat_id']);
			$show_index = TRUE;

			break;
			
		case 'cat_order':

			$move = intval($_GET['move']);
			$cat_id = intval($_GET[POST_CAT_URL]);

			$sql = "UPDATE " . CATEGORIES_TABLE . "
				SET cat_order = cat_order + $move
				WHERE cat_id = $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error("Couldn't change category order", E_USER_WARNING);
			}

			renumber_order('category');
			$show_index = TRUE;

			break;

		case 'forum_sync':
			sync('forum', intval($_GET[POST_FORUM_URL]));
			$show_index = TRUE;

			break;

		default:
			trigger_error('没有指定模式', E_USER_ERROR);
			break;
	}

	if ($show_index != TRUE)
	{
		page_footer();
		exit;
	}
}

$template->set_filenames(array(
	"body" => "admin/forum_admin_body.tpl")
);

$template->assign_vars(array(
	'S_FORUM_ACTION' => append_sid("admin_forums.php"))
);

$sql = "SELECT cat_id, cat_title, cat_order
	FROM " . CATEGORIES_TABLE . " 
	ORDER BY cat_order";
if( !$q_categories = $db->sql_query($sql) )
{
	trigger_error("Could not query categories list", E_USER_WARNING);
}

if( $total_categories = $db->sql_numrows($q_categories) )
{
	$category_rows = $db->sql_fetchrowset($q_categories);

	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		ORDER BY cat_id, forum_order";
	if(!$q_forums = $db->sql_query($sql))
	{
		trigger_error("Could not query forums information", E_USER_WARNING);
	}

	if( $total_forums = $db->sql_numrows($q_forums) )
	{
		$forum_rows = $db->sql_fetchrowset($q_forums);
	}

	$gen_cat = array();

	for($i = 0; $i < $total_categories; $i++)
	{
		$cat_id = $category_rows[$i]['cat_id'];

		$template->assign_block_vars("catrow", array( 
			'S_ADD_FORUM_SUBMIT' 	=> "addforum[$cat_id]", 
			'S_ADD_FORUM_NAME' 		=> "forumname[$cat_id]", 
			'CAT_ID' 				=> $cat_id,
			'CAT_TITLE' 			=> $category_rows[$i]['cat_title'],
			'U_CAT_EDIT' 			=> append_sid("admin_forums.php?mode=editcat&amp;" . POST_CAT_URL . "=$cat_id"),
			'U_CAT_DELETE' 			=> append_sid("admin_forums.php?mode=deletecat&amp;" . POST_CAT_URL . "=$cat_id"),
			'U_CAT_MOVE_UP' 		=> append_sid("admin_forums.php?mode=cat_order&amp;move=-15&amp;" . POST_CAT_URL . "=$cat_id"),
			'U_CAT_MOVE_DOWN' 		=> append_sid("admin_forums.php?mode=cat_order&amp;move=15&amp;" . POST_CAT_URL . "=$cat_id"),
			'U_VIEWCAT' 			=> append_sid(ROOT_PATH . "forum.php?" . POST_CAT_URL . "=$cat_id"))
		);

		for($j = 0; $j < $total_forums; $j++)
		{
			$forum_id = $forum_rows[$j]['forum_id'];
			
			if ($forum_rows[$j]['cat_id'] == $cat_id)
			{

				$template->assign_block_vars("catrow.forumrow",	array(
					'FORUM_NAME' 			=> $forum_rows[$j]['forum_name'],
					'FORUM_DESC' 			=> $forum_rows[$j]['forum_desc'],
					'NUM_TOPICS' 			=> $forum_rows[$j]['forum_topics'],
					'NUM_POSTS' 			=> $forum_rows[$j]['forum_posts'],

					'U_VIEWFORUM' 			=> append_sid(ROOT_PATH."viewforum.php?" . POST_FORUM_URL . "=$forum_id"),
					'U_FORUM_EDIT' 			=> append_sid("admin_forums.php?mode=editforum&amp;" . POST_FORUM_URL . "=$forum_id"),
					'U_FORUM_DELETE' 		=> append_sid("admin_forums.php?mode=deleteforum&amp;" . POST_FORUM_URL . "=$forum_id"),
					'U_FORUM_MOVE_UP' 		=> append_sid("admin_forums.php?mode=forum_order&amp;move=-15&amp;" . POST_FORUM_URL . "=$forum_id"),
					'U_FORUM_MOVE_DOWN' 	=> append_sid("admin_forums.php?mode=forum_order&amp;move=15&amp;" . POST_FORUM_URL . "=$forum_id"),
					'U_FORUM_RESYNC' 		=> append_sid("admin_forums.php?mode=forum_sync&amp;" . POST_FORUM_URL . "=$forum_id"))
				);

			}
			
		} 

	} 

}

$template->pparse("body");

page_footer();

?>