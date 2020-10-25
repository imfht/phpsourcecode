<?php
/**
* @package phpBB-WAP
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* MOD名称: 虚拟银行
* MOD支持地址: http://phpbb-wap.com
* MOD描述: 系统虚拟银行
* MOD作者: Crazy
* MOD版本: v1.0
* MOD显示: on
*/

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

if ( $bank_config['bankpayouttime'] < 1 )
	trigger_error('您必须指定一个比银行高回报零配置');
if ( $bank_config['bankopened'] == 'off' )
	trigger_error('虚拟银行现在已经关闭，如有问题请联系管理员');

$time = time();

if ( ($time - $bank_config['banklastrestocked']) > $bank_config['bankpayouttime'] )
{
	$sql = "UPDATE " . BANK_CONFIG_TABLE . "
		SET config_value = '$time'
		WHERE config_name = 'banklastrestocked'";

	if ( !($db->sql_query($sql)) )
		trigger_error('无法更新虚拟银行的设置', E_USER_WARNING);

	$interesttime = ( ($time - $bank_config['banklastrestocked']) / $bank_config['bankpayouttime'] );

	$sql = 'UPDATE ' . BANK_TABLE . '
		SET holding = holding + round(((holding / 100) * ' . $bank_config['bankinterest'] . ') * ' . $interesttime . ')
		' . ( ( $bank_config['bank_interestcut'] ) ? "WHERE holding < " . $bank_config['bank_interestcut'] : "" );
	if ( !($db->sql_query($sql)) )
		trigger_error('无法更新利息', E_USER_WARNING);

	header("Location: index.php");
}

$sql = "SELECT *
	FROM " . BANK_TABLE . "
	WHERE user_id = " .$userdata['user_id'];

if ( !$result = $db->sql_query($sql) ) 
	trigger_error('无法查询用户的虚拟银行信息', E_USER_WARNING);

$row = $db->sql_fetchrow($result);

if ( isset($_GET['action']) || isset($_POST['action']) ) $action = ( isset($_POST['action']) ) ? $_POST['action'] : $_GET['action'];
else $action = '';

if ( $action == 'createaccount' )
{
	if ( !$userdata['session_logged_in'] ) trigger_error('请先登录！' . back_link(append_sid('loading.php?mod=bank')));

	$template->set_filenames(array(
		'body' => 'bank_body.tpl')
	);

	if ( is_numeric($row['holding']) ) trigger_error('您已经在银行创建有虚拟账户' . back_link(append_sid('loading.php?mod=bank')));
	else
	{
		$sql = "INSERT INTO " . BANK_TABLE . " (user_id, opentime, fees)
			VALUES('{$userdata['user_id']}', '" . time() . "', 'on')";

		if ( !($db->sql_query($sql)) ) trigger_error('创建银行虚拟账户出错了');

		trigger_error('银行虚拟账户已成功创建！' . back_link(append_sid('loading.php?mod=bank')));
	}
}
elseif ( $action == 'deposit' )
{
	if ( isset($_GET['deposit']) || isset($_POST['deposit']) ) $deposit = ( isset($_POST['deposit']) ) ? intval($_POST['deposit']) : intval($_GET['deposit']);
	else $deposit = '';

	if ( !$userdata['session_logged_in'] ) trigger_error('请先登录！' . back_link(append_sid('loading.php?mod=bank')));

	if ( $deposit < $bank_config['bank_mindeposit'] ) trigger_error('最少需要存入' . $bank_config['bank_mindeposit'] . $board_config['points_name']);
	elseif ( $deposit < 1 ) trigger_error('存入金额有误，请重新填写');
	elseif ( $deposit > $userdata['user_points'] ) trigger_error('您的账户只有' . $userdata['user_points'] . $board_config['points_name']);

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = (user_points - $deposit)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) trigger_error('存款机出错了，请拨打虚拟银行存款服务热线13800138000');

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = (holding + $deposit),
			totaldeposit = (totaldeposit + $deposit)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) trigger_error('存款机出错了，请拨打虚拟银行存款服务热线13800138000');

	trigger_error('您已将' . $deposit . $board_config['points_name'] .'存入虚拟银行<br />您的虚拟银行账户中有' . ($row['holding'] + $deposit) . $board_config['points_name'] . '，网站帐号账户中有' . ($userdata['user_points'] - $deposit) . $board_config['points_name'] . back_link(append_sid('loading.php?mod=bank')));
}
elseif ( $action == 'withdraw' )
{
	if ( isset($_GET['withdraw']) || isset($_POST['withdraw']) ) $withdraw = ( isset($_POST['withdraw']) ) ? intval($_POST['withdraw']) : intval($_GET['withdraw']);
	else $withdraw = '';

	if ( !$userdata['session_logged_in'] ) trigger_error('请先登录！' . back_link(append_sid('loading.php?mod=bank')));

	if ( $withdraw < $bank_config['bank_minwithdraw'] ) trigger_error('至少需要取出' .  $bank_config['bank_minwithdraw'] . $board_config['points_name']);
	elseif ( $withdraw < 1 ) trigger_error('取出金额有误，请重新填写');
	
	if ( $row['fees'] == 'on' )
	{
		$withdrawtotal = round((($withdraw / 100) * $bank_config['bankfees']));
		if ( $withdrawtotal == 0 )  $withdrawtotal = 1;
	} else $withdrawtotal = 0;

	$withdrawtotal = $withdrawtotal + $withdraw;

	if ( $row['holding'] < $withdrawtotal ) trigger_error('余额不足');

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = (user_points + $withdraw)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) trigger_error('取款机出错了，请拨打虚拟银行取款服务热线10086');

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = (holding - $withdrawtotal),
			totalwithdrew = (totalwithdrew + $withdraw)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) trigger_error('取款机出错了，请拨打虚拟银行取款服务热线10086');

	trigger_error('您已成功取出' . $withdraw . $board_config['points_name'] . '！<br />您的虚拟银行账户中有' . ($row['holding'] - $withdrawtotal) . '，网站帐号账户中有' . ($userdata['user_points'] + $withdraw) . $board_config['points_name'] . back_link(append_sid('loading.php?mod=bank')));
}

//$template->reset_root()
$template->assign_var('BANK_LOGO', make_style_image('bank_logo'));

page_header($bank_config['bankname']);

if ( !isset($row['holding']) && $userdata['user_id'] > 0 )
{
	$template->assign_block_vars('no_account', array(
		'U_OPEN_ACCOUNT' => append_sid("loading.php?mod=bank&action=createaccount"))
	);
}
elseif ( $userdata['user_id'] > 0 )
{
	$template->assign_block_vars('has_account', array());
}

$sql = "SELECT sum(holding) as total_holding, count(user_id) as total_users
	FROM " . BANK_TABLE . "
	WHERE id > 0";

if ( !($result = $db->sql_query($sql)) ) trigger_error('无法统计虚拟银行资产', E_USER_WARNING);

$b_row = $db->sql_fetchrow($result);

$bankholdings = ( $b_row['total_holding'] ) ? $b_row['total_holding'] : 0;

$bankusers = $b_row['total_users'];

$withdrawtotal = ( $row['fees'] == 'on' ) ? $row['holding'] - (round($row['holding'] / 100 * $bank_config['bankfees'])) : $row['holding'];

if ( $row['fees'] == 'on' )
{
	$template->assign_block_vars('switch_withdraw_fees', array());
}
if ( $bank_config['bank_minwithdraw'] )
{
	$template->assign_block_vars('switch_min_with', array());
}
if ( $bank_config['bank_mindeposit'] )
{
	$template->assign_block_vars('switch_min_depo', array());
}

$account = (!is_numeric($row['holding']) ) ? '建立' : '存取款';

$template->assign_vars(array(
	'L_BANK_TITLE' => $bank_config['bankname'],
	'L_BANK_ACCOUNT_TITLE' => $account,

	'BANK_OPENED' => ($bank_config['bankopened'] == 'on') ? '开放' : '关闭',
	'BANK_HOLDINGS' => $bankholdings,
	'BANK_ACCOUNTS' => $bankusers,
	'BANK_FEES' => $bank_config['bankfees'],
	'BANK_INTEREST' => $bank_config['bankinterest'],
	'BANK_MIN_WITH' => $bank_config['bank_minwithdraw'],
	'BANK_MIN_DEPO' => $bank_config['bank_mindeposit'],

	'USER_BALANCE' => $row['holding'],
	'USER_GOLD' => $userdata['user_points'],
	'USER_WITHDRAW' => $withdrawtotal,
	'L_POINTS' => $board_config['points_name'],

	'U_WITHDRAW' => append_sid("loading.php?mod=bank&action=withdraw"),
	'U_DEPOSIT' => append_sid("loading.php?mod=bank&action=deposit")
));
$template->assign_block_vars('', array());

$template->set_filenames(array(
	'body' => 'bank_body.tpl')
);

$template->pparse('body');

page_footer();


?>