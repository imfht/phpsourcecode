<?php

require 'shop.conn.php';

$time_click = $shop_config['time_click'];

$per = 5;
$start = get_pagination_start($per);

if (isset($_GET['id']))
{
	$id = get_var('id', '');

	if (empty($id))
	{
		trigger_error('请指定您要访问的链接');
	}

	$sql = 'SELECT good_points, good_url
		FROM ' . SHOP_GOOD_TABLE . '
		WHERE good_id = ' . (int) $id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法取得内容信息', E_USER_WARNING);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$good_points = intval($row['good_points']);
		$good_url = $row['good_url'];

		if ( ($userdata['time_last_click'] + $time_click) < time() )
		{
			$new_user_points = $userdata['user_points'] + $good_points;

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = $new_user_points , time_last_click = '" . time() . "' 
				WHERE user_id = " . $userdata['user_id'];

			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('无法更新', E_USER_WARNING);
			}

			header('Location: ' . $good_url);
		}
		else
		{
			$ost = ($userdata['time_last_click'] + $time_click) - time();
			trigger_error('请勿重复点击，距离第二次点击还剩 ' . $ost . ' 秒！');
		}
	}
	else
		trigger_error('该链接不存在');
}

$sql = 'SELECT good_id, good_name, good_points
	FROM ' . SHOP_GOOD_TABLE . "
	LIMIT $start, $per";

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法取得信息', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	$template->assign_block_vars('good', array(
		'ROW_CLASS' => $row_class,
		'GOOD' 	=> $row['good_name'],
		'POINTS' => $row['good_points'],
		'U_GOOD' => append_sid('loading.php?mod=shop&load=good&id=' . $row['good_id']))
	);

	$i++;
}

$sql = 'SELECT COUNT(good_id) AS total
	FROM ' . SHOP_GOOD_TABLE;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计信息', E_USER_WARNING);
}

$total = $db->sql_fetchrow($result);

if ($total['total'] == 0)
{
	$template->assign_block_vars('not', array());
}

$pagination = ( $total['total'] <= 0 ) ? '' : generate_pagination('loading.php?mod=shop&load=good', $total['total'], $per, $start);

page_header($service['good']);

$template->set_filenames(array(
	'body' => 'shop_good.tpl')
);

$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=good'),
	'PAGINATION' => $pagination,
	'POINTS_NAME' => $board_config['points_name'])
);

$template->pparse('body');

page_footer();

?>