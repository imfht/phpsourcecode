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

session_start();

require_once('qc.class.php');
require_once('functions.php');

$error 		= false;
$error_msg 	= '';

if ( isset($_POST['submit']) )
{
	$username = strtolower($_POST['username']);
	$password = md5($_POST['password']);

	$sql = 'SELECT qq_openid 
		FROM ' . USERS_TABLE . " 
		WHERE qq_openid = '" . $db->sql_escape($_SESSION['openid']) . "'";
	if(!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询用户的openid信息', E_USER_WARNING);
	}
	if ($db->sql_numrows($result))
	{
		$error = TRUE;
		$error_msg .= '<p>对不起，您现在绑定的QQ帐号已绑定其它用户</p>';
	}
	
	if ( filter_var($username, FILTER_VALIDATE_EMAIL) )
	{
		$sql = 'SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit, qq_openid 
			FROM ' . USERS_TABLE . " 
			WHERE user_email = '" . $db->sql_escape($username) ."'
				AND user_password = '$password'";
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法查询用户的信息', E_USER_WARNING);
		}
		
		if (!$db->sql_numrows($result) )
		{
			$error = TRUE;
			$error_msg .= '<p>对不起，帐号密码不正确</p>';
		}
		
		$row = $db->sql_fetchrow($result);
		if ($row['qq_openid'] != '')
		{
			$error = TRUE;
			$error_msg .= '<p>对不起，使用该电子邮件的用户已绑定QQ登录</p>';
		}
		
		if ($row['user_id'] == ANONYMOUS)
		{
			$error = TRUE;
			$error_msg .= '<p>大哥？您绑定的是匿名用户</p>';
		}
	}
	else
	{
		$sql = 'SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit, qq_openid 
			FROM ' . USERS_TABLE . " 
			WHERE username = '" . $db->sql_escape($username) ."'
				AND user_password = '$password'";
			
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法查询用户的信息', E_USER_WARNING);
		}
		
		// 如果Open ID存在
		if (!$db->sql_numrows($result) )
		{
			$error = TRUE;
			$error_msg .= '<p>对不起，帐号密码不正确</p>';
		}
		
		$row = $db->sql_fetchrow($result);
		
		if ($row['qq_openid'] != '')
		{
			$error = TRUE;
			$error_msg .= '<p>对不起，使用该电子邮件的用户已绑定QQ登录</p>';
		}
		
		if ($row['user_id'] == ANONYMOUS)
		{
			$error = TRUE;
			$error_msg .= '<p>对不起，您绑定的是匿名用户</p>';
		}
	}
	
	if (!$error)
	{
		// 论坛已锁定，普通用户不能登录
		if( $row['user_level'] != ADMIN && $board_config['board_disable'] )
		{
			redirect(append_sid('index.php', true));
		}
		else
		{
			// 重置登录错误限制时间
			if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $row['user_last_login_try'] < (time() - ($board_config['login_reset_time'] * 60)))
			{
				
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_login_tries = 0, user_last_login_try = 0 
					WHERE user_id = ' . $row['user_id'];
				
				if (!$db->sql_query($sql))
				{
					trigger_error('无法更新用户表信息', E_USER_WARNING);
				}
				
				$row['user_last_login_try'] = $row['user_login_tries'] = 0;
			}

			// 您登录几次错误了？？
			if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $board_config['max_login_attempts'] && 
				$row['user_last_login_try'] >= (time() - ($board_config['login_reset_time'] * 60)) && $row['user_login_tries'] >= $board_config['max_login_attempts'] && $userdata['user_level'] != ADMIN)
			{
				trigger_error('您已经连续输入错误密码 ' . $board_config['max_login_attempts'] . ' 次，请再过 ' . $board_config['login_reset_time'] . '分钟后再次尝试登录！', E_USER_ERROR);
			}
			
			// 用户是已激活帐号
			if( $row['user_active'] )
			{
				$sql = 'SELECT user_regdate
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . $row['user_id'];
					
				if (!$result = $db->sql_query($sql))
				{
					trigger_error('无法查询用户注册日期信息', E_USER_WARNING);
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
						trigger_error('当你注册成功那一刻起，您有 <strong> '.$board_config['min_login_regdate'].' </strong> 分钟的时间阅读<strong> <a href="' . append_sid('rules.php') . '">规则</a> </strong>，此期间您是不能登录的，还剩<strong> '.$data_aktivacii_min.' </strong>分钟');
					}
				}
				
				$session_id = $session->update($row['user_id'], $user_ip, PAGE_INDEX, FALSE, 0, 0);
				
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_login_tries = 0, user_last_login_try = 0 
					WHERE user_id = ' . $row['user_id'];
					
				if(!$db->sql_query($sql))
				{
					trigger_error('无法查询用户表信息', E_USER_WARNING);
				}
				
				if( $session_id )
				{
				
					$posl_visit 	= create_date($board_config['default_dateformat'], $row['user_lastvisit'], $board_config['board_timezone']);
					$url 			= 'index.php';

					$login_infomation = '欢迎您：' . $row['username'] . '！<br />';
					$login_infomation .= ( $row['user_lastvisit'] != 0 ) ? '上次访问：' . $posl_visit . '<br />' : '';
					$login_infomation .= '浏览器：' . $user_agent . '<br />';
					$login_infomation .= 'IP地址：' . $client_ip . '<br />';
					$login_infomation .= '<a href="' . append_sid($url, true) . '">&lt;--快速进入</a><br/>';
					
					$sql = 'UPDATE ' . USERS_TABLE . " 
						SET qq_openid = '" . $db->sql_escape($_SESSION['openid']) . "' 
						WHERE user_id = " . (int) $row['user_id'];
					if(!$db->sql_query($sql))
					{
						trigger_error('无法更新用户openid', E_USER_WARNING);
					}
					
					trigger_error($login_infomation);
				}
				else
				{
					trigger_error('无法初始化session::登录', E_USER_WARNING);
				}
			}
			// 账户未激活
			else
			{	
				trigger_error('您的账户处于未激活状态！');
			}
		}	
	
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

page_header('绑定帐号');

// 获取腾讯放回的用户信息

$template->set_filenames(array(
	'body' => 'qqlogin_bind.tpl')
);

$template->assign_vars(array(
	'S_BIND_ACTION'	=> append_sid(ROOT_PATH . 'loading.php?mod=qqlogin&amp;load=bind'))
);

$template->pparse('body');

page_footer();
?>