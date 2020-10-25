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
	$module['论坛']['权限']   = $filename;

	return;
}

define('IN_PHPBB', true);
$no_page_header = true;
define('ROOT_PATH', './../');
require('./pagestart.php');

$simple_auth_ary = array(
	0  => array(AUTH_ALL, AUTH_ALL, AUTH_ALL, AUTH_ALL, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_ALL),
	1  => array(AUTH_ALL, AUTH_ALL, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_ALL),
	2  => array(AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_REG),
	3  => array(AUTH_ALL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_MOD, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_ACL),
	4  => array(AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_MOD, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_ACL),
	5  => array(AUTH_ALL, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD),
	6  => array(AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD),
);

$simple_auth_types 		= array('公共', '会员', '会员 [隐身]', '私有', '私有 [隐身]', '版主', '版主 [隐身]');

$forum_auth_fields 		= array('auth_view', 'auth_read', 'auth_post', 'auth_reply', 'auth_edit', 'auth_delete', 'auth_sticky', 'auth_announce', 'auth_marrow', 'auth_vote', 'auth_pollcreate', 'auth_attachments', 'auth_download');

$field_names = array(
	'auth_view' 		=> '浏览权限',
	'auth_read' 		=> '阅读权限',
	'auth_post' 		=> '发表权限',
	'auth_reply' 		=> '回复权限',
	'auth_edit' 		=> '编辑权限',
	'auth_delete' 		=> '删除权限',
	'auth_sticky' 		=> '置顶权限',
	'auth_announce'		=> '公告权限',
	'auth_marrow' 		=> '精华权限',
	'auth_vote' 		=> '投票权限', 
	'auth_pollcreate' 	=> '创建投票',
	'auth_attachments' 	=> '上传附件',
	'auth_download' 	=> '下载附件'
);

$forum_auth_levels 		= array('ALL', 'REG', 'PRIVATE', 'MOD', 'ADMIN');
$forum_auth_const 		= array(AUTH_ALL, AUTH_REG, AUTH_ACL, AUTH_MOD, AUTH_ADMIN);

if(isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]))
{
	$forum_id = (isset($_POST[POST_FORUM_URL])) ? intval($_POST[POST_FORUM_URL]) : intval($_GET[POST_FORUM_URL]);
	$forum_sql = "AND forum_id = $forum_id";
}
else
{
	unset($forum_id);
	$forum_sql = '';
}

if( isset($_GET['adv']) )
{
	$adv = intval($_GET['adv']);
}
else
{
	unset($adv);
}

if( isset($_POST['submit']) )
{
	$sql = '';

	if(!empty($forum_id))
	{
		if(isset($_POST['simpleauth']))
		{
			$simple_ary = $simple_auth_ary[intval($_POST['simpleauth'])];

			for($i = 0; $i < count($simple_ary); $i++)
			{
				$sql .= ( ( $sql != '' ) ? ', ' : '' ) . $forum_auth_fields[$i] . ' = ' . $simple_ary[$i];
			}

			if (is_array($simple_ary))
			{
				$sql = 'UPDATE ' . FORUMS_TABLE . " 
					SET $sql 
					WHERE forum_id = $forum_id";
			}
		}
		else
		{
			for($i = 0; $i < count($forum_auth_fields); $i++)
			{
				$value = intval($_POST[$forum_auth_fields[$i]]);

				if ( $forum_auth_fields[$i] == 'auth_vote' )
				{
					if ( $_POST['auth_vote'] == AUTH_ALL )
					{
						$value = AUTH_REG;
					}
				}

				$sql .= ( ( $sql != '' ) ? ', ' : '' ) .$forum_auth_fields[$i] . ' = ' . $value;
			}

			$sql = 'UPDATE ' . FORUMS_TABLE . " 
				SET $sql 
				WHERE forum_id = $forum_id";
		}

		if ( $sql != '' )
		{
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not update auth table', E_USER_WARNING);
			}
		}

		$forum_sql = '';
		$adv = 0;
	}

	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_forumauth.php?" . POST_FORUM_URL . "=$forum_id") . '">')
	);
	$message = '更新成功<br />点击 <a href="' . append_sid('admin_forumauth.php') . '">这里</a> 返回论坛选择页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 超级面板';
	trigger_error($message);
} 

$sql = 'SELECT f.*
	FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . " c
	WHERE c.cat_id = f.cat_id
	$forum_sql
	ORDER BY c.cat_order ASC, f.forum_order ASC";
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Couldn\'t obtain forum list', E_USER_WARNING);
}

$forum_rows = $db->sql_fetchrowset($result);
$db->sql_freeresult($result);

if( empty($forum_id) )
{

	$template->set_filenames(array(
		'body' => 'admin/auth_select_body.tpl')
	);

	if (count($forum_rows) <= 0)
	{
		$select_list = '您似乎没有建立任何论坛';
	}
	else
	{
		$select_list = '<select name="' . POST_FORUM_URL . '">';
		for($i = 0; $i < count($forum_rows); $i++)
		{
			$select_list .= '<option value="' . $forum_rows[$i]['forum_id'] . '">' . $forum_rows[$i]['forum_name'] . '</option>';
		}
		$select_list .= '</select>';
		
		$template->assign_block_vars('forum_auth_select', array());
	}

	$template->assign_vars(array(
		'L_AUTH_TITLE'	=> '论坛权限',
		'L_AUTH_SELECT'	=> '选择论坛',
		'S_AUTH_ACTION' => append_sid('admin_forumauth.php'),
		'S_AUTH_SELECT' => $select_list)
	);

}
else
{

	$template->set_filenames(array(
		'body' => 'admin/auth_forum_body.tpl')
	);

	$forum_name = $forum_rows[0]['forum_name'];

	@reset($simple_auth_ary);
	foreach($simple_auth_ary as $key => $auth_levels)
	{
		$matched = 1;
		for($k = 0; $k < count($auth_levels); $k++)
		{
			$matched_type = $key;

			if ( $forum_rows[0][$forum_auth_fields[$k]] != $auth_levels[$k] )
			{
				$matched = 0;
			}
		}

		if ( $matched )
		{
			break;
		}
	}

	if ( !isset($adv) && !$matched )
	{
		$adv = 1;
	}

	if ( empty($adv) )
	{
		$simple_auth = '<select name="simpleauth">';

		for($j = 0; $j < count($simple_auth_types); $j++)
		{
			$selected = ( $matched_type == $j ) ? ' selected="selected"' : '';
			$simple_auth .= '<option value="' . $j . '"' . $selected . '>' . $simple_auth_types[$j] . '</option>';
		}

		$simple_auth .= '</select>';
		
		$template->assign_block_vars('forum_auth_titles', array(
			'ROW_CLASS'				=> '',
			'CELL_TITLE'			=> '简洁权限',
			'S_AUTH_LEVELS_SELECT' 	=> $simple_auth)
		);
	}
	else
	{

		$lang = array(
			'Forum_ALL' 	=> '全部',
			'Forum_REG' 	=> '注册',
			'Forum_PRIVATE' => '浏览',
			'Forum_MOD' 	=> '版主',
			'Forum_ADMIN' 	=> '管理员'
		);
		for($j = 0; $j < count($forum_auth_fields); $j++)
		{
			$custom_auth[$j] = '&nbsp;<select name="' . $forum_auth_fields[$j] . '">';

			for($k = 0; $k < count($forum_auth_levels); $k++)
			{
				$selected = ( $forum_rows[0][$forum_auth_fields[$j]] == $forum_auth_const[$k] ) ? ' selected="selected"' : '';
				$custom_auth[$j] .= '<option value="' . $forum_auth_const[$k] . '"' . $selected . '>' . $lang['Forum_' . $forum_auth_levels[$k]] . '</option>';
			}
			$custom_auth[$j] .= '</select>&nbsp;';

			$cell_title 	= $field_names[$forum_auth_fields[$j]];
			$row_class 		= ( !($j % 2) ) ? 'row1' : 'row2';
			$template->assign_block_vars('forum_auth_titles', array(
				'ROW_CLASS'				=> $row_class,
				'CELL_TITLE' 			=> $cell_title,
				'S_AUTH_LEVELS_SELECT' 	=> $custom_auth[$j])
			);
		}
	}

	$adv_mode 			= ( empty($adv) ) ? '1' : '0';
	$switch_mode 		= append_sid('admin_forumauth.php?' . POST_FORUM_URL . '=' . $forum_id . '&adv='. $adv_mode);
	$switch_mode_text 	= ( empty($adv) ) ? '展开' : '收缩';
	$u_switch_mode 		= '【<a href="' . $switch_mode . '">' . $switch_mode_text . '</a>】';

	$s_hidden_fields 	= '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '">';

	$template->assign_vars(array(
		'FORUM_NAME' 			=> $forum_name,
		'U_SWITCH_MODE' 		=> $u_switch_mode,
		'U_AUTH_SELECT'			=> append_sid('admin_forumauth.php'),
		'S_FORUMAUTH_ACTION' 	=> append_sid('admin_forumauth.php'),
		'S_HIDDEN_FIELDS' 		=> $s_hidden_fields)
	);

}

page_header();

$template->pparse('body');

page_footer();
?>