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

/**
* 	phpBB-WAP Session
*	作者: Crazy
*/
class Session
{
	var $SID = '';
	var $config;
	var $_db = NULL;
	var $_time = 0;
	var $page_id = 0;
	var $session_method;
	var $session_id = '';
	var $session_data = '';
	var $cookie_name = 'phpbbwap';
	var $cookie_path = '/';
	var $cookie_domain = '';
	var $cookiesecure = false;

	var $userdata = array();

	function __construct()
	{
		global $db, $board_config;

		$this->config = $board_config;
		
		$this->_db = $db;

		$this->cookie_name = $this->config['cookie_name'];
		$this->cookie_path = $this->config['cookie_path'];
		$this->cookie_path = $this->config['cookie_path'];
		$this->cookie_domain = $this->config['cookie_domain'];
		$this->cookie_secure = $this->config['cookie_secure'];

		// 如果Cookie存在则优先使用Cookie，否则使用GET
		if ( isset($_COOKIE[$this->cookie_name . '_sid']) || isset($_COOKIE[$this->cookie_name . '_data']) )
		{
			$this->session_id = isset($_COOKIE[$this->cookie_name . '_sid']) ? $_COOKIE[$this->cookie_name . '_sid'] : '';
			$this->session_data = isset($_COOKIE[$this->cookie_name . '_data']) ? unserialize(stripslashes($_COOKIE[$this->cookie_name . '_data'])) : array();
			$this->session_method = SESSION_METHOD_COOKIE;

		}
		else
		{
			$this->session_data = array();
			$this->session_id = ( isset($_GET['sid']) ) ? $_GET['sid'] : '';
			$this->session_method = SESSION_METHOD_GET;
		}

		// 检测Session ID是否合法
		if (!preg_match('/^[a-z0-9]{32}$/', $this->session_id))
		{
			$this->session_id = '';
		}

		// session 时间
		$this->_time = time();
	}

	/**
	*	Session start
	*/
	function start($user_ip, $page_id)
	{

		// 用户正在浏览的页面
		$this->page_id = (int) $page_id;

		// 如果Session存在
		if (!empty($this->session_id))
		{
			$sql = "SELECT u.*, s.*
				FROM " . SESSIONS_TABLE . " s, " . USERS_TABLE . " u
				WHERE s.session_id = '$this->session_id'
					AND u.user_id = s.session_user_id";

			if ( !($result = $this->_db->sql_query($sql)) )
			{
				trigger_error('无法从数据库中返回用户Session', E_USER_WARNING);
			}

			$this->userdata = $this->_db->sql_fetchrow($result);

			// 检查是否有返回Session ID
			if ( isset($this->userdata['user_id']) )
			{
				$ip_check_s = substr($this->userdata['session_ip'], 0, 6);//Session IP
				$ip_check_u = substr($user_ip, 0, 6);// 用户IP

				if ($ip_check_s == $ip_check_u)
				{
					$this->SID = ($this->session_method == SESSION_METHOD_GET || defined('IN_ADMIN')) ? 'sid=' . $this->session_id : '';
					
					if ( $this->_time - $this->userdata['session_time'] > 60 )
					{
						$update_admin = (!defined('IN_ADMIN') && $this->_time - $this->userdata['session_time'] > ($this->config['session_length'] + 60)) ? ', session_admin = 0' : '';
					}
					else
					{
						$update_admin = '';
					}

					$sql = "UPDATE " . SESSIONS_TABLE . " 
						SET session_time = $this->_time, session_page = $this->page_id$update_admin
						WHERE session_id = '" . $this->userdata['session_id'] . "'";

					if ( !$this->_db->sql_query($sql) )
					{
						trigger_error('更新Session表遇到错误', E_USER_WARNING);
					}

					if ( $this->userdata['user_id'] != ANONYMOUS )
					{
						$sql = "UPDATE " . USERS_TABLE . " 
							SET user_session_time = $this->_time, user_session_page = $this->page_id
							WHERE user_id = " . $this->userdata['user_id'];

						if ( !$this->_db->sql_query($sql) )
						{
							trigger_error('无法更新用户表的Session信息', E_USER_WARNING);
						}
					}

					$this->clean();

					setcookie($this->cookie_name . '_data', serialize($this->session_data), $this->_time + 31536000, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
					setcookie($this->cookie_name . '_sid', $this->session_id, 0, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);

				}

				if ( isset($this->session_data['autologinid']) && $this->session_data['autologinid'] != '' )
				{
					$this->userdata['session_key'] = $this->session_data['autologinid'];
				}

				return $this->userdata;

			}
		}

		$user_id = ( isset($this->session_data['userid']) ) ? intval($this->session_data['userid']) : ANONYMOUS;

		if ( !($this->userdata = $this->update($user_id, $user_ip, $this->page_id, TRUE)) )
		{
			trigger_error('无法创建用户Session信息', E_USER_WARNING);
		}

		return $this->userdata;
	}

	/*
	*	更新Session信息
	*	用于用户登录成功后的Session更新
	*/
	function update($user_id, $user_ip, $page_id, $auto_create = 0, $enable_autologin = 0, $admin = 0)
	{
		if (isset($this->config['allow_autologin']) && !$this->config['allow_autologin'])
		{
			$enable_autologin = $this->session_data['autologinid'] = false;
		}

		if ($user_id != ANONYMOUS)
		{
			if (isset($this->session_data['autologinid']) && (string) $this->session_data['autologinid'] != '' && $user_id)
			{

				$sql = 'SELECT u.* 
					FROM ' . USERS_TABLE . ' u, ' . SESSIONS_KEYS_TABLE . ' k
					WHERE u.user_id = ' . (int) $user_id . "
						AND u.user_active = 1
						AND k.user_id = u.user_id
						AND k.key_id = '" . md5($this->session_data['autologinid']) . "'";

				if (!($result = $this->_db->sql_query($sql)))
				{
					trigger_error('无法查询用户的Session信息', E_USER_WARNING);
				}

				$this->userdata = $this->_db->sql_fetchrow($result);

				$this->_db->sql_freeresult($result);

				$enable_autologin = $login = 1;

			}
			elseif (!$auto_create)
			{
				$this->session_data['autologinid'] = '';
				$this->session_data['userid'] = $user_id;

				$sql = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $user_id . '
						AND user_active = 1';
				if (!($result = $this->_db->sql_query($sql)))
				{
					trigger_error('无法查询用户表数据', E_USER_WARNING);
				}

				$this->userdata = $this->_db->sql_fetchrow($result);
				$this->_db->sql_freeresult($result);

				$login = 1;
			}
		}

		if (!count($this->userdata) || !is_array($this->userdata) || !$this->userdata) 
		{
			$this->session_data['autologinid'] = '';
			$this->session_data['userid'] = $user_id = ANONYMOUS;
			$enable_autologin = $login = 0;

			$sql = 'SELECT *
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $user_id;

			if (!($result = $this->_db->sql_query($sql)))
			{
				trigger_error('无法查询用户表数据', E_USER_WARNING);
			}

			$this->userdata = $this->_db->sql_fetchrow($result);
			$this->_db->sql_freeresult($result);
		}

		$sql = "UPDATE " . SESSIONS_TABLE . "
			SET session_user_id = $user_id, session_start = $this->_time, session_time = $this->_time, session_page = $page_id, session_logged_in = $login, session_admin = $admin
			WHERE session_id = '" . $this->session_id . "' 
				AND session_ip = '$user_ip'";

		if ( !$this->_db->sql_query($sql) || !$this->_db->sql_affectedrows() )
		{
			$this->session_id = md5(dss_rand());

			$sql = "INSERT INTO " . SESSIONS_TABLE . "
				(session_id, session_user_id, session_start, session_time, session_ip, session_page, session_logged_in, session_admin)
				VALUES ('$this->session_id', $user_id, $this->_time, $this->_time, '$user_ip', $page_id, $login, $admin)";
			if ( !$this->_db->sql_query($sql) )
			{
				trigger_error('创建新的Session信息错误');
			}
		}

		if ( $user_id != ANONYMOUS )
		{
			$last_visit = ( $this->userdata['user_session_time'] > 0 ) ? $this->userdata['user_session_time'] : $this->_time;

			if (!$admin)
			{
				$sql = "UPDATE " . USERS_TABLE . " 
					SET user_session_time = $this->_time, user_session_page = $page_id, user_lastvisit = $last_visit
					WHERE user_id = $user_id";
				if ( !$this->_db->sql_query($sql) )
				{
					trigger_error('无法更新用户最后浏览时间', E_USER_WARNING);
				}
			}

			$this->userdata['user_lastvisit'] = $last_visit;

			if ($enable_autologin)
			{
				$auto_login_key = dss_rand() . dss_rand();
				
				if (isset($this->session_data['autologinid']) && (string) $this->session_data['autologinid'] != '')
				{
					$sql = 'UPDATE ' . SESSIONS_KEYS_TABLE . "
						SET last_ip = '$user_ip', key_id = '" . md5($auto_login_key) . "', last_login = $this->_time
						WHERE key_id = '" . md5($this->session_data['autologinid']) . "'";
				}
				else
				{
					$sql = 'INSERT INTO ' . SESSIONS_KEYS_TABLE . "(key_id, user_id, last_ip, last_login)
						VALUES ('" . md5($auto_login_key) . "', $user_id, '$user_ip', $this->_time)";
				}

				if ( !$this->_db->sql_query($sql) )
				{
					trigger_error('无法更新Session Key', E_USER_WARNING);
				}
				
				$this->session_data['autologinid'] = $auto_login_key;
				unset($auto_login_key);
			}
			else
			{
				$this->session_data['autologinid'] = '';
			}
			//$this->session_data['autologinid'] = (!$admin) ? (( $enable_autologin && $this->session_method == SESSION_METHOD_COOKIE ) ? $auto_login_key : '') : $this->session_data['autologinid'];
			$this->session_data['userid'] = $user_id;
		}

		$this->userdata['session_id'] = $this->session_id;
		$this->userdata['session_ip'] = $user_ip;
		$this->userdata['session_user_id'] = $user_id;
		$this->userdata['session_logged_in'] = $login;
		$this->userdata['session_page'] = $this->page_id;
		$this->userdata['session_start'] = $this->_time;
		$this->userdata['session_time'] = $this->_time;
		$this->userdata['session_admin'] = $admin;
		$this->userdata['session_key'] = $this->session_data['autologinid'];

		setcookie($this->cookie_name . '_data', serialize($this->session_data), $this->_time + 31536000, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
		setcookie($this->cookie_name . '_sid', $this->session_id, 0, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);

		$this->SID = 'sid=' . $this->session_id;

		return $this->userdata;
	}

	/**
	*	清除无效Session
	*	有时从客户端获取到的信息（Cookie、GET）不一定准确，造成有些Session信息未能清除
	*	所以我们要清理它
	*/
	function clean()
	{

		$sql = 'DELETE FROM ' . SESSIONS_TABLE . ' 
			WHERE session_time < ' . (time() - (int) $this->config['session_length']) . " 
				AND session_id <> '$this->session_id'";
		if ( !$this->_db->sql_query($sql) )
		{
			trigger_error('无法清除Session信息', E_USER_WARNING);
		}

		if (!empty($this->config['max_autologin_time']) && $this->config['max_autologin_time'] > 0)
		{
			$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
				WHERE last_login < ' . (time() - (86400 * (int) $this->config['max_autologin_time']));
			$this->_db->sql_query($sql);
		}
	}

	/**
	*	销毁Session
	*	用户注销用户登录操作
	*/
	function destroy()
	{
		// 如果Session不合法则不要继续执行
		if (!preg_match('/^[a-z0-9]{32}$/', $this->session_id))
		{
			return;
		}

		$sql = 'DELETE FROM ' . SESSIONS_TABLE . " 
			WHERE session_id = '$this->session_id' 
				AND session_user_id = {$this->userdata['user_id']}";
		if ( !$this->_db->sql_query($sql) )
		{
			trigger_error('无法注销用户Session', E_USER_WARNING);
		}

		if ( isset($this->userdata['session_key']) && $this->userdata['session_key'] != '' )
		{
			$autologin_key = md5($this->userdata['session_key']);
			$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
				WHERE user_id = ' . (int) $this->userdata['user_id'] . "
					AND key_id = '$autologin_key'";
			if ( !$this->_db->sql_query($sql) )
			{
				trigger_error('无法注销用户Session Key', E_USER_WARNING);
			}
		}
	}

	function reset_keys($user_id, $user_ip)
	{

		$key_sql = ($user_id == $this->userdata['user_id'] && !empty($this->userdata['session_key'])) ? "AND key_id != '" . md5($this->userdata['session_key']) . "'" : '';

		$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
			WHERE user_id = ' . (int) $user_id . "
				$key_sql";

		if ( !$this->_db->sql_query($sql) )
		{
			trigger_error('无法注销用户Session Key', E_USER_WARNING);
		}

		$where_sql = 'session_user_id = ' . (int) $user_id;
		$where_sql .= ($user_id == $this->userdata['user_id']) ? " AND session_id <> '" . $this->userdata['session_id'] . "'" : '';
		$sql = 'DELETE FROM ' . SESSIONS_TABLE . "
			WHERE $where_sql";
		if ( !$this->_db->sql_query($sql) )
		{
			trigger_error('无法删除用户Session', E_USER_WARNING);
		}

		if ( !empty($key_sql) )
		{
			$auto_login_key = dss_rand() . dss_rand();

			$this->_time = time();
			
			$sql = 'UPDATE ' . SESSIONS_KEYS_TABLE . "
				SET last_ip = '$user_ip', key_id = '" . md5($auto_login_key) . "', last_login = {$this->_time}
				WHERE key_id = '" . md5($this->userdata['session_key']) . "'";
			
			if ( !$this->_db->sql_query($sql) )
			{
				trigger_error('无法更新Session Key', E_USER_WARNING);
			}

			$this->session_data['userid'] = $user_id;
			$this->session_data['autologinid'] = $auto_login_key;

			setcookie($this->cookie_name . '_data', serialize($this->session_data), $this->_time + 31536000, $this->cookie_path, $this->cookie_domain, $this->cookie_secure);
			
			$this->userdata['session_key'] = $auto_login_key;
			unset($this->session_data);
			unset($auto_login_key);
		}
	}
}

?>