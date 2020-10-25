<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['会员']['待激活'] = $file .'?action=inactive';
	$module['会员']['已激活'] = $file .'?action=active';
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

if( !function_exists('period') )
{
	function period($date)
	{

		$years 	= floor($date/31536000);
		$date 	= $date - ($years*31536000);
		$weeks 	= floor($date/604800);
		$date 	= $date - ($weeks*604800);
		$days 	= floor($date/86400);
		$date 	= $date - ($days*86400);
		$hours 	= floor($date/3600);

		$result = 	(( $years ) ? $years .' 年, ' : '') .
					(( $years || $weeks ) ? $weeks .' 周, ' : '').
					(( $years || $weeks || $days ) ? $days .' 天, ' : '') .
					(( $years || $weeks || $days || $hours ) ? $hours .' 小时' : '');
		return $result;
	}
}

$submit_wait 	= ( isset($_POST['submit_wait']) ) ? TRUE : 0;
$confirm 		= ( isset($_POST['confirm']) ) ? TRUE : 0;
$delete 		= ( isset($_POST['delete']) ) ? TRUE : 0;
$activate 		= ( isset($_POST['activate']) ) ? TRUE : 0;
$mark_list 		= ( !empty($_POST['mark']) ) ? $_POST['mark'] : 0;

if( isset($_POST['letter']) )
{
	$by_letter = ( $_POST['letter'] ) ? $_POST['letter'] : 'all';
}
else if( isset($_GET['letter']) )
{
	$by_letter = ( $_GET['letter'] ) ? $_GET['letter'] : 'all';
}
else
{
	$by_letter = 'all';
}

if( isset($_POST['action']) || isset($_GET['action']) )
{
	$action = ( isset($_POST['action']) ) ? $_POST['action'] : $_GET['action'];
	if( $action != 'inactive' && $action != 'active' )
	{
		$action = 'inactive';
	}
}
else
{
	$action = 'inactive';
}

if( !empty($_POST['mode']) || !empty($_GET['mode']) )
{
	$mode = ( !empty($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
}
else
{
	$mode = '';
}

$start = get_pagination_start($board_config['posts_per_page']);

if( isset($_POST[POST_USERS_URL]) || isset($_GET[POST_USERS_URL]) )
{
	$user_id = ( isset($_POST[POST_USERS_URL]) ) ? intval($_POST[POST_USERS_URL]) : intval($_GET[POST_USERS_URL]);
}
else
{
	$user_id = '';
}

if( (($delete && $confirm) || $activate) && $mark_list )
{
	if( count($mark_list) )
	{
		$email_id = '';
		for( $i = 0; $i < count($mark_list); $i++ )
		{
			$email_id .= (($email_id != '') ? ', ' : '') . intval($mark_list[$i]);
		}

		$sql_mail = "SELECT username, user_email, user_active FROM ". USERS_TABLE ." WHERE user_id IN ($email_id)";
		if( !($result_mail = $db->sql_query($sql_mail)) )
		{
			trigger_error('could not get mail addresses', E_USER_WARNING);
		}
		while( $mail = $db->sql_fetchrow($result_mail) )
		{
			if( $delete )
			{
				$subject 	= '删除用户帐号';
				$text 		= '用户帐号已删除';
			}
			else if( $activate )
			{
				$subject = ( $mail['user_active'] == '0' ) ? '用户帐号激活' : '停用用户帐号';
				$text = ( $mail['user_active'] == '0' ) ? '用户帐户已激活' : '用户帐号已停用';
			}

			require_once(ROOT_PATH .'includes/class/emailer.php');
			$emailer = new emailer();
			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);
			$emailer->use_template('admin_account_action');
			$emailer->email_address($mail['user_email']);
			$emailer->set_subject($subject);

			$emailer->assign_vars(array(
				'SUBJECT' 	=> $subject,
				'TEXT' 		=> sprintf($text, $board_config['sitename']),
				'USERNAME' 	=> $mail['username'],
				'EMAIL_SIG' => ( !empty($board_config['board_email_sig']) ) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',
			));
			$emailer->send();
			$emailer->reset();
		}
		$db->sql_freeresult($result_mail);
	}
}

if( $activate && $mark_list )
{
	if( count($mark_list) )
	{
		$activate_id = '';
		for ($i = 0; $i < count($mark_list); $i++)
		{
			$activate_id .= (($activate_id != '') ? ', ' : '') . intval($mark_list[$i]);
		}

		$activate_sql = "UPDATE ". USERS_TABLE;
		switch( $action )
		{
			case 'inactive':
				$activate_sql .= " SET user_active = '1' WHERE user_active = '0'";
				break;
			case 'active':
				$activate_sql .= " SET user_active = '0' WHERE user_active = '1'";
				break;
		}
		$activate_sql .= " AND user_id IN ($activate_id)";
		if( !$db->sql_query($activate_sql) )
		{
			trigger_error('could not activate users.', E_USER_WARNING);
		}

		$template->assign_vars(array('MESSAGE' => (( count($mark_list) == '1' ) ? (( $action == 'active' ) ? '停用' : '激活') : (( $action == 'active' ) ? '停用' : '激活')).' '. '用电子邮件通知用户'));
		$template->assign_block_vars('switch_message', array() );
	}
}

$template->set_filenames(array('body' => 'admin/admin_account_body.tpl'));

$others_sql = '';
$select_letter = '';
for( $i = 97; $i <= 122; $i++ )
{
	$others_sql .= " AND username NOT LIKE '" . chr($i) . "%' ";
	$select_letter .= ( $by_letter == chr($i) ) ? strtoupper(chr($i)) .'&nbsp;' : '<a href="'. append_sid("admin_account.php?action=$action&amp;letter=". chr($i) ."&amp;start=$start") .'">'. strtoupper(chr($i)) .'</a>&nbsp;';
}
$select_letter .= ( $by_letter == 'others' ) ? '其它帐号&nbsp;' : '<a href="'. append_sid("admin_account.php?action=$action&amp;letter=others&amp;start=$start") .'">其他帐号</a>&nbsp;';
$select_letter .= ( $by_letter == 'all' ) ? '所有帐号' : '<a href="'. append_sid("admin_account.php?action=$action&amp;letter=all&amp;start=$start") .'">所有帐号</a>';

if( $by_letter == 'all' )
{
	$letter_sql = '';
}
else if( $by_letter == 'others' )
{
	$letter_sql = $others_sql;
}
else
{
	$letter_sql = " AND username LIKE '$by_letter%' ";
}

$sql_count = "SELECT COUNT(user_id) AS total_users FROM ". USERS_TABLE ." ";
$sql = "SELECT username, user_id, user_actkey, user_regdate, user_email FROM ". USERS_TABLE ." ";
switch( $action )
{
	case 'inactive':
		$sql_count .= "WHERE user_id <> ". ANONYMOUS ." AND user_active != '1' $letter_sql";
		$sql .= "WHERE user_id <> ". ANONYMOUS ." AND user_active != '1' $letter_sql";
		break;
	case 'active':
		$sql_count .= "WHERE user_id <> ". ANONYMOUS ." AND user_active != '0' $letter_sql";
		$sql .= "WHERE user_id <> ". ANONYMOUS ." AND user_active != '0' $letter_sql";
		break;
	default:
		trigger_error('没有指定模式', E_USER_ERROR);
		break;
}

if( $submit_wait && (!empty($_POST['days']) || !empty($_GET['days'])) )
{
	$days = ( !empty($_POST['days']) ) ? intval($_POST['days']) : intval($_GET['days']);
	$awaits = time() - ($days * 86400);

	$limit_awaits_count = " AND user_regdate > $awaits";
	$limit_awaits = " AND user_regdate > $awaits ";

	if( !empty($_POST['days']) )
	{
		$start = 0;
	}
}
else
{
	$limit_awaits_count = '';
	$limit_awaits = '';
	$post_days = 0;
	$days = 0;
}

$sql .= $limit_awaits ." ORDER BY user_regdate DESC LIMIT $start, ". $board_config['posts_per_page'];
$sql_all = $sql_count;
$sql_count .= $limit_awaits_count;

if( !($result = $db->sql_query($sql_count)) )
{
	trigger_error('could not query users information.', E_USER_WARNING);
}

$total_users = ( $row = $db->sql_fetchrow($result) ) ? $row['total_users'] : 0;
if( !($result = $db->sql_query($sql_all)) )
{
	trigger_error('could not query users information.', E_USER_WARNING);
}

$all_total_users = ( $row = $db->sql_fetchrow($result) ) ? $row['total_users'] : 0;

$previous_days = array(0, 1, 7, 14, 30, 90, 180, 364);
$previous_days_text = array('全部', '一天内', '一周内', '两周内', '一个月内', '三个月内', '半年内', '一年内');

$select_days = '';
for( $i = 0; $i < count($previous_days); $i++ )
{
	$selected = ( $days == $previous_days[$i] ) ? ' selected="selected"' : '';
	$select_days .= '<option value="'. $previous_days[$i] .'"'. $selected .'>'. $previous_days_text[$i] .'</option>';
}

$l_activation = ( $board_config['require_activation'] == USER_ACTIVATION_SELF ) ? '注册用户自行激活' : (( $board_config['require_activation'] == USER_ACTIVATION_ADMIN ) ? '由管理员激活' : '自由激活');

$template->assign_vars(array(
	'L_ACCOUNT_ACTIONS_EXPLAIN' 	=> ( $action == 'inactive' ) ? '等待激活的用户帐号' : '已激活的用户帐号',
	'L_DE_ACTIVATE_MARKED' 			=> ( $action == 'inactive' ) ? '激活' : '停用',
	'L_REGISTERED_AWAITS' 			=> ( $action == 'inactive' ) ? '等待激活' : '注册用户',
	'L_ACTIVATION' 					=> $l_activation,
	'TOTAL_USERS' 					=> '共有 ' . $total_users . '位用户',
	'PAGINATION' 					=> generate_pagination('admin_account.php?action=' . $action . '&amp;letter=' . $by_letter, $total_users, $board_config['posts_per_page'], $start),
	'S_LETTER_SELECT' 				=> $select_letter,
	'S_LETTER_HIDDEN' 				=> '<input type="hidden" name="letter" value="'. $by_letter .'">',
	'S_ACCOUNT_ACTION' 				=> append_sid("admin_account.php?action=$action"),
	'S_SELECT_DAYS' 				=> $select_days,
));

if( !($result = $db->sql_query($sql)) )
{
	trigger_error('could not query users.', E_USER_WARNING);
}

if( $db->sql_numrows($result) > 0 )
{
	$i = 0;
	while( $row = $db->sql_fetchrow($result) )
	{
		$user_id = $row['user_id'];

		$email_url = ( $board_config['board_email_form'] ) ? append_sid("../ucp.php?mode=email&amp;". POST_USERS_URL ."=$user_id") : 'mailto:'. $row['user_email'];
		$email = '<a href="'. $email_url .'" class="gensmall">'. $row['user_email'] .'</a>';
		
		$template->assign_block_vars('admin_account', array(
			'NUMBER' 		=> $i + $start + 1,
			'ROW_CLASS' 	=> ( !($i % 2) ) ? 'row1' : 'row2',
			'USERNAME' 		=> $row['username'],
			'U_UCP'			=> append_sid(ROOT_PATH . 'ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id),
			'EMAIL' 		=> $email,
			'JOINED' 		=> create_date($board_config['default_dateformat'], $row['user_regdate'], $board_config['board_timezone']),
			'PERIOD' 		=> period(time() - $row['user_regdate']),
			'U_EDIT_USER' 	=> append_sid("admin_users.php?mode=edit&amp;". POST_USERS_URL ."=$user_id"),
			'U_USER_AUTH' 	=> append_sid("admin_ug_auth.php?mode=user&amp;". POST_USERS_URL ."=$user_id"),
			'S_MARK_ID' 	=> $user_id, 
		));
		$i++;
	}
	
	$db->sql_freeresult($result);
}
else
{
	$template->assign_block_vars('switch_no_users', array() );
}

$template->pparse('body');
page_footer();

?>