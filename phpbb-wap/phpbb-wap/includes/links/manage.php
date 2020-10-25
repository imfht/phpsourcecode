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

$submit = isset($_POST['submit']) ? true : false;
$id_list	= get_var('id_list', array(0));

if ($submit && count($id_list) > 0)
{

	$sql = 'DELETE FROM ' . LINKS_TABLE . '
		WHERE link_id IN (' . implode(', ', $id_list) . ')
			AND link_admin_user = ' . (int) $userdata['user_id'];

	if (!$db->sql_query($sql))
	{
		trigger_error('无法删除友链', E_USER_WARNING);
	}	

	trigger_error('删除成功' . back_link(append_sid('links.php?mode=manage')), E_USER_ERROR);

}

$sql = 'SELECT link_id, link_title
	FROM ' . LINKS_TABLE . '
	WHERE link_admin_user = ' . $userdata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法取得您的友链信息', E_USER_WARNING);
}

if ($db->sql_numrows($result))
{
	$i = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('your_links', array(
			'NUMBER'	=> $i + 1,
			'LINK_TITLE' => $row['link_title'],
			'LINK_ID'	=> $row['link_id'],
			'U_EDIT_LINK' => append_sid('links.php?mode=edit&id=' . $row['link_id']))
		);

		$i++;
	}

	$total_links = $i;
}
else
{
	$total_links = 0;
	$template->assign_block_vars('your_not_link', array());
}

page_header('您的申请的友链列表');

$template->assign_vars(array(
	'TOTAL_LINKS' => $total_links,
	'U_BACK' => append_sid('links.php'))
);

$template->set_filenames(array(
	'manage' => 'links/links_manage.tpl')
);

$template->pparse('manage');

page_footer();
?>