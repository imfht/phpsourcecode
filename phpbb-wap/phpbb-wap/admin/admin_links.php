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
	$module['系统']['友情链接'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

$mode = get_var('mode', '');

if($mode == 'edit'){

	$id = isset($_GET['id']) ? $_GET['id'] : false;		//取得id的值
	if($id == false){
		trigger_error('未获取到ID值!', E_USER_WARNING);	//如果未能成功取值
	}

	if(isset($_POST['submit_edit'])){		//如果有post的数据的话

		$link_title = get_var('title', '');
		$link_name = get_var('name', '');
		$link_url = get_var('url', '');
		$link_cat = get_var('cat', 0);
		$link_desc = get_var('desc', '');

		if ($link_title == '' || mb_strlen($link_title, 'UTF-8') > 8){
			$error = true;
			$error_message .= '<p>网站的名称必须在1~8个字符以内</p>';
		}

		if ($link_title == '' || mb_strlen($link_name, 'UTF-8') > 2){
			$error = true;
			$error_message .= '<p>网站的简称不能大于两个字符</p>';
		}

		if (!filter_var($link_url, FILTER_VALIDATE_URL)){
			$error = true;
			$error_message .= '<p>网站地址不合法</p>';
		}


		$update_arr = array(
			'link_title' => $link_title,
			'link_name'	=> $link_name,
			'link_url' => $link_url,
			'link_desc' => $link_desc,
			'link_class_id' => $link_cat
		);

		$sql = 'UPDATE ' . LINKS_TABLE . ' 
			SET ' . $db->sql_build_array('UPDATE', $update_arr) . '
			WHERE link_id = ' . (int)$id;

		if (!$db->sql_query($sql)){
			trigger_error('无法更新友链信息', E_USER_WARNING);
		}else{
			trigger_error('修改成功' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php?mode=edit&id='.$id)), E_USER_ERROR);
		}


	}


	$sql = 'SELECT * FROM ' . LINKS_TABLE . " WHERE `link_id` = '$id'";
	if (! $result = $db->sql_query($sql)){
			trigger_error('无法查询友链信息', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);
	$template->assign_vars(array(
		'LINK_NAME' 	=> $row['link_name'],
		'LINK_TITLE' 	=> $row['link_title'],
		'CAT_SELECT' 	=> $cat_select,
		'LINK_URL'		=> $row['link_url'],
		'LINK_DESC'		=> $row['link_desc'],
		'LINK_JOIN'		=> create_date($userdata['user_dateformat'], $row['link_join_time'], $userdata['user_timezone']),
		'LAST_VISIT'	=> create_date($userdata['user_dateformat'], $row['link_last_visit'], $userdata['user_timezone']),
		'LINK_IN'		=> $row['link_in'],
		'LINK_OUT'		=> $row['link_out'],
		'LINK_SHOW'		=> ($row['link_show']) ? '已通过' : '正在审核',
		'U_BACK'		=> append_sid('links.php?mode=manage'),
		'S_ACTION'		=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=edit&id='.$row['link_id']))
	);

	$template->set_filenames(array('edit' => 'admin/links_edit.tpl'));		//模版初始化
	$template->pparse('edit');		//调用模版

	page_footer();
	die();
}

if ($mode == 'ing')
{
	$id_list	= get_var('id_list', array(0));

	if (isset($_POST['delete']) && count($id_list) > 0)
	{
		$sql = 'DELETE FROM ' . LINKS_TABLE . '
			WHERE link_id IN (' . implode(', ', $id_list) . ')';

		if (!$db->sql_query($sql))
		{
			trigger_error('无法删除友链', E_USER_WARNING);
		}	

		trigger_error('操作成功' . back_link(append_sid('admin_links.php')), E_USER_ERROR);
	}
	elseif (isset($_POST['pass']) && count($id_list) > 0)
	{
		$sql = 'UPDATE ' . LINKS_TABLE . ' 
			SET link_show = 1
			WHERE link_id IN(' . implode(', ', $id_list) . ')';
		if (!$db->sql_query($sql))
		{
			trigger_error('无法更新友链信息', E_USER_WARNING);
		}	
		trigger_error('操作成功' . back_link(append_sid('admin_links.php')), E_USER_ERROR);
	}
	else
	{
		trigger_error('您选择的参数有误！' . back_link(append_sid('admin_links.php')), E_USER_ERROR);
	}
}
else if ($mode == 'cat')
{
	$submit = isset($_POST['submit']) ? true : false;

	$cat_id = get_var('id', 0);

	if ($submit)
	{
		if (isset($_POST['delete'])) 
		{
			if ( isset($_POST['cancel']) )
			{
				redirect(append_sid('admin/admin_links.php?mode=cat&id=' . (int) $cat_id, true));
			}

			$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

			if( !$confirm )
			{
				
				$template->set_filenames(array(
					'confirm' => 'confirm_body.tpl')
				);

				$template->assign_vars(array(
					'MESSAGE_TITLE' 	=> '确认删除',
					'MESSAGE_TEXT'		=> '您是否要删除该友链分类？',
					'L_YES' 			=> '是',
					'L_NO' 				=> '否',
					'S_HIDDEN_FIELDS'	=> '<input type="hidden" name="delete" value="true" /><input type="hidden" name="submit" value="true" />',
					'S_CONFIRM_ACTION' 	=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=cat&id=' . (int) $cat_id))
				);

				$template->pparse('confirm');

				page_footer();
			}

			$sql = 'DELETE FROM ' . LINKCLASS_TABLE . ' 
				WHERE linkclass_id = ' . (int) $cat_id;

			if (!$db->sql_query($sql))
			{
				trigger_error('无法删除友链分类', E_USER_WARNING);
			}

			trigger_error('删除成功' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php')), E_USER_ERROR);
		}

		$linkclass_name = get_var('name', '');
		$linkclass_sort = get_var('sort', 0);
		$linkclass_desc = get_var('desc', '');

		if ($linkclass_name == '')
		{
			trigger_error('名称不能为空' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php?mode=cat&id=' . (int)$cat_id)), E_USER_ERROR);
		}

		$sql = 'UPDATE ' . LINKCLASS_TABLE . "
			SET linkclass_name = '$linkclass_name', linkclass_sort = $linkclass_sort, linkclass_desc = '$linkclass_desc'
			WHERE linkclass_id = " . (int) $cat_id;

		if (!$db->sql_query($sql))
		{
			trigger_error('无法更新友链分类信息', E_USER_WARNING);
		}

		trigger_error('保存成功' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php?mode=cat&id=' . (int)$cat_id)), E_USER_ERROR);
	}
	else
	{
		$sql = 'SELECT linkclass_name, linkclass_sort, linkclass_desc 
			FROM ' . LINKCLASS_TABLE . '
			WHERE linkclass_id = ' . (int) $cat_id;

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法取得友链的分类信息', E_USER_WARNING);
		}

		if (!$row = $db->sql_fetchrow($result))
		{
			trigger_error('您选择的分类不存在' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php')), E_USER_ERROR);
		}

		$template->set_filenames(array('body' => 'admin/links_editcat.tpl'));

		$template->assign_block_vars('switch_delete', array());
		
		$template->assign_vars(array(
			'S_ACTION' 			=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=cat&id=' . (int) $cat_id),
			'LINKCLASS_NAME'	=> $row['linkclass_name'],
			'LINKCLASS_SORT'	=> $row['linkclass_sort'],
			'LINKCLASS_DESC'	=> $row['linkclass_desc'],
			'U_ADMIN_LINKS'		=> append_sid(ROOT_PATH . 'admin/admin_links.php'))
		);

		$template->pparse('body');
	}
	
}
elseif ($mode == 'create' )
{
	$submit = isset($_POST['submit']) ? true : false;

	if ($submit)
	{
		$linkclass_name = get_var('name', '');
		$linkclass_sort = get_var('sort', 0);
		$linkclass_desc = get_var('desc', '');

		if ($linkclass_name == '')
		{
			trigger_error('名称不能为空' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php?mode=create')), E_USER_ERROR);
		}

		$sql = 'INSERT INTO ' . LINKCLASS_TABLE . " (linkclass_name, linkclass_desc, linkclass_sort) 
			VALUES ('$linkclass_name', '$linkclass_desc', $linkclass_sort)";

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法新增分类', E_USER_WARNING);
		}

		trigger_error('创建成功' . back_link(append_sid(ROOT_PATH . 'admin/admin_links.php')), E_USER_ERROR);
	}

	$template->set_filenames(array('body' => 'admin/links_editcat.tpl'));

	$template->assign_vars(array(
		'S_ACTION' 			=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=create'),
		'LINKCLASS_NAME'	=> '',
		'LINKCLASS_SORT'	=> 0,
		'LINKCLASS_DESC'	=> '',
		'U_ADMIN_LINKS'		=> append_sid(ROOT_PATH . 'admin/admin_links.php'))
	);

	$template->pparse('body');
}
else
{
	$per = 15;
	$start = get_pagination_start($per);

	// 显示所有分类
	$sql = "SELECT linkclass_id, linkclass_name, linkclass_desc
		FROM " . LINKCLASS_TABLE;

	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法取得友链分类信息！', E_USER_WARNING);
	}

	$catrow = array();

	while ($row = $db->sql_fetchrow($result))
	{
		$catrow[$row['linkclass_id']] = $row['linkclass_name'];
		$template->assign_block_vars('linkcat', array(
			'LINKCLASS_NAME' 	=> $row['linkclass_name'],
			'LINKCLASS_DESC'	=> $row['linkclass_desc'],
			'U_LINKCLASS'		=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=cat&id=' . $row['linkclass_id']))
		);		
	}

	if (count($catrow) == 0)
	{
		$template->assign_block_vars('not_cat', array());
	}

// 显示已审核的友链
	$sql = 'SELECT * 
		FROM ' . LINKS_TABLE . "
		WHERE link_show = 1
		ORDER BY link_join_time DESC
		LIMIT $start , $per";
	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法取得友链信息！', E_USER_WARNING);
	}

	if ($row = $db->sql_numrows($result))
	{
		$template->assign_block_vars('not_links', '');
	}
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('links_pass_row', array(
			'LINK_TITLE' 	=> $row['link_title'],
			'LINK_ID'		=> $row['link_id'],
			'LINK_CAT'		=> (isset($catrow[$row['link_class_id']])) ? $catrow[$row['link_class_id']] : '无分类',
			'LINK_EDIT'		=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=edit&id='.$row['link_id'])
			)
		);
	}



	// 显示未审核的友链
	$sql = 'SELECT * 
		FROM ' . LINKS_TABLE . "
		WHERE link_show = 0
		ORDER BY link_join_time DESC
		LIMIT $start , $per";
	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法取得友链信息！', E_USER_WARNING);
	}

	if ($row = $db->sql_numrows($result))
	{
		$template->assign_block_vars('not_links', '');
	}
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('links_row', array(
			'LINK_TITLE' 	=> $row['link_title'],
			'LINK_ID'		=> $row['link_id'],
			'LINK_CAT'		=> (isset($catrow[$row['link_class_id']])) ? $catrow[$row['link_class_id']] : '无分类',
			'U_LINKS'		=> $row['link_url'])
		);
	}	



	$sql = 'SELECT COUNT(link_id) AS total_links
		FROM ' . LINKS_TABLE . '
		WHERE link_show = 0';
	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法统计友链信息！', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	$total_links = $row['total_links'];

	$template->set_filenames(array('body' => 'admin/links_body.tpl'));

	$pagination = generate_pagination('admin_links.php?', $total_links, $per, $start);

	$template->assign_vars(array(
		'S_ACTION' 		=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=ing'),
		'S_ACTION_EDIT'	=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=edit'),
		'PAGINATION'	=> $pagination,
		'U_CREATE_CAT'	=> append_sid(ROOT_PATH . 'admin/admin_links.php?mode=create'))
	);

	$template->pparse('body');
}

page_footer();
?>