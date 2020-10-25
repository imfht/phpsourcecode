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

page_header('友情链接');

$sql = 'SELECT linkclass_id, linkclass_name
	FROM ' . LINKCLASS_TABLE . '
	ORDER BY linkclass_id, linkclass_sort';

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获得友链分类信息', E_USER_WARNING);
}

$linkclass_data = array();

while ($row = $db->sql_fetchrow($result))
{
	$linkclass_data[] = $row;
}

$i = 0;
foreach ($linkclass_data as $key => $value)
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('linkclass', array(
		'ROW_CLASS'			=> $row_class,
		'LINKCLASS_NAME' 	=> $value['linkclass_name'],
		'U_LINKCLASS'		=> append_sid('links.php?mode=cat&id=' . $value['linkclass_id']))
	);

	$sql = 'SELECT link_id, link_name
		FROM ' . LINKS_TABLE . '
		WHERE link_class_id = ' . (int)$value['linkclass_id'] . '
			AND link_show = 1
		ORDER BY link_last_visit DESC
		LIMIT 5';
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法取得友链数据', E_USER_WARNING);
	}

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('linkclass.links',	array(
			'LINK_NAME'			=> $row['link_name'],
			'U_LINK'			=> append_sid('links.php?mode=view&id=' . $row['link_id']))
		);	
	}

	$i++;
}

$template->assign_vars(array(
	'U_JOIN' => append_sid('links.php?mode=join'),
	'U_BACK' => append_sid('index.php'))
);

$template->set_filenames(array(
	'links' => 'links/links_body.tpl')
);

$template->pparse('links');		
page_footer();
?>