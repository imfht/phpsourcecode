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

/*
* 请把该文件保存为UTF-8无BOM格式，否则不能识别
*/

/*
* MOD名称: QQ登录
* MOD支持地址: http://phpbb-wap.com/mod
* MOD描述: 腾讯QQ互联登录功能
* MOD作者: 爱疯的云
* MOD版本: v6.0
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

session_start();

//	如果 用户已登录
if ($userdata['session_logged_in'])
{
	// 跳转到首页，别来瞎参合
	redirect(append_sid('index.php', true));
}
// 	如果未登录
else
{
	
	// 如果是登录模式，且腾讯的code和state参数存在
	if (isset($_GET['code']) && isset($_GET['state'])) 
	{
		// 参数不能为空？
		//if (($_GET['code'] == '') && ($_GET['state'] == ''))
		
		require_once('qc.class.php');
		
		// 获取access_token和Open ID
		$QQC->qq_callback($_GET['code']);
		$QQC->get_openid();

		if (!isset($_SESSION['openid']) || empty($_SESSION['openid']))
		{
			trigger_error('无法取得 Open ID', E_USER_ERROR);
		}

		// 查询表中的open id
		$sql = 'SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit, user_avatar_type
			FROM ' . USERS_TABLE . " 
			WHERE qq_openid = '" . $db->sql_escape($_SESSION['openid']) . "'";
	
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法获得用户的信息', E_USER_WARNING);
		}
		
		// 如果open id存在
		if ($db->sql_numrows($result) )
		{
			$row = $db->sql_fetchrow($result);
			
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
						trigger_error('无法查询用户登录信息', E_USER_WARNING);
					}
					
					$row['user_last_login_try'] = $row['user_login_tries'] = 0;
				}

				// 您登录几次错误了？？
				if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $board_config['max_login_attempts'] && 
					$row['user_last_login_try'] >= (time() - ($board_config['login_reset_time'] * 60)) && $row['user_login_tries'] >= $board_config['max_login_attempts'] && $userdata['user_level'] != ADMIN)
				{
					trigger_error('您已经连续输入错误密码 ' . $board_config['max_login_attempts'] . ' 次，请再过 ' . $board_config['login_reset_time'] . '分钟后再次尝试登录！');
				}
				
				// 用户是已激活帐号
				if( $row['user_active'] )
				{
					$sql = 'SELECT user_regdate
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $row['user_id'];
						
					if (!$result = $db->sql_query($sql))
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
							trigger_error('当你注册成功那一刻起，您有 <strong> '.$board_config['min_login_regdate'].' </strong> 分钟的时间阅读<strong> <a href="' . append_sid('rules.php') . '">规则</a> </strong>，此期间您是不能登录的，还剩<strong> '.$data_aktivacii_min.' </strong>分钟');
						}
					}

					$session_id = $session->update($row['user_id'], $user_ip, PAGE_INDEX, FALSE, 0, 0);
					
					$sql = 'UPDATE ' . USERS_TABLE . ' 
						SET user_login_tries = 0, user_last_login_try = 0 
						WHERE user_id = ' . $row['user_id'];
						
					if(!$db->sql_query($sql))
					{
						trigger_error('无法更新用户登录信息', E_USER_WARNING);
					}

					if ($row['user_avatar_type'] == USER_AVATAR_NONE || $row['user_avatar_type'] == USER_AVATAR_REMOTE)
					{
						$qq_user_info = $QQC->get_user_info($_SESSION['access_token'], $_SESSION['openid']);

						$sql = 'UPDATE ' . USERS_TABLE . " 
							SET user_avatar = '{$qq_user_info->figureurl_qq_1}', user_avatar_type = " . USER_AVATAR_REMOTE . "
							WHERE user_id = {$row['user_id']}";

						if(!$db->sql_query($sql))
						{
							trigger_error('无法更新用户头像', E_USER_WARNING);
						}
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
						
						trigger_error($login_infomation);
				 	}
					else
					{
						trigger_error('无法初始化session::登录', E_USER_WARNING);
						//trigger_error('无法初始化session::登录', E_USER_ERROR);
					}
					
				}
				// 账户未激活
				else
				{	
					trigger_error('您的账户处于未激活状态！');
				}
			}	
		}
		else
		{
			// 创建或绑定帐号
			$message = '<p>由于您使用此QQ帐号第一次登录本站，您需要绑定或创建一个帐号</p>';
			$message .= '<p><a href="' . append_sid('loading.php?mod=qqlogin&amp;load=create') . '">创建新用户</a></p>';
			$message .= '<p><a href="' . append_sid('loading.php?mod=qqlogin&amp;load=bind') . '">绑定用户</a></p>';
			trigger_error($message);

		}
		
	}
	// 没有返回 code、state
	else
	{
		trigger_error('非法访问');
	}
	
}

?>