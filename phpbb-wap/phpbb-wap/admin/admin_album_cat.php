<?php
/*****************************************
 *		admin_album_cat.php
 *		-------------------
 *   	Разработка: (C) 2003 Smartor
 *   	Модификация: Гутник Игорь ( чел )
 *		简体中文：爱疯的云
 *		描述：相册的分类管理
 ******************************************/



if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['相册']['分类'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

function reorder_cat()
{
	global $db;

	$sql = "SELECT cat_id, cat_order
			FROM ". ALBUM_CAT_TABLE ."
			WHERE cat_id <> 0
			ORDER BY cat_order ASC";
	if( !$result = $db->sql_query($sql) )
	{
		trigger_error('Could not get list of Categories', E_USER_WARNING);
	}

	$i = 10;

	while( $row = $db->sql_fetchrow($result) )
	{
		$sql = "UPDATE ". ALBUM_CAT_TABLE ."
				SET cat_order = $i
				WHERE cat_id = ". $row['cat_id'];
		if( !$db->sql_query($sql) )
		{
			trigger_error('Could not update order fields', E_USER_WARNING);
		}
		$i += 10;
	}
}

if( !isset($_POST['mode']) )
{
	if( !isset($_GET['action']) )
	{
		$template->set_filenames(array(
			'body' => 'admin/album_cat_body.tpl')
		);

		$sql = "SELECT *
				FROM ". ALBUM_CAT_TABLE ."
				ORDER BY cat_order ASC";
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('Could not query Album Categories information', E_USER_WARNING);
		}

		$catrow = array();

		while ($row = $db->sql_fetchrow($result))
		{
			$catrow[] = $row;
		}

		for( $i = 0; $i < count($catrow); $i++ )
		{
			$template->assign_block_vars('catrow', array(
				'ROW_CLASS' => ($i % 2) ? 'row1' : 'row2',
				'TITLE' => $catrow[$i]['cat_title'],
				'DESC' => $catrow[$i]['cat_desc'],
				'S_MOVE_UP' => append_sid("admin_album_cat.php?action=move&amp;move=-15&amp;cat_id=" . $catrow[$i]['cat_id']),
				'S_MOVE_DOWN' => append_sid("admin_album_cat.php?action=move&amp;move=15&amp;cat_id=" . $catrow[$i]['cat_id']),
				'S_EDIT_ACTION' => append_sid("admin_album_cat.php?action=edit&amp;cat_id=" . $catrow[$i]['cat_id']),
				'S_DELETE_ACTION' => append_sid("admin_album_cat.php?action=delete&amp;cat_id=" . $catrow[$i]['cat_id'])
				)
			);
		}

		$template->pparse('body');

		page_footer();
	}
	else
	{
		if( $_GET['action'] == 'edit' )
		{
			$cat_id = intval($_GET['cat_id']);

			$sql = "SELECT *
					FROM ". ALBUM_CAT_TABLE ."
					WHERE cat_id = '$cat_id'";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not query Album Categories information', E_USER_WARNING);
			}
			if( $db->sql_numrows($result) == 0 )
			{
				trigger_error('The requested category is not existed');
			}
			$catrow = $db->sql_fetchrow($result);

			$template->set_filenames(array(
				'body' => 'admin/album_cat_new_body.tpl')
			);

			$template->assign_vars(array(

				'U_ALBUM_LISTS' => append_sid("admin_album_cat.php"),
				
				'VIEW_GUEST' => ($catrow['cat_view_level'] == ALBUM_GUEST) ? 'selected="selected"' : '',
				'VIEW_REG' => ($catrow['cat_view_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'VIEW_PRIVATE' => ($catrow['cat_view_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'VIEW_MOD' => ($catrow['cat_view_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'VIEW_ADMIN' => ($catrow['cat_view_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'UPLOAD_GUEST' => ($catrow['cat_upload_level'] == ALBUM_GUEST) ? 'selected="selected"' : '',
				'UPLOAD_REG' => ($catrow['cat_upload_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'UPLOAD_PRIVATE' => ($catrow['cat_upload_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'UPLOAD_MOD' => ($catrow['cat_upload_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'UPLOAD_ADMIN' => ($catrow['cat_upload_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'RATE_GUEST' => ($catrow['cat_rate_level'] == ALBUM_GUEST) ? 'selected="selected"' : '',
				'RATE_REG' => ($catrow['cat_rate_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'RATE_PRIVATE' => ($catrow['cat_rate_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'RATE_MOD' => ($catrow['cat_rate_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'RATE_ADMIN' => ($catrow['cat_rate_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'COMMENT_GUEST' => ($catrow['cat_comment_level'] == ALBUM_GUEST) ? 'selected="selected"' : '',
				'COMMENT_REG' => ($catrow['cat_comment_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'COMMENT_PRIVATE' => ($catrow['cat_comment_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'COMMENT_MOD' => ($catrow['cat_comment_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'COMMENT_ADMIN' => ($catrow['cat_comment_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'EDIT_REG' => ($catrow['cat_edit_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'EDIT_PRIVATE' => ($catrow['cat_edit_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'EDIT_MOD' => ($catrow['cat_edit_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'EDIT_ADMIN' => ($catrow['cat_edit_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'DELETE_REG' => ($catrow['cat_delete_level'] == ALBUM_USER) ? 'selected="selected"' : '',
				'DELETE_PRIVATE' => ($catrow['cat_delete_level'] == ALBUM_PRIVATE) ? 'selected="selected"' : '',
				'DELETE_MOD' => ($catrow['cat_delete_level'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'DELETE_ADMIN' => ($catrow['cat_delete_level'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'APPROVAL_DISABLED' => ($catrow['cat_approval'] == ALBUM_USER) ? 'selected="selected"' : '',
				'APPROVAL_MOD' => ($catrow['cat_approval'] == ALBUM_MOD) ? 'selected="selected"' : '',
				'APPROVAL_ADMIN' => ($catrow['cat_approval'] == ALBUM_ADMIN) ? 'selected="selected"' : '',

				'S_ALBUM_ACTION' => append_sid("admin_album_cat.php?cat_id=$cat_id"),
				'S_CAT_TITLE' => $catrow['cat_title'],
				'S_CAT_DESC' => $catrow['cat_desc'],
				'S_MODE' => 'edit',

				'S_GUEST' => ALBUM_GUEST,
				'S_USER' => ALBUM_USER,
				'S_PRIVATE' => ALBUM_PRIVATE,
				'S_MOD' => ALBUM_MOD,
				'S_ADMIN' => ALBUM_ADMIN)
			);

			$template->pparse('body');

			page_footer();
		}
		else if( $_GET['action'] == 'delete' )
		{
			$cat_id = intval($_GET['cat_id']);

			$sql = "SELECT cat_id, cat_title, cat_order
					FROM ". ALBUM_CAT_TABLE ."
					ORDER BY cat_order ASC";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not query Album Categories information', E_USER_WARNING);
			}

			$cat_found = FALSE;
			while( $row = $db->sql_fetchrow($result) )
			{
				if( $row['cat_id'] == $cat_id )
				{
					$thiscat = $row;
					$cat_found = TRUE;
				}
				else
				{
					$catrow[] = $row;
				}
			}
			if( $cat_found == FALSE )
			{
				trigger_error('The requested category is not existed');
			}

			$select_to = '<select name="target"><option value="0">删除所有</option>';
			for ($i = 0; $i < count($catrow); $i++)
			{
				$select_to .= '<option value="'. $catrow[$i]['cat_id'] .'">'. $catrow[$i]['cat_title'] .'</option>';
			}
			$select_to .= '</select>';

			$template->set_filenames(array(
				'body' => 'admin/album_cat_delete_body.tpl')
			);

			$template->assign_vars(array(
				'U_ALBUM_LISTS' => append_sid("admin_album_cat.php"),
				
				'S_CAT_TITLE' => $thiscat['cat_title'],
				'S_ALBUM_ACTION' => append_sid("admin_album_cat.php?cat_id=$cat_id"),
				'S_SELECT_TO' => $select_to)
			);

			$template->pparse('body');

			page_footer();
		}
		else if( $_GET['action'] == 'move' )
		{
			$cat_id = intval($_GET['cat_id']);
			$move = intval($_GET['move']);

			$sql = "UPDATE ". ALBUM_CAT_TABLE ."
					SET cat_order = cat_order + $move
					WHERE cat_id = $cat_id";
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Could not change category order', E_USER_WARNING);
			}

			reorder_cat();

			$message = "更新成功<br />点击 <a href=\"" . append_sid("admin_album_cat.php") . "\">这里</a> 返回权限管理页面<br />点击 <a href=\"" . append_sid("index.php?pane=right") . "\">这里</a> 返回超级管理面板首页";

			trigger_error($message);
		}
	}
}
else
{
	if( $_POST['mode'] == 'new' )
	{
		if( !isset($_POST['cat_title']) )
		{
			$template->set_filenames(array(
				'body' => 'admin/album_cat_new_body.tpl')
			);

			$template->assign_vars(array(

				'VIEW_GUEST' => 'selected="selected"',
				'UPLOAD_REG' => 'selected="selected"',
				'RATE_REG' => 'selected="selected"',
				'COMMENT_REG' => 'selected="selected"',
				'EDIT_REG' => 'selected="selected"',
				'DELETE_MOD' => 'selected="selected"',
				'APPROVAL_DISABLED' => 'selected="selected"',

				'S_MODE' => 'new',

				'S_GUEST' => ALBUM_GUEST,
				'S_USER' => ALBUM_USER,
				'S_PRIVATE' => ALBUM_PRIVATE,
				'S_MOD' => ALBUM_MOD,
				'S_ADMIN' => ALBUM_ADMIN)
			);

			$template->pparse('body');

			page_footer();
		}
		else
		{
			$cat_title = str_replace("\'", "''", htmlspecialchars(trim($_POST['cat_title'])));
			$cat_desc = str_replace("\'", "''", trim($_POST['cat_desc']));
			$view_level = intval($_POST['cat_view_level']);
			$upload_level = intval($_POST['cat_upload_level']);
			$rate_level = intval($_POST['cat_rate_level']);
			$comment_level = intval($_POST['cat_comment_level']);
			$edit_level = intval($_POST['cat_edit_level']);
			$delete_level = intval($_POST['cat_delete_level']);
			$cat_approval = intval($_POST['cat_approval']);

			if (empty($cat_title))
			{
				trigger_error('分类名称不能为空');
			}

			$sql = "SELECT cat_order FROM ". ALBUM_CAT_TABLE ."
					ORDER BY cat_order DESC
					LIMIT 1";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not query Album Categories information', E_USER_WARNING);
			}
			$row = $db->sql_fetchrow($result);
			$last_order = $row['cat_order'];
			$cat_order = $last_order + 10;

			$sql = "INSERT INTO ". ALBUM_CAT_TABLE ." (cat_title, cat_desc, cat_order, cat_view_level, cat_upload_level, cat_rate_level, cat_comment_level, cat_edit_level, cat_delete_level, cat_approval)
					VALUES ('$cat_title', '$cat_desc', '$cat_order', '$view_level', '$upload_level', '$rate_level', '$comment_level', '$edit_level', '$delete_level', '$cat_approval')";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not create new Album Category', E_USER_WARNING);
			}

			$message = "分类创建成功<br />点击 <a href=\"" . append_sid("admin_album_cat.php") . "\">这里</a> 返回权限管理页面<br />点击 <a href=\"" . append_sid("index.php?pane=right") . "\">这里</a> 返回超级管理面板首页";

			trigger_error($message);
		}
	}
	else if( $_POST['mode'] == 'edit' )
	{
		$cat_id = intval($_GET['cat_id']);
		$cat_title = str_replace("\'", "''", htmlspecialchars(trim($_POST['cat_title'])));
		$cat_desc = str_replace("\'", "''", trim($_POST['cat_desc']));
		$view_level = intval($_POST['cat_view_level']);
		$upload_level = intval($_POST['cat_upload_level']);
		$rate_level = intval($_POST['cat_rate_level']);
		$comment_level = intval($_POST['cat_comment_level']);
		$edit_level = intval($_POST['cat_edit_level']);
		$delete_level = intval($_POST['cat_delete_level']);
		$cat_approval = intval($_POST['cat_approval']);

		if (empty($cat_title))
		{
			trigger_error('分类名称不能为空');
		}

		$sql = "UPDATE ". ALBUM_CAT_TABLE ."
				SET cat_title = '$cat_title', cat_desc = '$cat_desc', cat_view_level = '$view_level', cat_upload_level = '$upload_level', cat_rate_level = '$rate_level', cat_comment_level = '$comment_level', cat_edit_level = '$edit_level', cat_delete_level = '$delete_level', cat_approval = '$cat_approval'
				WHERE cat_id = '$cat_id'";
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('Could not update this Album Category', E_USER_WARNING);
		}

		$message = "分类修改成功<br />点击 <a href=\"" . append_sid("admin_album_cat.php") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("index.php?pane=right") . "\">这里</a> 返回后台首页";

		trigger_error($message);
	}
	else if( $_POST['mode'] == 'delete' )
	{
		$cat_id = intval($_GET['cat_id']);
		$target = intval($_POST['target']);

		if( $target == 0 ) // Delete All
		{
			$sql = "SELECT pic_id, pic_filename, pic_thumbnail, pic_cat_id
					FROM ". ALBUM_TABLE ."
					WHERE pic_cat_id = '$cat_id'";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not query Album information', E_USER_WARNING);
			}
			$picrow = array();
			while( $row = $db ->sql_fetchrow($result) )
			{
				$picrow[] = $row;
				$pic_id_row[] = $row['pic_id'];
			}

			if( count($picrow) != 0 ) // if this category is not empty
			{
				for ($i = 0; $i < count($picrow); $i++)
				{
					@unlink('../' . ALBUM_CACHE_PATH . $picrow[$i]['pic_thumbnail']);

					@unlink('../' . ALBUM_UPLOAD_PATH . $picrow[$i]['pic_filename']);
				}

				$pic_id_sql = '(' . implode(',', $pic_id_row) . ')';

				$sql = "DELETE FROM ". ALBUM_RATE_TABLE ."
						WHERE rate_pic_id IN ". $pic_id_sql;
				if(!$result = $db->sql_query($sql))
				{
					trigger_error('Could not delete Ratings information', E_USER_WARNING);
				}

				$sql = "DELETE FROM ". ALBUM_COMMENT_TABLE ."
						WHERE comment_pic_id IN ". $pic_id_sql;
				if(!$result = $db->sql_query($sql))
				{
					trigger_error('Could not delete Comments information', E_USER_WARNING);
				}

				$sql = "DELETE FROM ". ALBUM_TABLE ."
						WHERE pic_cat_id = '$cat_id'";
				if(!$result = $db->sql_query($sql))
				{
					trigger_error('Could not delete pic entries in the DB', E_USER_WARNING);
				}
			}

			$sql = "DELETE FROM ". ALBUM_CAT_TABLE ."
					WHERE cat_id = '$cat_id'";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not delete this Category', E_USER_WARNING);
			}

			reorder_cat();
			$message = "分类已删除<br />点击 <a href=\"" . append_sid("admin_album_cat.php") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("index.php?pane=right") . "\">这里</a> 返回后台首页";

			trigger_error($message);
		}
		else
		{
			$sql = "UPDATE ". ALBUM_TABLE ."
					SET pic_cat_id = '$target'
					WHERE pic_cat_id = '$cat_id'";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not update this Category content', E_USER_WARNING);
			}

			$sql = "DELETE FROM ". ALBUM_CAT_TABLE ."
					WHERE cat_id = '$cat_id'";
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not delete this Category', E_USER_WARNING);
			}

			reorder_cat();
			$message = "分类已删除<br />点击 <a href=\"" . append_sid("admin_album_cat.php") . "\">这里</a> 返回相册分类页面<br />点击 <a href=\"" . append_sid("index.php?pane=right") . "\">这里</a> 返回后台首页";

			trigger_error($message);
		}
	}
}

?>