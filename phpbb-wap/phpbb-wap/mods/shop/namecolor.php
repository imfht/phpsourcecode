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

	$color = get_var('color', '');

	if ( !preg_match("/^#[A-Za-z0-9]{6}$/", $color) )// 匹配颜色代码
	{
		$error = TRUE;
		$error_msg .= '<p>颜色值填写错误</p>';
	}

	if (!$error)
	{
		$sql = "UPDATE " . USERS_TABLE . "
			SET user_nic_color = '" . str_replace("\'", "''", $color) . "'
			WHERE user_id = " . $userdata['user_id'];

		if ( !$db->sql_query($sql) )
		{
			trigger_error('修改颜色值出错', E_USER_WARNING);
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

}

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

page_header('请输入颜色值');

$template->set_filenames(array(
	'body' => 'shop_namecolor.tpl')
);

$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=namecolor'),
	'S_ACTION' => append_sid('loading.php?mod=shop&load=namecolor'))
);

$template->pparse('body');

page_footer();
?>