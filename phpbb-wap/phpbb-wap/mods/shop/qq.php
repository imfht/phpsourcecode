<?php

require 'shop.conn.php';

$buy_points = 100;
$per = 5;
$start = get_pagination_start($per);

$result = verify_points($buy_points);

if (!$result['return'])
	trigger_error('您没有足够的' . $board_config['points_name']);

if (isset($_GET['qq']))
{
	$qq = get_var('qq', '');

	if (empty($qq))
		trigger_error('请指定您要购买的ＱＱ号码', E_USER_ERROR);

	$sql = 'SELECT qq, points, password
		FROM ' . SHOP_QQ_TABLE . '
		WHERE qq = ' . (int) $qq;

	if (!$result = $db->sql_query($sql))
		trigger_error('无法取得出售的ＱＱ信息', E_USER_WARNING);
	
	if ($row = $db->sql_fetchrow($result))
	{
		if ($row['points'] > $userdata['user_points'])
		{
			trigger_error('您没有足够的' . $board_config['points_name'] . '，看看其他的吧！' . back_link(append_sid('loading.php?mod=shop&load=qq')), E_USER_ERROR);
		}

		$sql = 'DELETE FROM ' . SHOP_QQ_TABLE . ' WHERE qq = ' . (int) $row['qq'];
		
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除已购买的ＱＱ号码', E_USER_WARNING);
		}

		$new_user_points = $userdata['user_points'] - (int) $row['points'];

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = $new_user_points
			WHERE user_id = " . $userdata['user_id'];

		if ( !$db->sql_query($sql) )
		{
			trigger_error('噢！你幸运，系统没有扣除你的' . $board_config['points_name'], E_USER_WARNING);
		}

		trigger_error('购买成功，请记住哦，刷新页面后就不会显示了<br />ＱＱ：' . $row['qq'] . '<br />密码：' . $row['password'] . back_link(append_sid('loading.php?mod=shop')), E_USER_ERROR);
	}
	else
		trigger_error('系统没有出售此ＱＱ或已被他人购买' . back_link(append_sid('loading.php?mod=shop&load=qq')), E_USER_ERROR);
}

page_header('购买ＱＱ号码');

$sql = "SELECT qq, points 
	FROM  " . SHOP_QQ_TABLE . " 
	LIMIT $start, $per";

if ( !$result = $db->sql_query($sql) )
{
	trigger_error('无法取得出售的ＱＱ信息', E_USER_WARNING);
}

$i = 1;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('qq', array(
		'ROW_CLASS' => $row_class,
		'QQ' => $row['qq'],
		'POINTS' => $row['points'],
		'U_BUY' => append_sid('loading.php?mod=shop&load=qq&qq=' . $row['qq']))
	);

	$i++;
}

$sql = "SELECT COUNT(qq) AS total FROM " . SHOP_QQ_TABLE;

if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法统计出售的ＱＱ号码', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

$pagination = generate_pagination('loading.php?mod=shop&load=qq', $row['total'], $per, $start);

$template->set_filenames(array(
	'body' => 'shop_qq.tpl')
);

$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=qq'),
	'PAGINATION' => $pagination,
	'POINTS_NAME' => $board_config['points_name'])
);
$template->pparse('body');

page_footer();

?>