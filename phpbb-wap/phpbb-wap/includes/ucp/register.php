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
}

$unhtml_specialchars_match 		= array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace 	= array('>', '<', '"', '&');

function show_agreement()
{
	global $template;

	$template->set_filenames(array(
		'body' => 'ucp/agreement.tpl')
	);
	
	$template->assign_vars(array(
		'U_AGREE' => append_sid('ucp.php?mode=register&amp;agreed=true'))
	);
	
	$template->pparse('body');

}

function get_crypted_name($name)
{
	global $board_config;

	$code_start = $board_config['board_startdate'] % 2 ? 65: 97;
	$first 		= chr($code_start + $board_config['board_startdate'] % 26);
	$var 		= $first . md5($name . $board_config['board_startdate']);

	return $var;
}

$error = FALSE;
$error_msg = '';
$page_title = ( $mode == 'editprofile' ) ? '修改密码' : '注册会员';

// 注册协议
if ( $mode == 'register' && !isset($_POST['agreed']) && !isset($_GET['agreed']) )
{
	page_header($page_title);

	show_agreement();

	page_footer();
}

$strip_var_list = array(
	'email' 		=> 'email',
	'confirm_code' 	=> 'confirm_code'
); 
$trim_var_list = array(
	'cur_password' 		=> 'cur_password',
	'new_password' 		=> 'new_password',
	'password_confirm' 	=> 'password_confirm'
); 

$gender = ( isset($_POST['gender']) ) ? intval($_POST['gender']) : 0;
	
// 验证用户提交的注册信息
if ( isset($_POST['submit']) || $mode == 'register' )
{
	include(ROOT_PATH . 'includes/functions/validate.php');
	include(ROOT_PATH . 'includes/functions/bbcode.php');
	include(ROOT_PATH . 'includes/functions/post.php');

	if ( $mode == 'editprofile' )
	{
		$user_id = intval($_POST['user_id']);
		$current_email = trim(htmlspecialchars($_POST['current_email']));
	}

	$strip_var_list = array(
		'email' 		=> 'email',
		'confirm_code' 	=> 'confirm_code'
	);

	foreach($strip_var_list as $var => $param)
	{
		$param = ( $mode == 'register' ) ? get_crypted_name($param) : $param;
		if ( !empty($_POST[$param]) )
		{
			$$var = trim(htmlspecialchars($_POST[$param]));
		}
	}

	$param = ( $mode == 'register' ) ? get_crypted_name('username') : 'username';
	$username = ( !empty($_POST[$param]) ) ? phpbb_clean_username($_POST[$param]) : '';

	$trim_var_list = array(
		'cur_password' 		=> 'cur_password',
		'new_password' 		=> 'new_password',
		'password_confirm' 	=> 'password_confirm'
	);

	foreach($trim_var_list as $var => $param)
	{
		$param = ( $mode == 'register' ) ? get_crypted_name($param) : $param;
		if ( !empty($_POST[$param]) )
		{
			$$var = trim($_POST[$param]);
		}
	}
	$sid = (isset($_POST['sid'])) ? $_POST['sid'] : 0;
}

if ($mode == 'register' && ($userdata['session_logged_in'] || $username == $userdata['username']))
{
	trigger_error('该用户名已经存在！', E_USER_ERROR);
}

if ( isset($_POST['submit']) )
{
	if ($sid == '' || $sid != $userdata['session_id'])
	{
		$error = true;
		$error_msg .= '<p>错误！请重新加载页面</p>';
	}

	$passwd_sql = '';
	if ( $mode == 'editprofile' )
	{
		if ( $user_id != $userdata['user_id'] )
		{
			$error = TRUE;
			$error_msg .= '<p>您无权更改他人信息</p>';
		}
	}
	else if ( $mode == 'register' )
	{
		if ($username == '' || $new_password == '' || $password_confirm == '' || $email == '' || $gender =='')
		{
			$error = TRUE;
			$error_msg .= '<p>标 * 选项不能留空</p>';
		}
	}

	if ($board_config['enable_confirm'] && $mode == 'register')
	{
		if (empty($_POST['confirm_id']))
		{
			$error = TRUE;
			$error_msg .= '<p>无效确认码</p>';
		}
		else
		{
			$confirm_id = htmlspecialchars($_POST['confirm_id']);
			if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
			{
				$confirm_id = '';
			}
			
			$sql = 'SELECT code 
				FROM ' . CONFIRM_TABLE . " 
				WHERE confirm_id = '$confirm_id' 
					AND session_id = '" . $userdata['session_id'] . "'";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not obtain confirmation code', E_USER_WARNING);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				if ($row['code'] != $confirm_code)
				{
					$error = TRUE;
					$error_msg .= '<p>无效确认码</p>';
				}
				else
				{
					$sql = 'DELETE FROM ' . CONFIRM_TABLE . " 
						WHERE confirm_id = '$confirm_id' 
							AND session_id = '" . $userdata['session_id'] . "'";
					if (!$db->sql_query($sql))
					{
						trigger_error('Could not delete confirmation code', E_USER_WARNING);
					}
				}
			}
			else
			{		
				$error = TRUE;
				$error_msg .= '<p>无效确认码</p>';
			}
			$db->sql_freeresult($result);
		}
	}

	if ( !empty($new_password) && !empty($password_confirm) )
	{
		if ( $new_password != $password_confirm )
		{
			$error = TRUE;
			$error_msg .= '<p>两次输入的密码不匹配</p>';
		}
		else if ( strlen($new_password) > 32 )
		{
			$error = TRUE;
			$error_msg .= '<p>密码过长</p>';
		}
		else
		{
			if ( $mode == 'editprofile' )
			{
				$sql = "SELECT user_password
					FROM " . USERS_TABLE . "
					WHERE user_id = $user_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not obtain user_password information', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);

				if ( $row['user_password'] != md5($cur_password) )
				{
					$error = TRUE;
					$error_msg .= '<p>当前密码不正确</p>';
				}
			}

			if ( !$error )
			{
				$new_password = md5($new_password);
				$passwd_sql = "user_password = '$new_password', ";
			}
		}
	}
	else if ( ( empty($new_password) && !empty($password_confirm) ) || ( !empty($new_password) && empty($password_confirm) ) )
	{
		$error = TRUE;
		$error_msg .= '<p>两次输入的密码不匹配</p>';
	}

	if ( $email != $userdata['user_email'] || $mode == 'register' )
	{
		$result = validate_email($email);
		if ( $result['error'] )
		{
			$email = $userdata['user_email'];

			$error = TRUE;
			$error_msg .= '<p>' . $result['error_msg'] . '</p>';
		}

		if ( $mode == 'editprofile' )
		{
			$sql = "SELECT user_password
				FROM " . USERS_TABLE . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain user_password information', E_USER_WARNING);
			}

			$row = $db->sql_fetchrow($result);

			if ( $row['user_password'] != md5($cur_password) )
			{
				$email = $userdata['user_email'];

				$error = TRUE;
				$error_msg .= '<p>当前密码不正确</p>';
			}
		}
	}

	$username_sql = '';
	if ( $board_config['allow_namechange'] || $mode == 'register' )
	{
		if ( empty($username) )
		{
			$error = TRUE;
		}
		else if ( $username != $userdata['username'] || $mode == 'register')
		{
			if (strtolower($username) != strtolower($userdata['username']) || $mode == 'register')
			{
				$result = validate_username($username);
				if ( $result['error'] )
				{
					$error = TRUE;
					$error_msg .= '<p>' . $result['error_msg'] . '</p>';
				}
			}

			if (!$error)
			{
				$username_sql = "username = '" . $db->sql_escape($username) . "', ";
			}
		}
	}

	if ( !$error )
	{

		if ( $mode == 'editprofile' )
		{
			if ( $email != $userdata['user_email'] && $board_config['require_activation'] != USER_ACTIVATION_NONE && $userdata['user_level'] != ADMIN )
			{
				$user_active = 0;

				$user_actkey = gen_rand_string(true);
				$key_len = 54 - ( strlen($server_url) );
				$key_len = ( $key_len > 6 ) ? $key_len : 6;
				$user_actkey = substr($user_actkey, 0, $key_len);

				if ( $userdata['session_logged_in'] )
				{
					$session->destroy();
				}
			}
			else
			{
				$user_active = 1;
				$user_actkey = '';
			}

			$sql = "UPDATE " . USERS_TABLE . "
				SET " . $username_sql . $passwd_sql . "user_email = '" . $db->sql_escape($email) ."', user_active = $user_active, user_actkey = '" . $db->sql_escape($user_actkey) . "'
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update users table', E_USER_WARNING);
			}

			if ( !empty($passwd_sql) )
			{
				$session->reset_keys($user_id, $user_ip);
			}

			if ( !$user_active )
			{

				include(ROOT_PATH . 'includes/class/emailer.php');
				$emailer = new emailer();

 				if ( $board_config['require_activation'] != USER_ACTIVATION_ADMIN )
 				{
 					$emailer->from($board_config['board_email']);
 					$emailer->replyto($board_config['board_email']);
 
 					$emailer->use_template('user_activate');
 					$emailer->email_address($email);
 					$emailer->set_subject('重新激活帐号!');
  
 					$emailer->assign_vars(array(
 						'SITENAME' 		=> $board_config['sitename'],
 						'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
 						'EMAIL_SIG' 	=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',
  
 						'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 					);
 					$emailer->send();
 					$emailer->reset();
 				}
 				else if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
 				{
 					$sql = 'SELECT user_email, user_lang 
 						FROM ' . USERS_TABLE . '
 						WHERE user_level = ' . ADMIN;
 					
 					if ( !($result = $db->sql_query($sql)) )
 					{
 						trigger_error('Could not select Administrators', E_USER_WARNING);
 					}
 					
 					while ($row = $db->sql_fetchrow($result))
 					{
 						$emailer->from($board_config['board_email']);
 						$emailer->replyto($board_config['board_email']);
 						
 						$emailer->email_address(trim($row['user_email']));
 						$emailer->use_template("admin_activate");
 						$emailer->set_subject('重新激活帐号');
 
 						$emailer->assign_vars(array(
 							'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
 							'EMAIL_SIG' 	=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),
 
 							'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 						);
 						$emailer->send();
 						$emailer->reset();
 					}
 					$db->sql_freeresult($result);
 				}

				$message = '您的用户资料已经更新，但因为修改了某些重要的用户资料部分，当前帐号暂时处于不可用。请收取邮件将被告知如何重新激活您的帐号，或者请求论坛管理员恢复激活您的帐号<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';
			}
			else
			{
				$message = '您的用户资料已经更新<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';
			}

			trigger_error($message);
		}
		else
		{
			$sql = "SELECT MAX(user_id) AS total
				FROM " . USERS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain next user_id information', E_USER_WARNING);
			}

			if ( !($row = $db->sql_fetchrow($result)) )
			{
				trigger_error('Could not obtain next user_id information', E_USER_WARNING);
			}
			
			$user_id 			= $row['total'] + 1;
			$board_timezone		= $board_config['board_timezone'];
			$default_dateformat	= $board_config['default_dateformat'];
			$topics_per_page 	= $board_config['topics_per_page'];
			$posts_per_page 	= $board_config['posts_per_page'];

			// 注册时执行的数据
			$sql = "INSERT INTO " . USERS_TABLE . "	(user_id, username, user_regdate, user_password, user_email, user_viewemail, user_attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_viewonline, user_notify_to_email, user_notify_to_pm, user_notify_pm, user_popup_pm, user_timezone, user_dateformat, user_level, user_allow_pm, user_message_quote, user_topics_per_page, user_posts_per_page, user_posl_red, user_index_spisok, user_style, user_gender, user_active, user_actkey) 
				VALUES ($user_id, '" . $db->sql_escape($username) . "', " . time() . ", '" . $db->sql_escape($new_password) . "', '" . $db->sql_escape($email) . "', 1, 0, 1, 0, 1, 1, 0, 0, 0, 1, $board_timezone, '" . $db->sql_escape($default_dateformat) . "', 0, 1, '" . $board_config['message_quote'] . "', '$topics_per_page', '$posts_per_page', '" . $board_config['posl_red'] . "', '" . $board_config['index_spisok'] . "', " . $board_config['default_style'] . ", '1', ";
			
			if ( $board_config['require_activation'] == USER_ACTIVATION_SELF || $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$user_actkey = gen_rand_string(true);
				$key_len = 54 - (strlen($server_url));
				$key_len = ( $key_len > 6 ) ? $key_len : 6;
				$user_actkey = substr($user_actkey, 0, $key_len);
				$sql .= "0, '" . $db->sql_escape($user_actkey) . "')";
			}
			else
			{
				$user_actkey = '';
				$sql .= "1, '')";
			}

			if ( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				trigger_error('Could not insert data into users table', E_USER_WARNING);
			}

			//mysql_query("set sql_mode=''");
			$sql = "UPDATE " . USERS_TABLE . " 
				SET user_new_privmsg = 1, user_last_privmsg = 999999
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update users table', E_USER_WARNING);
			}

			$register_pm 			= '当您收到这条信息时，说明您的账户已成功激活，您可以删除这条消息，请勿回复此信息！';
			$privmsgs_date 			= date('U');
			
			$sql = 'INSERT INTO ' . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip) 
				VALUES ('0', '" . $db->sql_escape('恭喜您，您已成功注册为 ' . $board_config['sitename'] . ' 的会员！') . "', -1, " . $user_id . ", " . $privmsgs_date . ", '$user_ip')";
			
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not insert private message sent info', E_USER_WARNING);
			}

			$privmsg_sent_id = $db->sql_nextid();
			$privmsgs_text	= '恭喜您，您已成功注册为 %s 的会员！';
			
			$sql = 'INSERT INTO ' . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text) VALUES ($privmsg_sent_id, '" . $db->sql_escape(addslashes(sprintf($register_pm, $board_config['sitename'], $board_config['sitename']))) . "')";
			
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not insert private message sent text', E_USER_WARNING);
			}
			
			$sql = 'INSERT INTO ' . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator)
				VALUES ('', 'Personal User', 1, 0)";
				
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not insert data into groups table', E_USER_WARNING);
			}

			$group_id = $db->sql_nextid();

			$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending)
				VALUES ($user_id, $group_id, 0)";
			if( !($result = $db->sql_query($sql, END_TRANSACTION)) )
			{
				trigger_error('Could not insert data into user_group table', E_USER_WARNING);
			}


			if ( $board_config['require_activation'] == USER_ACTIVATION_SELF )
			{
				$message 		= '对不起，因为您的帐号尚未激活，所以无法使用找回密码功能。请联系论坛管理员以获得更多的信息';
				$email_template	= 'user_welcome_inactive';
			}
			else if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$message 		= '您的帐号已经建立。然而，由于论坛限制帐号必须由论坛管理员激活。当您的帐号被论坛管理员激活时候将发送一个邮件通知给您';
				$email_template	= 'admin_welcome_inactive';
			}
			else
			{
				$message 		= '谢谢您的注册，帐号已经建立，马上就使用您的用户名称和用户密码登陆论坛';
				$email_template	= 'user_welcome';
			}

			require(ROOT_PATH . 'includes/class/emailer.php');
			
			$emailer = new emailer();

			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);
			$emailer->use_template($email_template);
			$emailer->email_address($email);
			$emailer->set_subject('欢迎您访问' . $board_config['sitename']);

			$emailer->assign_vars(array(
				'SITENAME' 		=> $board_config['sitename'],
				'WELCOME_MSG' 	=> '欢迎您访问' . $board_config['sitename'],
				'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
				'PASSWORD' 		=> $password_confirm,
				'EMAIL_SIG'		=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),
				'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
			);

			$emailer->send();
			$emailer->reset();

			if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$sql = "SELECT user_email 
					FROM " . USERS_TABLE . "
					WHERE user_level = " . ADMIN;
				
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not select Administrators', E_USER_WARNING);
				}
				
				while ($row = $db->sql_fetchrow($result))
				{
					$emailer->from($board_config['board_email']);
					$emailer->replyto($board_config['board_email']);
					$emailer->email_address(trim($row['user_email']));
					$emailer->use_template('admin_activate');
					$emailer->set_subject('新的用户帐号');

					$emailer->assign_vars(array(
						'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
						'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),

						'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
					);
					
					$emailer->send();
					$emailer->reset();
				}
				$db->sql_freeresult($result);
			}

			$message = $message . '<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

			trigger_error($message);
		} 
	}
}

if ( $error )
{
	$username = stripslashes($username);
	$email = stripslashes($email);
	$cur_password = '';
	$new_password = '';
	$password_confirm = '';
}
else if ( $mode == 'editprofile' )
{
	$user_id = $userdata['user_id'];
	$username = $userdata['username'];
	$email = $userdata['user_email'];
	$cur_password = '';
	$new_password = '';
	$password_confirm = '';
}

page_header($page_title);

if ( $mode == 'editprofile' )
{
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg = '您不能编辑他人的资料';
	}
}

include(ROOT_PATH . 'includes/functions/selects.php');

$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';
$s_hidden_fields .= '<input type="hidden" name="agreed" value="true" />';
$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

if( $mode == 'editprofile' )
{
	$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
	$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';
}

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

$template->set_filenames(array(
	'body' => 'ucp/add_user.tpl')
);

if ( $mode == 'editprofile' )
{
	$template->assign_block_vars('switch_edit_profile', array());
}
else
{
	$template->assign_block_vars('else_edit_profile', array());
}

if ( ($mode == 'register') || ($board_config['allow_namechange']) )
{
	$template->assign_block_vars('switch_namechange_allowed', array());
}
else
{
	$template->assign_block_vars('switch_namechange_disallowed', array());
}

$confirm_image = '';
if ($board_config['enable_confirm'] && $mode == 'register')
{
	$sql = 'SELECT session_id 
		FROM ' . SESSIONS_TABLE; 
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not select session data', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		$confirm_sql = '';
		do
		{
			$confirm_sql .= (($confirm_sql != '') ? ', ' : '') . "'" . $row['session_id'] . "'";
		}
		while ($row = $db->sql_fetchrow($result));
	
		$sql = 'DELETE FROM ' .  CONFIRM_TABLE . " 
			WHERE session_id NOT IN ($confirm_sql)";
		if (!$db->sql_query($sql))
		{
			trigger_error('Could not delete stale confirm data', E_USER_WARNING);
		}
	}
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(session_id) AS attempts 
		FROM ' . CONFIRM_TABLE . " 
		WHERE session_id = '" . $userdata['session_id'] . "'";
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not obtain confirm code count', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		if ($row['attempts'] > 3)
		{
			trigger_error('您已经达到了注册尝试限制数量，请稍后再试', E_USER_ERROR);
		}
	}
	$db->sql_freeresult($result);

	$allowed_symbols = "0123456789";
	$length = 5;
		while(true){
			$code='';
			for($i=0;$i<$length;$i++){
				$code.=$allowed_symbols{mt_rand(0,strlen($allowed_symbols)-1)};
			}
			if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $code)) break;
		}

	$confirm_id = md5(uniqid($user_ip));

	$sql = 'INSERT INTO ' . CONFIRM_TABLE . " (confirm_id, session_id, code) 
		VALUES ('$confirm_id', '". $userdata['session_id'] . "', '$code')";
	if (!$db->sql_query($sql))
	{
		trigger_error('Could not insert new confirm code information', E_USER_WARNING);
	}

	unset($code);
	
	$confirm_image = '<img src="' . append_sid("ucp.php?mode=confirm&amp;id=$confirm_id") . '" alt="" title="" />';
	$s_hidden_fields .= '<input type="hidden" name="confirm_id" value="' . $confirm_id . '" />';
	$template->assign_block_vars('switch_confirm', array());
}

$warning = ( $mode == 'editprofile' ) ? '' : '（<font color="red">必填</font>）';

$gender_male_checked = '';
$gender_female_checked = '';
$gender_no_specify_checked = '';
switch ($gender) 
{ 
	case 0:
		$gender_no_specify_checked = 'checked="checked"';
		break; 
	case 1: 
		$gender_male_checked = 'checked="checked"';
		break; 
	case 2:
		$gender_female_checked = 'checked="checked"';
		break; 
	default:
		$gender_no_specify_checked = 'checked="checked"';
}
foreach ( $strip_var_list as $var => $param )
{
	$template->assign_vars(array(
		'VAR_' . strtoupper($param) => ( $mode == 'register' ) ? get_crypted_name($param) : $param
		)
	);
}

foreach ( $trim_var_list as $var => $param )
{
	$template->assign_vars(array(
		'VAR_' . strtoupper($param) => ( $mode == 'register' ) ? get_crypted_name($param) : $param
		)
	);
}

$template->assign_vars(array(
	'VAR_USERNAME' => ( $mode == 'register' ) ? get_crypted_name('username') : 'username'
	)
);

$template->assign_vars(array(
	'USERNAME' 					=> isset($username) ? $username : '',
	'CUR_PASSWORD'				=> isset($cur_password) ? $cur_password : '',
	'NEW_PASSWORD' 				=> isset($new_password) ? $new_password : '',
	'PASSWORD_CONFIRM' 			=> isset($password_confirm) ? $password_confirm : '',
	'EMAIL' 					=> isset($email) ? $email : '',
	'CONFIRM_IMG' 				=> $confirm_image, 
	
	'LOCK_GENDER' 				=>($mode!='register') ? 'DISABLED':'', 
	'GENDER' 					=> $gender, 
	
	'GENDER_NO_SPECIFY_CHECKED' => $gender_no_specify_checked, 
	'GENDER_MALE_CHECKED' 		=> $gender_male_checked,
	'GENDER_FEMALE_CHECKED' 	=> $gender_female_checked, 
	
	'L_WARNING' 				=> $warning,

	'U_VIEWPROFILE'				=> append_sid('ucp.php?mode=viewprofile&u=' . $userdata['user_id']),
	
	'S_HIDDEN_FIELDS' 			=> $s_hidden_fields)
);

$template->pparse('body');

page_footer();

?>