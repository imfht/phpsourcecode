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

define('IN_LOGIN', true);
define('IN_PHPBB', true);
define('ROOT_PATH', './');
require(ROOT_PATH . 'common.php');
require(ROOT_PATH . 'mods/qqlogin/qc.class.php');

if (isset($_GET['username']))
{
	$_POST['username'] = $_GET['username'];
	$_POST['password'] = $_GET['password'];
	$_POST['login'] = 'Enter';
	//如果使用_GET的方式来登录, 注销...
	unset( $_POST['logout'], $_POST['autologin'] );
}

$userdata = $session->start($user_ip, PAGE_LOGIN);
init_userprefs($userdata);

if( isset($_POST['login']) || isset($_GET['login']) || isset($_POST['logout']) || isset($_GET['logout']) )
{
	if( ( isset($_POST['login']) || isset($_GET['login']) ) && (!$userdata['session_logged_in'] || isset($_POST['admin'])) )
	{
		$username = isset($_POST['username']) ? phpbb_clean_username($_POST['username']) : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		//$password_select = md5($password); 
		  
		if ( filter_var($username, FILTER_VALIDATE_EMAIL) )
		{
			$sql = "SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit
				FROM " . USERS_TABLE . "
				WHERE user_email = '" . str_replace("\\'", "''", $username) . "'";		
		}
		else
		{
			$sql = "SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit
				FROM " . USERS_TABLE . "
				WHERE username = '" . str_replace("\\'", "''", $username) . "'"; 
		}
		
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法取得用户信息', E_USER_WARNING);
		}

		if( $row = $db->sql_fetchrow($result) )
		{
			if( $row['user_level'] != ADMIN && $board_config['board_disable'] )
			{
				redirect(append_sid('index.php', true));
			}
			else
			{
				if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $row['user_last_login_try'] < (time() - ($board_config['login_reset_time'] * 60)))
				{
					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_login_tries = 0, user_last_login_try = 0 WHERE user_id = ' . $row['user_id']);
					$row['user_last_login_try'] = $row['user_login_tries'] = 0;
				}

				if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $board_config['max_login_attempts'] && 
					$row['user_last_login_try'] >= (time() - ($board_config['login_reset_time'] * 60)) && $row['user_login_tries'] >= $board_config['max_login_attempts'] && $userdata['user_level'] != ADMIN)
				{
					trigger_error('您已经连续输入错误密码 ' . $board_config['max_login_attempts'] . ' 次，请再过 ' . $board_config['login_reset_time'] . '分钟后再次尝试登录！' . back_link(append_sid('login.php')), E_USER_ERROR);
				}

				if( md5($password) == $row['user_password'] && $row['user_active'] )
				{
					$sql = "SELECT user_regdate
						FROM " . USERS_TABLE . "
						WHERE user_id = ".$row['user_id'];
					if ( !($result = $db->sql_query($sql)) )
					{
						trigger_error('无法取得用户注册日期', E_USER_WARNING);
					}
					if( $rowreg = $db->sql_fetchrow($result) )
					{
						$min_login_regdate = $board_config['min_login_regdate'] * 60;
						$time_activation = $rowreg['user_regdate'] + $min_login_regdate;
						$now_time = time();
						if ( $time_activation > $now_time )
						{
							$data_aktivacii = $time_activation - $now_time;
							$data_aktivacii_min = ceil($data_aktivacii / 60);
							trigger_error('当你注册成功那一刻起，您有 <strong> '.$board_config['min_login_regdate'].' </strong> 分钟的时间阅读<strong> <a href="' . append_sid('rules.php') . '">规则</a> </strong>，此期间您是不能登录的，还剩<strong> '.$data_aktivacii_min.' </strong>分钟', E_USER_ERROR);
						}
					}

					$autologin = ( isset($_POST['autologin']) ) ? TRUE : 0;

					$login_admin = (isset($_POST['admin'])) ? 1 : 0;
				
					$session_id = $session->update($row['user_id'], $user_ip, PAGE_INDEX, FALSE, $autologin, $login_admin);

					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_login_tries = 0, user_last_login_try = 0 WHERE user_id = ' . $row['user_id']);

					if( $session_id )
					{
						$posl_visit = create_date($board_config['default_dateformat'], $row['user_lastvisit'], $board_config['board_timezone']);

						$url = ( !empty($_POST['redirect']) ) ? urldecode($_POST['redirect']) : 'index.php';

						$login_infomation = '欢迎您：' . $row['username'] . '！<br />';
						$login_infomation .= ( $row['user_lastvisit'] != 0 ) ? '上次访问：' . $posl_visit . '<br />' : '';
						$login_infomation .= '浏览器：' . $user_agent . '<br />';
						$login_infomation .= 'IP地址：' . $client_ip . '<br />';
						$login_infomation .= '<a href="' . append_sid($url, true) . '">&lt;--快速进入</a><br/>';
						trigger_error($login_infomation, E_USER_ERROR);
				 	}
					else
					{
						trigger_error('无法获取用户Session', E_USER_WARNING);
					}
				}
				else
				{
					if ($row['user_id'] != ANONYMOUS)
					{
						$sql = 'UPDATE ' . USERS_TABLE . '
							SET user_login_tries = user_login_tries + 1, user_last_login_try = ' . time() . '
							WHERE user_id = ' . $row['user_id'];
						if (!$db->sql_query($sql))
						{
							trigger_error('无法更新 user 表', E_USER_WARNING);
						}
					}
					
					$

					$redirect = ( !empty($_POST['redirect']) ) ? $_POST['redirect'] : 'index.php';


					$message = '帐号或密码错误！<br />点击 <a href="login.php?redirect=' . $redirect . '">这里</a>返回登录页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

					trigger_error($message, E_USER_ERROR);
				}
			}
		}
		else
		{
			$redirect = ( !empty($_POST['redirect']) ) ? $_POST['redirect'] : 'index.php';

			$message = '帐号或密码错误！<br />点击 <a href="login.php?redirect=' . $redirect . '">这里</a>返回登录页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

			trigger_error($message, E_USER_ERROR);
		}
	}
	else if( ( isset($_GET['logout']) || isset($_POST['logout']) ) && $userdata['session_logged_in'] )
	{

		if( $userdata['session_logged_in'] )
		{
			$session->destroy();
		}

		trigger_error('欢迎下次访问！<br /><a href="' . append_sid('index.php') . '">&lt;--快速退出</a>');
	}
	else
	{
		trigger_error('请点击确认！<br /><a href="' . append_sid('index.php') . '">确认--&gt;</a>');
	}
}
else
{

	if( !$userdata['session_logged_in'] || (isset($_GET['admin']) && $userdata['session_logged_in'] && !$userdata['session_admin'] && $userdata['user_level'] == ADMIN))
	{
		
		$page_title = '会员登录';

		$redirect_url = '';

		if( isset($_POST['redirect']) || isset($_GET['redirect']) )
		{
			$redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : $_GET['redirect'];

			if ($redirect_url == '')
			{
				$redirect_url = 'index.php';
			}
		}

		$username = ( $userdata['user_id'] != ANONYMOUS ) ? $userdata['username'] : '';

		$s_hidden_fields = '<input type="hidden" name="redirect" value="' . $redirect_url . '" />';

		if (isset($_GET['admin']) && $userdata['session_logged_in'] && !$userdata['session_admin'] && $userdata['user_level'] == ADMIN)
		{
			$template->assign_block_vars('admin', array());
			$s_hidden_fields .= '<input type="hidden" name="admin" value="1" />';
		}
		else
		{
			$s_hidden_fields .= '';
			$template->assign_block_vars('login', array());
		}
			
		page_header($page_title);

		$template->set_filenames(array(
			'body' => 'login_body.tpl')
		);

		$template->assign_vars(array(
			'USERNAME' 			=> $username,
			'U_REGISTER'		=> append_sid('ucp.php?mode=register'),
			'U_SEND_PASSWORD' 	=> append_sid('ucp.php?mode=sendpassword'),
			'U_AUTOLOGIN' 		=> append_sid('rules.php?mode=faq&amp;act=autologin'),
			'U_QQ_LOGIN'		=> $QQC->qq_loginurl(),
			'S_LOGIN_ACTION'	=> append_sid('login.php'),
			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		redirect(append_sid('index.php', true));
	}
}
?>