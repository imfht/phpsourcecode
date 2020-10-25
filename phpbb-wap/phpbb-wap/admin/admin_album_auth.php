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
	$module['相册']['权限'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

if( !isset($_POST['submit']) )
{
	$sql = "SELECT cat_id, cat_title, cat_order
			FROM ". ALBUM_CAT_TABLE ."
			ORDER BY cat_order ASC";
	if( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法获取相册分类信息', E_USER_WARNING);
	}

	$catrows = array();

	while( $row = $db->sql_fetchrow($result) )
	{
		$catrows[] = $row;
	}

	for ($i = 0; $i < count($catrows); $i++)
	{
		$template->assign_block_vars('catrow', array(
			'CAT_ID' 	=> $catrows[$i]['cat_id'],
			'CAT_TITLE'	=> $catrows[$i]['cat_title'])
		);
	}

	$template->set_filenames(array(
		'body' => 'admin/album_cat_select_body.tpl')
	);

	$template->assign_vars(array(
		'S_ALBUM_ACTION' 		=> append_sid("admin_album_auth.php"))
	);

	$template->pparse('body');

	page_footer();
}
else
{
	if( !isset($_GET['cat_id']) )
	{

		if (!isset($_POST['cat_id']))
		{
			trigger_error('请指定分类ID');
		}

		$cat_id = intval($_POST['cat_id']);

		$template->set_filenames(array(
			'body' => 'admin/album_auth_body.tpl')
		);

		$template->assign_vars(array(
			'U_ALBUM_AUTH_SELECT'		=> append_sid("admin_album_auth.php"),
			'S_ALBUM_ACTION' 			=> append_sid("admin_album_auth.php?cat_id=$cat_id"),
			)
		);

		$sql = "SELECT group_id, group_name
				FROM " . GROUPS_TABLE . "
				WHERE group_single_user <> " . TRUE ."
				ORDER BY group_name ASC";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法获取用户组信息', E_USER_WARNING);
		}

		$groupdata = array();
		while( $row = $db->sql_fetchrow($result) )
		{
			$groupdata[] = $row;
		}

		$sql = "SELECT cat_id, cat_title, cat_view_groups, cat_upload_groups, cat_rate_groups, cat_comment_groups, cat_edit_groups, cat_delete_groups, cat_moderator_groups
				FROM ". ALBUM_CAT_TABLE ."
				WHERE cat_id = '$cat_id'";
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法获取相册分类信息', E_USER_WARNING);
		}

		$thiscat = $db->sql_fetchrow($result);

		$view_groups = @explode(',', $thiscat['cat_view_groups']);
		$upload_groups = @explode(',', $thiscat['cat_upload_groups']);
		$rate_groups = @explode(',', $thiscat['cat_rate_groups']);
		$comment_groups = @explode(',', $thiscat['cat_comment_groups']);
		$edit_groups = @explode(',', $thiscat['cat_edit_groups']);
		$delete_groups = @explode(',', $thiscat['cat_delete_groups']);

		$moderator_groups = @explode(',', $thiscat['cat_moderator_groups']);

		for ($i = 0; $i < count($groupdata); $i++)
		{
			$template->assign_block_vars('grouprow', array(
				'GROUP_ID' => $groupdata[$i]['group_id'],
				'GROUP_NAME' => $groupdata[$i]['group_name'],

				'VIEW_CHECKED' => (in_array($groupdata[$i]['group_id'], $view_groups)) ? 'checked="checked"' : '',

				'UPLOAD_CHECKED' => (in_array($groupdata[$i]['group_id'], $upload_groups)) ? 'checked="checked"' : '',

				'RATE_CHECKED' => (in_array($groupdata[$i]['group_id'], $rate_groups)) ? 'checked="checked"' : '',

				'COMMENT_CHECKED' => (in_array($groupdata[$i]['group_id'], $comment_groups)) ? 'checked="checked"' : '',

				'EDIT_CHECKED' => (in_array($groupdata[$i]['group_id'], $edit_groups)) ? 'checked="checked"' : '',

				'DELETE_CHECKED' => (in_array($groupdata[$i]['group_id'], $delete_groups)) ? 'checked="checked"' : '',

				'MODERATOR_CHECKED' => (in_array($groupdata[$i]['group_id'], $moderator_groups)) ? 'checked="checked"' : '')
			);
		}

		$template->pparse('body');

		page_footer();
	}
	else
	{
		$cat_id = intval($_GET['cat_id']);

		$view_groups = @implode(',', $_POST['view']);
		$upload_groups = @implode(',', $_POST['upload']);
		$rate_groups = @implode(',', $_POST['rate']);
		$comment_groups = @implode(',', $_POST['comment']);
		$edit_groups = @implode(',', $_POST['edit']);
		$delete_groups = @implode(',', $_POST['delete']);

		$moderator_groups = @implode(',', $_POST['moderator']);

		$sql = "UPDATE ". ALBUM_CAT_TABLE ."
				SET cat_view_groups = '$view_groups', cat_upload_groups = '$upload_groups', cat_rate_groups = '$rate_groups', cat_comment_groups = '$comment_groups', cat_edit_groups = '$edit_groups', cat_delete_groups = '$delete_groups',	cat_moderator_groups = '$moderator_groups'
				WHERE cat_id = '$cat_id'";
		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法更新相册分组信息', E_USER_WARNING);
		}

		$message = '相册权限成功更新<br />点击 <a href="' . append_sid("admin_album_auth.php") . '">这里</a> 返回相册权限页面<br />点击 <a href="' . append_sid("index.php?pane=right") . '">这里</a> 返回超级面板';

		trigger_error($message);
	}
}

?>