<?php

require_once 'shop.conn.php';

$buy_points = 100;

$result = verify_points($buy_points);

if (!$result['return'])
	trigger_error('您没有足够的' . $board_config['points_name']);

$error 		= false;
$error_msg 	= '';

if ( isset($_POST['submit']) )
{

	$rank = get_var('rank', '');

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_zvanie = '" . str_replace("\'", "''", $rank) . "'
		WHERE user_id = " . $userdata['user_id'];
	if ( !$db->sql_query($sql) )
	{
		trigger_error('无法更新等级名称', E_USER_WARNING);
	}

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = user_points - " . $buy_points . "
		WHERE user_id = " . $userdata['user_id'];

	if ( !$db->sql_query($sql) )
	{
		trigger_error('噢！你幸运，系统没有扣除你的' . $board_config['points_name'], E_USER_WARNING);
	}

	trigger_error('修改成功！' . back_link(append_sid('loading.php?mod=shop')));

}

page_header('请输入等级');

$template->set_filenames(array(
	'body' => 'shop_rank.tpl')
);

$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=rank'),
	'S_ACTION' => append_sid('loading.php?mod=shop&load=rank'))
);

$template->pparse('body');

page_footer();
?>