<?php

define('BANK_TABLE', $table_prefix.'bank');
define('BANK_CONFIG_TABLE', $table_prefix.'bank_config');

$sql = "SELECT config_name, config_value
	FROM " . BANK_CONFIG_TABLE;
if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法取得商店的配置信息', E_USER_WARNING);
}

while ( $row = $db->sql_fetchrow($result) )
{
	$bank_config[$row['config_name']] = $row['config_value'];
}

if ( isset($_GET['action']) || isset($_POST['action']) ) $action = ( isset($_POST['action']) ) ? $_POST['action'] : $_GET['action'];
else $action = '';

if ( $action == 'update_config')
{
	if ( isset($_GET['name']) || isset($_POST['name']) ) $name = ( isset($_POST['name']) ) ? $_POST['name'] : $_GET['name'];
	else $name = '';
	if ( isset($_GET['interestrate']) || isset($_POST['interestrate']) ) $interestrate = ( isset($_POST['interestrate']) ) ? intval($_POST['interestrate']) : intval($_GET['interestrate']);
	else $interestrate = '';
	if ( isset($_GET['withdrawfee']) || isset($_POST['withdrawfee']) ) $withdrawfee = ( isset($_POST['withdrawfee']) ) ? intval($_POST['withdrawfee']) : intval($_GET['withdrawfee']);
	else $withdrawfee = '';
	if ( isset($_GET['paymenttime']) || isset($_POST['paymenttime']) ) $paymenttime = ( isset($_POST['paymenttime']) ) ? intval($_POST['paymenttime']) : intval($_GET['paymenttime']);
	else $paymenttime = '';
	if ( isset($_GET['disableinterest']) || isset($_POST['disableinterest']) ) $disableinterest = ( isset($_POST['disableinterest']) ) ? intval($_POST['disableinterest']) : intval($_GET['disableinterest']);
	else $disableinterest = '';
	if ( isset($_GET['min_depo']) || isset($_POST['min_depo']) ) $min_depo = ( isset($_POST['min_depo']) ) ? intval($_POST['min_depo']) : intval($_GET['min_depo']);
	else $min_depo = '';
	if ( isset($_GET['min_with']) || isset($_POST['min_with']) ) $min_with = ( isset($_POST['min_with']) ) ? intval($_POST['min_with']) : intval($_GET['min_with']);
	else $min_with = '';
	if ( isset($_GET['status']) || isset($_POST['status']) ) $status = ( isset($_POST['status']) ) ? $_POST['status'] : $_GET['status'];
	else $status = '';


	$sql = array();

	if ( stripslashes($name) != $bank_config['bankname'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$name'
			WHERE config_name = 'bankname'";
	}
	if ( $interestrate != $bank_config['bankinterest'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$interestrate'
			WHERE config_name = 'bankinterest'";
	}
	if ( $withdrawfee != $bank_config['bankfees'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$withdrawfee'
			WHERE config_name = 'bankfees'"; 
	}
	if ( $paymenttime != $bank_config['bankpayouttime'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$paymenttime'
			WHERE config_name = 'bankpayouttime'";
	}
	if ( $min_with != $bank_config['bank_minwithdraw'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$min_with'
			WHERE config_name = 'bank_minwithdraw'";
	}
	if ( $min_depo != $bank_config['bank_mindeposit'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$min_depo'
			WHERE config_name = 'bank_mindeposit'";
	}
	if ( $disableinterest != $bank_config['bank_interestcut'] )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '$disableinterest'
			WHERE config_name = 'bank_interestcut'";
	}
	if ( $status != $bank_config['bankopened'] && ( $status == 'off' || $status == 'on' ) )
	{
		$sql[] = "UPDATE ". BANK_CONFIG_TABLE . "
			SET config_value = '" . ( ( $status == 'off' ) ? 'off' : time() ) . "'
			WHERE config_name = 'bankopened'";
	}

	$sql_count = count($sql);
	for ( $i = 0; $i < $sql_count; $i++ ) 
	{ 
		if ( !($db->sql_query($sql[$i])) ) trigger_error('无法设置参数');
	}
	trigger_error('虚拟银行参数保存成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=bank')));
}
else if ( $action == 'edit_account' )
{
	if ( isset($_GET['username']) || isset($_POST['username']) ) $username = ( isset($_POST['username']) ) ? $_POST['username'] : $_GET['username'];
	else $username = '';

	$template->set_filenames(array(
		'body' => 'bank_admin_edit_user.tpl')
	);

	//check username & get account information
	$user_row = get_userdata($username);

	$sql = "SELECT *
		FROM " . BANK_TABLE . "
		WHERE user_id = '{$user_row['user_id']}'";
	if ( !($result = $db->sql_query($sql)) ) trigger_error('无法取得该用户在虚拟银行的信息');
	if ( !($db->sql_numrows($result)) ) trigger_error('该用户还没有创建虚拟银行账户' . back_link(append_sid('admin_mods.php?mode=admin&mods=bank')));
	else $row = $db->sql_fetchrow($result);

	$fees_on_select = ( $row['fees'] == 'on' ) ? 'SELECTED' : '';
	$fees_off_select = ( $row['fees'] == 'on' ) ? '' : 'SELECTED';

	$template->assign_vars(array(
		'S_CONFIG_ACTION' => append_sid('admin_mods.php?mode=admin&mods=bank'),

		'USER_ID' => $user_row['user_id'],
		'USER_HOLDING' => $row['holding'],
		'USER_WITHDRAWN' => $row['totalwithdrew'],
		'USER_DEPOSITED' => $row['totaldeposit'],
		
		'U_ADMIN_BANK'	=> append_sid('admin_mods.php?mode=admin&mods=bank'),

		'SELECT_FEES_ON' => $fees_on_select,
		'SELECT_FEES_OFF' => $fees_off_select)
	);
	
	$template->pparse('body');

}
elseif ( $action == 'update_account' )
{
	if ( isset($_GET['user_id']) || isset($_POST['user_id']) ) $user_id = ( isset($_POST['user_id']) ) ? intval($_POST['user_id']) : intval($_GET['user_id']);
	else $user_id = '';
	if ( isset($_GET['holding']) || isset($_POST['holding']) ) $holding = ( isset($_POST['holding']) ) ? intval($_POST['holding']) : intval($_GET['holding']);
	else $holding = '';
	if ( isset($_GET['withdrawn']) || isset($_POST['withdrawn']) ) $withdrawn = ( isset($_POST['withdrawn']) ) ? intval($_POST['withdrawn']) : intval($_GET['withdrawn']);
	else $withdrawn = '';
	if ( isset($_GET['deposited']) || isset($_POST['deposited']) ) $deposited = ( isset($_POST['deposited']) ) ? intval($_POST['deposited']) : intval($_GET['deposited']);
	else $deposited = '';
	if ( isset($_GET['fees']) || isset($_POST['fees']) ) $fees = ( isset($_POST['fees']) ) ? $_POST['fees'] : $_GET['fees'];
	else $fees = '';

	$sql = "SELECT *
		FROM " . BANK_TABLE . "
		WHERE user_id = '$user_id'";
	if ( !($result = $db->sql_query($sql)) ) trigger_error('无法查询用户的虚拟银行信息');
	if ( !($db->sql_numrows($result)) ) trigger_error('该用户还没有创建虚拟银行账户' . back_link(append_sid('admin_mods.php?mode=admin&mods=bank')));
	else $row = $db->sql_fetchrow($result);

	$holding = ( $holding < 0 ) ? $row['holding'] : $holding;
	$withdrawn = ( $withdrawn < 0 ) ? $row['totalwithdrew'] : $withdrawn;
	$deposited = ( $deposited < 0 ) ? $row['totaldeposited'] : $deposited;
	$fees = ( $fees != 'on' && $fees != 'off' ) ? $row['fees'] : $fees;

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = '$holding',
			totalwithdrew = '$withdrawn',
			totaldeposit = '$deposited', 
			fees = '$fees'
		WHERE user_id = '$user_id'";
	if ( !($db->sql_query($sql)) ) trigger_error('更新失败');

	trigger_error('更新成功' . back_link(append_sid('admin_mods.php?mode=admin&mods=bank')));
}
else
{
	$sql = "SELECT sum(holding) as holdings, sum(totaldeposit) as total_deposits, sum(totalwithdrew) as total_withdraws, count(*) as total_users
		FROM " . BANK_TABLE;
	if ( !($result = $db->sql_query($sql)) ) trigger_error('数据统计失败');

	$row = $db->sql_fetchrow($result);

	$bank_on_select = ( $bank_config['bankopened'] != off ) ? 'SELECTED' : '';
	$bank_off_select = ( $bank_config['bankopened'] != off ) ? '' : 'SELECTED';

	$template->assign_vars(array(
		'S_CONFIG_ACTION' => append_sid('admin_mods.php?mode=admin&mods=bank'),
		'BANK_ACCOUNTS' => ( $row['total_users'] ) ? $row['total_users'] : '0',
		'BANK_DEPOSITS' => ( $row['total_deposits'] ) ? $row['total_deposits'] : '0',
		'BANK_WITHDRAWS' => ( $row['total_withdraws'] ) ? $row['total_withdraws'] : '0',
		'BANK_HOLDING' => ( $row['holdings'] ) ? $row['holdings'] : '0', 
		'BANK_DISABLE_INTEREST' => $bank_config['bank_interestcut'],
		'BANK_PAY_TIME' => $bank_config['bankpayouttime'],
		'BANK_FEES' => $bank_config['bankfees'],
		'BANK_INTEREST' => $bank_config['bankinterest'],
		'BANK_NAME' => $bank_config['bankname'],
		'BANK_MIN_DEPO' => $bank_config['bank_mindeposit'],
		'BANK_MIN_WITH' => $bank_config['bank_minwithdraw'],
		'L_POINTS' => $board_config['points_name'],

		'SELECT_STATUS_ON' => $bank_on_select,
		'SELECT_STATUS_OFF' => $bank_off_select)
	);
	$template->set_filenames(array(
		'body' => 'bank_admin_config.tpl')
	);

	$template->assign_vars(array(
		'U_ADMIN_MODS'		=> append_sid('admin_mods.php'))
	);

	$template->pparse('body');
}

?>