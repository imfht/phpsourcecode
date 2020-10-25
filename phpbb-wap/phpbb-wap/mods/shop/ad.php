<?php

require 'shop.conn.php';

$buy_points = 0;
$top_ad = $shop_config['top_ad'];
$foot_ad = $shop_config['foot_ad'];
$max_day = $shop_config['max_day'];
$min_day = $shop_config['min_day'];
$max_top_ad = $shop_config['max_top_ad'];
$max_foot_ad = $shop_config['max_foot_ad'];

$result = verify_points($buy_points);

if (!$result['return'])
	trigger_error('您没有足够的' . $board_config['points_name']);

$sql = 'DELETE FROM ' . SHOP_AD_TABLE . ' WHERE ad_time < ' . time();

if (!$db->sql_query($sql))
{
	trigger_error('无法删除已过期的广告', E_USER_WARNING);
}

$error 		= false;
$error_msg 	= '';

if (isset($_POST['submit']))
{
	$name = get_var('name', '');
	$type = get_var('type', 0);
	$url = get_var('url', '');
	$day = get_var('day', '');

	if (empty($name))
	{
		$error 		= true;
		$error_msg	= '<p>链接名称不能为空</p>';
	}

	if (strlen($name) > 64)
	{
		$error 		= true;
		$error_msg	= '<p>链接名称不能超过64字节</p>';
	}

	$type = ($type) ? 1 : 0;

	if (strlen($url) > 250)
	{
		$error 		= true;
		$error_msg	= '<p>URL不能超过250字节</p>';
	}

	if (intval($day) > $max_day || intval($day) < $min_day)
	{
		$error 		= true;
		$error_msg	= '<p>至少需要投放 ' . $min_day . ' 天，最多不能超过 ' . $max_day . ' 天</p>';
	}

	$type_points = ($type) ? $top_ad : $foot_ad;

	$pay_points = $type_points * $day;

	if ($pay_points > $userdata['user_points'])
	{
		$error 		= true;
		$error_msg	= '<p>您没有足够的' . $board_config['points_name'] . '支付</p>';
	}

	$sql = 'SELECT count(*) AS total 
		FROM ' . SHOP_AD_TABLE . '
		WHERE ad_type = ' . $type;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法统计广告信息', E_USER_WARNING);
	}

	$total = $db->sql_fetchrow($result);

	if ($type)
	{
		if ($total['total'] == $max_top_ad)
		{
			$error 		= true;
			$error_msg	= '<p>顶部广告已满</p>';
		}
	}
	else
	{
		if ($total['total'] == $max_foot_ad)
		{
			$error 		= true;
			$error_msg	= '<p>底部广告已满</p>';
		}
	}

	if ( !$error )
	{
		$end_time = time() + 86400000 * intval($day);

		$sql = 'INSERT INTO ' . SHOP_AD_TABLE . " (ad_name, ad_type, ad_time, ad_url) 
			VALUES ('" . str_replace("\'", "''", $name) . "', $type, $end_time, '" . str_replace("\'", "''", $url) . "')";

		if (!$db->sql_query($sql))
		{
			trigger_error('无法插入新的广告信息', E_USER_WARNING);
		}

		$new_user_points = $userdata['user_points'] - $pay_points;
		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = $new_user_points 
			WHERE user_id = " . $userdata['user_id'];

		if ( !$db->sql_query($sql) )
		{
			trigger_error('噢！你幸运，系统没有扣除你的' . $board_config['points_name'], E_USER_WARNING);
		}

		trigger_error('投放成功！' . back_link(append_sid('loading.php?mod=shop&load=ad')));
	}

}

page_header('请输入广告信息');

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

$template->set_filenames(array(
	'body' => 'shop_ad.tpl')
);


$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=ad'),
	'TOP_AD' => $top_ad,
	'FOOT_AD' => $foot_ad,
	'POINTS_NAME' => $board_config['points_name'],
	'MAX_DAY' => $max_day,
	'MIN_DAY' => $min_day)
);
$template->pparse('body');

page_footer();
?>