<?php

require_once(ROOT_PATH . 'includes/functions/validate.php');

require 'shop.conn.php';

$buy_points = 100;

$result = verify_points($buy_points);

if (!$result['return'])
	trigger_error('您没有足够的' . $board_config['points_name']);

$error 		= false;
$error_msg 	= '';

if ( isset($_POST['submit']) )
{

	$username = get_var('username', '');

	$username = phpbb_clean_username($username);

	if ($username == '')
	{
		$error = TRUE;
		$error_msg .= '<p>用户名不能为空</p>';
	}

	$result = validate_username($username);

	if ( $result['error'] )
	{
		$error = TRUE;
		$error_msg .= '<p>' . $result['error_msg'] . '</p>';
	}

	if (!$error)
	{
		$sql = "UPDATE " . USERS_TABLE . "
			SET username = '" . str_replace("\'", "''", $username) . "'
			WHERE user_id = " . $userdata['user_id'];

		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法修改用户名', E_USER_WARNING);
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

page_header('请输入用户名');

$template->set_filenames(array(
	'body' => 'shop_username.tpl')
);

$template->assign_vars(array(
	'U_BACK'	=> append_sid('loading.php?mod=shop&load=buy&to=username'),
	'S_ACTION' => append_sid('loading.php?mod=shop&load=username'))
);

$template->pparse('body');

page_footer();
?>