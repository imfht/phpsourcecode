<?php

include 'shop.constants.php';


if ( isset($_GET['delete']) )
{
	$qq = get_var('qq', '');
	if (empty($qq))
	{
		trigger_error('请指定要删除的ＱＱ');
	}

	$sql = 'DELETE FROM ' . SHOP_QQ_TABLE . '
		WHERE qq = ' . (int) $qq;

	if (!$db->sql_query($sql))
	{
		trigger_error('删除失败', E_USER_WARNING);
	}

	trigger_error('删除成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=qq')));

}
elseif (isset($_POST['add']))
{
	$qq = get_var('qq', '');
	$password = get_var('password', '');
	$points = get_var('points', '');

	if (!preg_match('/^[1-9][0-9]{4,9}$/', $qq))
	{
		trigger_error('这不是一个ＱＱ号码');
	}

	$sql = "INSERT INTO " . SHOP_QQ_TABLE . " (qq, password, points) 
		VALUES ($qq, '$password', $points)";

	if (!$db->sql_query($sql))
	{
		trigger_error('无法添加内容', E_USER_WARNING);
	}

	trigger_error('添加成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=qq')));
}

$sql = 'SELECT qq, password, points
	FROM ' . SHOP_QQ_TABLE;

if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法查询精彩内容表', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('qq', array(
		'ROW_CLASS' => $row_class,
		'QQ' => $row['qq'],
		'PASSWORD' => $row['password'],
		'POINTS' => $row['points'],
		'U_DELETE' => append_sid('admin_mods.php?mode=admin&mods=shop&load=qq&qq=' . $row['qq'] . '&delete'))
	);
	
	$i++;
}
$template->set_filenames(array(
	'body' => 'shop_admin_qq.tpl')
);

$template->assign_vars(array(
	'U_BACK' => append_sid('admin_mods.php?mode=admin&mods=shop'),
	'POINTS_NAME' => $board_config['points_name'],
	'S_ACTION' => append_sid('admin_mods.php?mode=admin&mods=shop&load=qq'))
);

$template->pparse('body');

?>