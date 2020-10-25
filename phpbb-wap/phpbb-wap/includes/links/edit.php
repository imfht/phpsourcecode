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

if (!defined('IN_PHPBB')) exit;

$link_id = get_var('id', 0);

$sql = 'SELECT * 
	FROM ' . LINKS_TABLE . '
	WHERE link_id = ' . (int)$link_id;
if (!$result = $db->sql_query($sql))
{
	trigger_error('无法读取友链信息', E_USER_WARNING);
}

if (!$row = $db->sql_fetchrow($result))
{
	trigger_error('友链不存在' . back_link(append_sid('links.php?mode=manage')), E_USER_ERROR);
}

if ($row['link_admin_user'] !== $userdata['user_id'])
{
	trigger_error('该网站不是你申请的' . back_link(append_sid('links.php?mode=manage')), E_USER_ERROR);
}

$submit = isset($_POST['submit']) ? true : false;

$error = false;
$error_message = '';

if ($submit)
{
	$link_title = get_var('title', '');
	$link_name = get_var('name', '');
	$link_url = get_var('url', '');
	$link_cat = get_var('cat', 0);
	$link_desc = get_var('desc', '');

	if ($link_title == '' || mb_strlen($link_title, 'UTF-8') > 8)
	{
		$error = true;
		$error_message .= '<p>网站的名称必须在1~8个字符以内</p>';
	}

	if ($link_title == '' || mb_strlen($link_name, 'UTF-8') > 2)
	{
		$error = true;
		$error_message .= '<p>网站的简称不能大于两个字符</p>';
	}

	if (!filter_var($link_url, FILTER_VALIDATE_URL))
	{
		$error = true;
		$error_message .= '<p>网站地址不合法</p>';
	}

	$sql = 'SELECT linkclass_id
		FROM ' . LINKCLASS_TABLE . '
		WHERE linkclass_id = ' . $link_cat;
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询友链分类信息', E_USER_WARNING);
	}

	if (!$row = $db->sql_fetchrow($result))
	{
		$error = true;
		$error_message = '<p>网站类型指定不正确/p>';				
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
		WHERE link_id = ' . (int)$link_id;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法更新友链信息', E_USER_WARNING);
	}

	trigger_error('修改成功' . back_link(append_sid('links.php?mode=edit&id=' . $link_id)), E_USER_ERROR);
}

$sql = 'SELECT linkclass_id, linkclass_name
	FROM ' . LINKCLASS_TABLE;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询分类信息', E_USER_WARNING);
}

$cat_select = '<select name="cat">';
while ($cat = $db->sql_fetchrow($result))
{
	$selected = ( $cat['linkclass_id'] == (int)$link_id ) ? ' selected="selected"' : '';
	$cat_select .= '<option value="' . $cat['linkclass_id'] . '"' . $selected . '>' . $cat['linkclass_name'] . '</option>';
}
$cat_select .= '</select>';

page_header($row['link_title']);

$template->set_filenames(array(
	'edit' => 'links/link_edit.tpl')
);

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
	'S_ACTION'		=> append_sid('links.php?mode=edit&id=' . $link_id))
);

$template->pparse('edit');	

page_footer();
?>