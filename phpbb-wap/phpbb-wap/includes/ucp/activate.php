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
}

$sql = "SELECT user_active, user_id, username, user_email, user_newpasswd, user_actkey 
	FROM " . USERS_TABLE . "
	WHERE user_id = " . intval($_GET[POST_USERS_URL]);
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain user information', E_USER_WARNING);
}

if ( $row = $db->sql_fetchrow($result) )
{
	if ( $row['user_active'] && trim($row['user_actkey']) == '' )
	{
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.php") . '">')
		);

		trigger_error('您的帐号已经激活', E_USER_ERROR);
	}
	else if ((trim($row['user_actkey']) == trim($_GET['act_key'])) && (trim($row['user_actkey']) != ''))
	{
		if (intval($board_config['require_activation']) == USER_ACTIVATION_ADMIN && $row['user_newpasswd'] == '')
		{
			if (!$userdata['session_logged_in'])
			{
				login_back('ucp.php?mode=activate&amp;' . POST_USERS_URL . '=' . $row['user_id'] . '&act_key=' . trim($_GET['act_key']));
			}
			else if ($userdata['user_level'] != ADMIN)
			{
				trigger_error('您没有权限访问', E_USER_ERROR);
			}
		}

		$sql_update_pass = ( $row['user_newpasswd'] != '' ) ? ", user_password = '" . str_replace("\'", "''", $row['user_newpasswd']) . "', user_newpasswd = ''" : '';

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_active = 1, user_actkey = ''" . $sql_update_pass . " 
			WHERE user_id = " . $row['user_id']; 
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not update users table', E_USER_WARNING);
		}

		if ( intval($board_config['require_activation']) == USER_ACTIVATION_ADMIN && $sql_update_pass == '' )
		{
			require_once(ROOT_PATH . 'includes/class/emailer.php');
			$emailer = new emailer();

			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);

			$emailer->use_template('admin_welcome_activated');
			$emailer->email_address($row['user_email']);
			$emailer->set_subject('帐号已经激活');

			$emailer->assign_vars(array(
				'SITENAME' => $board_config['sitename'], 
				'USERNAME' => $row['username'],
				'PASSWORD' => $password_confirm,
				'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '')
			);
			$emailer->send();
			$emailer->reset();

			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.php") . '">')
			);

			trigger_error('该帐号现在已经激活', E_USER_ERROR);
		}
		else
		{
			$template->assign_vars(array(
				'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.php") . '">')
			);

			$message = ( $sql_update_pass == '' ) ? '您的帐号已经激活，谢谢您的注册' : '您的帐号已经生效，请使用您收到的邮件中提供的用户名称和用户密码进行登陆论坛'; 
			trigger_error($message);
		}
	}
	else
	{
		trigger_error('激活代码不正确', E_USER_ERROR);
	}
}
else
{
	trigger_error('对不起，您输入的用户不存在', E_USER_ERROR);
}

?>