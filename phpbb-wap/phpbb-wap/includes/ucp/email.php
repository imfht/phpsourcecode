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
	die("Hacking attempt");
	exit;
}

if (!$board_config['board_email_form'])
{
	redirect(append_sid('index.php', true));
}

if ( !empty($_GET[POST_USERS_URL]) || !empty($_POST[POST_USERS_URL]) )
{
	$user_id = ( !empty($_GET[POST_USERS_URL]) ) ? intval($_GET[POST_USERS_URL]) : intval($_POST[POST_USERS_URL]);
}
else
{
	trigger_error('用户不存在', E_USER_ERROR);
}

if ( !$userdata['session_logged_in'] )
{
	login_back("ucp.php&mode=email&" . POST_USERS_URL . "=$user_id");
}

$sql = "SELECT username, user_email, user_viewemail
	FROM " . USERS_TABLE . " 
	WHERE user_id = $user_id";
if ( $result = $db->sql_query($sql) )
{
	if ( $row = $db->sql_fetchrow($result) )
	{

		$username = $row['username'];
		$user_email = $row['user_email']; 

		if ( $row['user_viewemail'] || $userdata['user_level'] == ADMIN )
		{
			if ( time() - $userdata['user_emailtime'] < $board_config['flood_interval'] )
			{
				trigger_error('您当前不能发送其他的邮件给其他人，请稍候重试', E_USER_ERROR);
			}
			
			$error = FALSE;

			if ( isset($_POST['submit']) )
			{

				if ( !empty($_POST['subject']) )
				{
					$subject = trim(stripslashes($_POST['subject']));
				}
				else
				{
					$error = TRUE;
					$error_msg = '<p>电子邮件标题不能为空</p>';
				}

				if ( !empty($_POST['message']) )
				{
					$message = trim(stripslashes($_POST['message']));
				}
				else
				{
					$error = TRUE;
					$error_msg = '<p>电子邮件正文不能为空</p>';
				}

				if ( !$error )
				{
					$sql = "UPDATE " . USERS_TABLE . " 
						SET user_emailtime = " . time() . " 
						WHERE user_id = " . $userdata['user_id'];
					if ( $result = $db->sql_query($sql) )
					{
						include(ROOT_PATH . 'includes/class/emailer.php');
						$emailer = new emailer();

						$emailer->from($userdata['user_email']);
						$emailer->replyto($userdata['user_email']);

						$email_headers = 'X-AntiAbuse: Board servername - ' . $server_name . "\n";
						$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
						$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
						$email_headers .= 'X-AntiAbuse: User IP - ' . decode_ip($user_ip) . "\n";

						$emailer->use_template('profile_send_email');
						$emailer->email_address($user_email);
						$emailer->set_subject($subject);
						$emailer->extra_headers($email_headers);

						$emailer->assign_vars(array(
							'SITENAME' => $board_config['sitename'], 
							'BOARD_EMAIL' => $board_config['board_email'], 
							'FROM_USERNAME' => $userdata['username'], 
							'TO_USERNAME' => $username, 
							'MESSAGE' => $message)
						);
						$emailer->send();
						$emailer->reset();

						if ( !empty($_POST['cc_email']) )
						{
							$emailer->from($userdata['user_email']);
							$emailer->replyto($userdata['user_email']);
							$emailer->use_template('profile_send_email');
							$emailer->email_address($userdata['user_email']);
							$emailer->set_subject($subject);

							$emailer->assign_vars(array(
								'SITENAME' => $board_config['sitename'], 
								'BOARD_EMAIL' => $board_config['board_email'], 
								'FROM_USERNAME' => $userdata['username'], 
								'TO_USERNAME' => $username, 
								'MESSAGE' => $message)
							);
							$emailer->send();
							$emailer->reset();
						}

						trigger_error(back_link('邮件已发送！'), E_USER_ERROR);
					}
					else
					{
						trigger_error('无法更新最后发送邮件时间', E_USER_WARNING);
					}
				}
			}

			page_header($page_title);

			$template->set_filenames(array(
				'body' => 'ucp/send_email.tpl')
			);

			if ( $error )
			{
				error_box('ERROR_BOX', $error_msg);
			}

			$template->assign_vars(array(
				'USERNAME' => $username,

				'S_HIDDEN_FIELDS' => '', 
				'S_POST_ACTION' => append_sid("ucp.php?mode=email&amp;" . POST_USERS_URL . "=$user_id"))
			);

			$template->pparse('body');

			page_footer();
		}
		else
		{
			trigger_error(back_link('该用户不希望接收邮件'), E_USER_ERROR);
		}
	}
	else
	{
		trigger_error(back_link('用户不存在'), E_USER_ERROR);
	}
}
else
{
	trigger_error('无法查询用户信息', E_USER_WARNING);
}

?>