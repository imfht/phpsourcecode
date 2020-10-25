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

if (!defined('IN_PHPBB')) exit;;

$view_id = get_var('id', 0);

$sql = 'SELECT link_id, link_class_id, link_url, link_title, link_desc, link_join_time, link_last_visit, link_in, link_out, link_show
	FROM ' . LINKS_TABLE . '
	WHERE link_id = ' . (int) $view_id;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法取得友链信息', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

if (!$row['link_show'])
{
	trigger_error('该网站还没审核' . back_link(append_sid('links.php')), E_USER_ERROR);
}

$template->assign_vars(array(
	'LINK_TITLE' => $row['link_title'],
	'LINK_URL'	=> $row['link_url'],
	'U_LINK_OUT' => append_sid('links.php?mode=out&id=' . $row['link_id']),
	'LINK_DESC'	=> str_replace(PHP_EOL, '</p><p>', $row['link_desc']),
	'LINK_JOIN'	=> create_date($userdata['user_dateformat'], $row['link_join_time'], $userdata['user_timezone']),
	'LINK_LAST_VISIT' => create_date($userdata['user_dateformat'], $row['link_last_visit'], $userdata['user_timezone']),
	'LINK_IN'	=> $row['link_in'],
	'LINK_OUT'	=> $row['link_out'],
	'U_BACK'	=> append_sid('links.php?mode=cat&id=' . $row['link_class_id']))
);

page_header($row['link_title']);

$template->set_filenames(array(
	'view' => 'links/link_view.tpl')
);

$template->pparse('view');

page_footer();
?>