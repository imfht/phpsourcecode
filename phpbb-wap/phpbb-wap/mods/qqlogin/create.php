<?php
/**
* @package phpBB-WAP MODS
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

if (!defined('IN_PHPBB'))
{
	exit;
}

session_start();

require_once('qc.class.php');
require_once(ROOT_PATH . 'includes/functions/validate.php');
require_once('functions.php');

$script_name 		= preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name 		= ( $script_name != '' ) ? $script_name . '/ucp.php' : 'ucp.php';
$server_name		= trim($board_config['server_name']);
$server_protocol 	= ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port 		= ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
$server_url 		= $server_protocol . $server_name . $server_port . $script_name;

$error 		= false;
$error_msg 	= '';

if ( isset($_POST['submit']) )
{
	
	$username 	= strtolower($_POST['username']);
	$email 		= strtolower($_POST['user_email']);
	$password 	= gen_rand_string(false);
	$gender		= 0;
	
	if ($username == '')
	{
		$error = TRUE;
		$error_msg .= '<p>用户名不能为空</p>';
	}

	if ($password == '')
	{
		$error = TRUE;
		$error_msg .= '<p>密码不能为空</p>';
	}
	
	$result = validate_openid($_SESSION['openid']);
	if ( $result['error'] )
	{
		$error = TRUE;
		$error_msg .= '<p>' . $result['error_msg'] . '</p>';
	}
	
	$result = validate_username($username);
	if ( $result['error'] )
	{
		$error = TRUE;
		$error_msg .= '<p>' . $result['error_msg'] . '</p>';
	}
	
	$result = validate_email($email);
	if ( $result['error'] )
	{
		$email = $userdata['user_email'];

		$error = TRUE;
		$error_msg .= '<p>' . $result['error_msg'] . '</p>';
	}
	
	if (!$error)
	{
		$sql = "SELECT MAX(user_id) AS total
			FROM " . USERS_TABLE;
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法取得最大的 user_id ', E_USER_WARNING);
		}

		if ( !($row = $db->sql_fetchrow($result)) )
		{
			trigger_error('无法取得最大的 user_id 数据', E_USER_ERROR);
		}
		
		$user_id 			= $row['total'] + 1;
		$board_timezone		= $board_config['board_timezone'];
		$default_dateformat	= $board_config['default_dateformat'];
		$topics_per_page 	= $board_config['topics_per_page'];
		$posts_per_page 	= $board_config['posts_per_page'];
		
		$sql = "INSERT INTO " . USERS_TABLE . "	(user_id, username, user_regdate, user_password, user_email, user_viewemail, user_attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_viewonline, user_notify_to_email, user_notify_to_pm, user_notify_pm, user_popup_pm, user_timezone, user_dateformat, user_level, user_allow_pm, user_message_quote, user_topics_per_page, user_posts_per_page, user_posl_red, user_index_spisok, user_style, qq_openid, user_gender, user_active, user_actkey) 
			VALUES ($user_id, '" . $db->sql_escape($username) . "', " . time() . ", '" . $db->sql_escape(md5($password)) . "', '" . $db->sql_escape($email) . "', 1, 0, 1, 0, 1, 1, 0, 0, 0, 1, $board_timezone, '" . $db->sql_escape($default_dateformat) . "', 0, 1, '" . $board_config['message_quote'] . "', '$topics_per_page', '$posts_per_page', '" . $board_config['posl_red'] . "', '" . $board_config['index_spisok'] . "', " . $board_config['default_style'] . ", '" . $db->sql_escape($_SESSION['openid']) . "', '0', ";
		
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

		if ( !$db->sql_query($sql, BEGIN_TRANSACTION) )
		{
			trigger_error('无法添加新用户数据', E_USER_WARNING);
		}
		
		$sql = "UPDATE " . USERS_TABLE . " 
			SET user_new_privmsg = '1', user_last_privmsg = '999'
			WHERE user_id = $user_id";
			
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法更新用户表信息', E_USER_WARNING);
		}
		
		$register_pm 			= '当您收到这条信息时，说明您的账户已成功激活，您可以删除这条消息，请勿回复此信息！';
		$privmsgs_date 			= date('U');
			
		$sql = 'INSERT INTO ' . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip) 
			VALUES ('0', '" . $db->sql_escape('恭喜您，您已成功注册为 ' . $board_config['sitename'] . ' 的会员！') . "', '2', " . $user_id . ", " . $privmsgs_date . ", '$user_ip')";
			
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法发送系统消息给新用户', E_USER_WARNING);
		}

		$privmsg_sent_id	= $db->sql_nextid();
		$privmsgs_text		= '恭喜您，您已成功注册为 %s 的会员！';
			
		$sql = 'INSERT INTO ' . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text) 
			VALUES ($privmsg_sent_id, '" . $db->sql_escape(addslashes(sprintf($register_pm, $board_config['sitename'], $board_config['sitename']))) . "')";
			
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法添加系统信息内容', E_USER_WARNING);
		}
			
		$sql = 'INSERT INTO ' . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator)
			VALUES ('', 'Personal User', 1, 0)";
			
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法设置为用户设置组', E_USER_WARNING);
		}

		$group_id = $db->sql_nextid();

		$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending)
			VALUES ($user_id, $group_id, 0)";
		if( !($result = $db->sql_query($sql, END_TRANSACTION)) )
		{
			trigger_error('无法添加用户小组', E_USER_WARNING);
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
		
		$unhtml_specialchars_match 		= array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
		$unhtml_specialchars_replace 	= array('>', '<', '"', '&');
		
		$emailer->assign_vars(array(
			'SITENAME' 		=> $board_config['sitename'],
			'WELCOME_MSG' 	=> '欢迎您访问' . $board_config['sitename'],
			'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
			'PASSWORD' 		=> $password,
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
				trigger_error('无法查询管理员邮件信息', E_USER_WARNING);
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

		$userinfo = '<br />用户名：' . $username . '<br />密码：' . $password . '<br />邮箱：' . $email; 
		$message = $message . $userinfo . '<br />点击 <a href="' . $QQC->qq_loginurl() . '">这里</a> 重新登录';

		trigger_error($message);
	}
	
}

if ( !isset($_SESSION["access_token"]) || !isset($_SESSION["openid"]) )
{
	exit;//请重新登录 
}

if ( $error )
{
	error_box('ERROR_BOX', $error_msg);
}

page_header('创建新帐号');

// 获取腾讯放回的用户信息
$info = $QQC->get_user_info($_SESSION["access_token"], $_SESSION["openid"]);

$template->set_filenames(array(
	'body' => 'qqlogin_create.tpl')
);

$template->assign_vars(array(
	'USERNAME' 			=> $info->nickname,
	'S_CREATE_ACTION'	=> append_sid(ROOT_PATH . 'loading.php?mod=qqlogin&amp;load=create'))
);

$template->pparse('body');

page_footer();
?>