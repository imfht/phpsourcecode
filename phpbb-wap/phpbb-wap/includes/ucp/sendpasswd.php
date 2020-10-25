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

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

if ( isset($_POST['submit']) )
{
	$username = ( !empty($_POST['username']) ) ? phpbb_clean_username($_POST['username']) : '';
	$email = ( !empty($_POST['email']) ) ? trim(strip_tags(htmlspecialchars($_POST['email']))) : '';

	$sql = "SELECT user_id, username, user_email, user_active 
		FROM " . USERS_TABLE . " 
		WHERE user_email = '" . str_replace("\'", "''", $email) . "' 
			AND username = '" . str_replace("\'", "''", $username) . "'";
	if ( $result = $db->sql_query($sql) )
	{
		if ( $row = $db->sql_fetchrow($result) )
		{
			if ( !$row['user_active'] )
			{
				trigger_error('对不起，因为您的帐号尚未激活，所以无法使用找回密码功能。请联系论坛管理员以获得更多的信息', E_USER_ERROR);
			}

			$username = $row['username'];
			$user_id = $row['user_id'];

			$user_actkey = gen_rand_string(true);
			$key_len = 54 - strlen($server_url);
			$key_len = ($key_len > 6) ? $key_len : 6;
			$user_actkey = substr($user_actkey, 0, $key_len);
			$user_password = gen_rand_string(false);
			
			$sql = "UPDATE " . USERS_TABLE . " 
				SET user_newpasswd = '" . md5($user_password) . "', user_actkey = '$user_actkey'  
				WHERE user_id = " . $row['user_id'];
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not update new password information', E_USER_WARNING);
			}

			include(ROOT_PATH . 'includes/class/emailer.php');
			$emailer = new emailer();

			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);

			$emailer->use_template('user_activate_passwd');
			$emailer->email_address($row['user_email']);
			$emailer->set_subject('新的密码已经生效');

			$emailer->assign_vars(array(
				'SITENAME' => $board_config['sitename'], 
				'USERNAME' => $username,
				'PASSWORD' => $user_password,
				'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

				'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
			);
			$emailer->send();
			$emailer->reset();

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="15;url=' . append_sid("index.php") . '">')
			);

			$message = '新的密码已经建立，请收信并查阅信息，以便了解如何使新的密码生效<br />点击 <a href="' . append_sid("index.php") . '">这里</a> 返回首页';

			trigger_error($message);
		}
		else
		{
			trigger_error('无法从论坛用户列表中找到与该邮件地址相匹配的用户名称', E_USER_ERROR);
		}
	}
	else
	{
		trigger_error('Could not obtain user information for sendpassword', E_USER_WARNING);
	}
}
else
{
	$username = '';
	$email = '';
}

page_header($page_title);

$template->set_filenames(array(
	'body' => 'ucp/send_password.tpl')
);

$template->assign_vars(array(
	'USERNAME' => $username,
	'EMAIL' => $email,
	'S_UCP_ACTION' => append_sid('ucp.php?mode=sendpassword'))
);

$template->pparse('body');

page_footer();

?>