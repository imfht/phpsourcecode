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
	$filename = basename(__FILE__);
	$module['系统']['群发邮件'] = $filename;
	
	return;
}

define('IN_PHPBB', true);
$no_page_header = TRUE;
define('ROOT_PATH', './../');
require('pagestart.php');

@set_time_limit(1200);

$message 	= '';
$subject 	= '';
$error	 	= FALSE;
$error_msg 	= '';

if ( isset($_POST['submit']) )
{
	$subject = stripslashes(trim($_POST['subject']));
	$message = stripslashes(trim($_POST['message']));
	
	if ( empty($subject) )
	{
		$error = true;
		$error_msg .= '<p>电子邮件的内容不能为空</p>';
	}

	if ( empty($message) )
	{
		$error = true;
		$error_msg .= '<p>电子邮件的内容不能为空</p>';
	}

	$group_id = intval($_POST[POST_GROUPS_URL]);

	$sql = ( $group_id != -1 ) ? 'SELECT u.user_email FROM ' . USERS_TABLE . ' u, ' . USER_GROUP_TABLE . ' ug WHERE ug.group_id = ' . $group_id . ' AND ug.user_pending <> ' . TRUE . ' AND u.user_id = ug.user_id' : 'SELECT user_email FROM ' . USERS_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not select group members', E_USER_WARNING);
	}
	
	$bcc_list = array();
	if ($db->sql_numrows($result))
	{
		while ($row = $db->sql_fetchrow($result))
		{
			$bcc_list[] = $row['user_email'];
		}
		
		$db->sql_freeresult($result);
	}
	else
	{
		$message = ( $group_id != -1 ) ? '对不起，您输入的小组不存在' : '对不起，您输入的用户不存在';

		$error = true;
		$error_msg .= $message;
	}

	if ( !$error )
	{
		require(ROOT_PATH . 'includes/class/emailer.php');

		if ( preg_match('/[c-z]:\\\.*/i', getenv('PATH')) && !$board_config['smtp_delivery'])
		{
			$board_config['smtp_delivery'] = 1;
			$board_config['smtp_host'] = @ini_get('SMTP');
		}

		$emailer = new emailer();
	
		$emailer->from($board_config['board_email']);
		$emailer->replyto($board_config['board_email']);

		for ($i = 0; $i < count($bcc_list); $i++)
		{
			$emailer->bcc($bcc_list[$i]);
		}

		$email_headers = 'X-AntiAbuse: Board servername - ' . $board_config['server_name'] . "\n";
		$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
		$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
		$email_headers .= 'X-AntiAbuse: User IP - ' . decode_ip($user_ip) . "\n";

		$emailer->use_template('admin_send_email');
		$emailer->email_address($board_config['board_email']);
		
		$emailer->set_subject($subject);
		$emailer->extra_headers($email_headers);

		$emailer->assign_vars(array(
			'SITENAME' 		=> $board_config['sitename'], 
			'BOARD_EMAIL' 	=> $board_config['board_email'], 
			'MESSAGE' 		=> $message)
		);
		$emailer->send();
		$emailer->reset();

		trigger_error('邮件已成功发送<br />点击 <a href="' . append_sid('admin_mass_email.php') . '">这里</a> 返回超级面板<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板');
	}
}	

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

$sql = 'SELECT group_id, group_name 
	FROM ' . GROUPS_TABLE . '  
	WHERE group_single_user <> 1';
	
if ( !($result = $db->sql_query($sql)) ) 
{
	trigger_error('Could not obtain list of groups', E_USER_WARNING);
}

$select_list = '<select name = "' . POST_GROUPS_URL . '"><option value = "-1">' . '所有用户' . '</option>';
while ( $row = $db->sql_fetchrow($result) )
{
	$select_list .= '<option value = "' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
}
$select_list .= '</select>';

page_header();


$template->set_filenames(array(
	'body' => 'admin/user_email_body.tpl')
);

$template->assign_vars(array(
	'MESSAGE' => $message,
	'SUBJECT' => $subject, 
	'S_USER_ACTION' => append_sid('admin_mass_email.php'),
	'S_GROUP_SELECT' => $select_list)
);

$template->pparse('body');

page_footer();
?>