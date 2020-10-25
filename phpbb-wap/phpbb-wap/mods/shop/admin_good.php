<?php

include 'shop.constants.php';


if ( isset($_GET['delete']) )
{
	$delete_id = get_var('id', '');
	if (empty($delete_id))
	{
		trigger_error('请指定要删除的内容');
	}

	$sql = 'DELETE FROM ' . SHOP_GOOD_TABLE . '
		WHERE good_id = ' . (int) $delete_id;

	if (!$db->sql_query($sql))
	{
		trigger_error('删除失败', E_USER_WARNING);
	}

	trigger_error('删除成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=good')));

}
elseif (isset($_POST['add']))
{
	$good_name = get_var('name', '');
	$good_url = get_var('url', '');
	$good_points = get_var('points', '');

	$sql = "INSERT INTO " . SHOP_GOOD_TABLE . " (good_name, good_url, good_points) 
		VALUES ('$good_name', '$good_url', $good_points)";

	if (!$db->sql_query($sql))
	{
		trigger_error('无法添加内容', E_USER_WARNING);
	}

	trigger_error('添加成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=good')));
}

$sql = 'SELECT good_id, good_name, good_url, good_points
	FROM ' . SHOP_GOOD_TABLE;

if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法查询精彩内容表', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('good', array(
		'ROW_CLASS' => $row_class,
		'GOOD' => $row['good_name'],
		'U_GOOD' => $row['good_url'],
		'GOOD_POINTS' => $row['good_points'],
		'GOOD_URL' => $row['good_url'],
		'U_DELETE' => append_sid('admin_mods.php?mode=admin&mods=shop&load=good&id=' . $row['good_id'] . '&delete'))
	);
	
	$i++;
}
$template->set_filenames(array(
	'body' => 'shop_admin_good.tpl')
);

$template->assign_vars(array(
	'U_BACK' => append_sid('admin_mods.php?mode=admin&mods=shop'),
	'POINTS_NAME' => $board_config['points_name'],
	'S_ACTION' => append_sid('admin_mods.php?mode=admin&mods=shop&load=good'))
);

$template->pparse('body');

?>