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

$per = 10;
$start = get_pagination_start($per);	
$cat_id = get_var('id', 0);

$sql = 'SELECT linkclass_name 
	FROM ' . LINKCLASS_TABLE . '
	WHERE linkclass_id = ' . (int)$cat_id;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获得分类信息', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

$cat_name = $row['linkclass_name'];

$sort = get_var('sort', 0);
$link_show_sql = ' AND link_show = 1';
// 入
if ($sort == 1) {
	$sort_sql = ' ORDER BY link_in DESC';
}
// 审核中的网站
elseif ($sort == 2) {
	$sort_sql = ' ORDER BY link_id DESC';
	$link_show_sql = ' AND link_show = 0';
}
// 动态排行
else
{
	$sort_sql = ' ORDER BY link_last_visit DESC';
}

$sql = 'SELECT link_id, link_title
	FROM ' . LINKS_TABLE . '
	WHERE link_class_id = ' . (int)$cat_id . 
		$link_show_sql . 
	$sort_sql . '
	LIMIT ' . $start . ', ' . $per;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获取友链信息', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('cat_links', array(
		'NUMBER'		=> $i + $start + 1,
		'ROW_CLASS'		=> $row_class,
		'LINK_TITLE' 	=> $row['link_title'],
		'U_LINK'		=> append_sid('links.php?mode=view&id=' . $row['link_id']))
	);
	$i++;
}

$sql = 'SELECT COUNT(link_id) AS link_total 
	FROM ' . LINKS_TABLE . '
	WHERE link_class_id = ' . (int)$cat_id . 
		$link_show_sql;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计友链信息', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

if (!$row['link_total'])
{
	$template->assign_block_vars('not_links', array());
}

$s_hidden = '<input type="hidden" name="id" value="' . $cat_id . '">';
$s_hidden .= '<input type="hidden" name="mode" value="cat">';

$template->assign_vars(array(
	'U_JOIN' 		=> append_sid('links.php?mode=join'),
	'S_ACTION'		=> append_sid('links.php'),
	'U_BACK'		=> append_sid('links.php'),
	'S_HIDDEN'		=> $s_hidden,
	'PAGINATION' 	=> generate_pagination('links.php?mode=cat&id=' . $cat_id . '&sort=' . $sort, $row['link_total'], $per, $start))
);

page_header($cat_name);

$template->set_filenames(array(
	'body' => 'links/links_cat.tpl')
);

$template->pparse('body');

page_footer();
?>