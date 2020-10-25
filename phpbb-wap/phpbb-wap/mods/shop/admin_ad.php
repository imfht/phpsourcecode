<?php

include 'shop.constants.php';

if ( isset($_GET['delete']) )
{
	$delete_id = get_var('id', '');
	if (empty($delete_id))
	{
		trigger_error('请指定要删除的内容');
	}

	$sql = 'DELETE FROM ' . SHOP_AD_TABLE . '
		WHERE ad_id = ' . (int) $delete_id;

	if (!$db->sql_query($sql))
	{
		trigger_error('删除失败', E_USER_WARNING);
	}

	trigger_error('删除成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=ad')));

}

$sql = 'SELECT ad_id, ad_name, ad_type, ad_time, ad_url
	FROM ' . SHOP_AD_TABLE;

if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法查询广告表', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('ad', array(
		'ROW_CLASS' => $row_class,
		'TITLE' => $row['ad_name'],
		'URL' => $row['ad_url'],
		'DATE' => create_date($userdata['user_dateformat'], time(), $userdata['user_timezone']),
		'LOCATION' => ($row['ad_type']) ? '顶部' : '底部',
		'U_DELETE' => append_sid('admin_mods.php?mode=admin&mods=shop&load=ad&id=' . $row['ad_id'] . '&delete'))
	);
	
	$i++;
}

$template->set_filenames(array(
	'body' => 'shop_admin_ad.tpl')
);

$template->assign_vars(array(
	'U_BACK' => append_sid('admin_mods.php?mode=admin&mods=shop'),
	'POINTS_NAME' => $board_config['points_name'],
	'S_ACTION' => append_sid('admin_mods.php?mode=admin&mods=shop&load=good'))
);

$template->pparse('body');

?>