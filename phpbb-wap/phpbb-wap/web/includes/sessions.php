<?php
/***************************************************************************
 *                                sessions.php
 *                            -------------------
 *   begin                : Saturday, Feb 13, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: sessions.php 5930 2006-05-18 19:23:07Z grahamje $
 *
 *
 ***************************************************************************/

function session_begin($user_id, $user_ip, $page_id, $auto_create = 0, $enable_autologin = 0, $admin = 0)
{
	global $db, $board_config;
	global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;

	$cookiename = $board_config['cookie_name'];
	$cookiepath = $board_config['cookie_path'];
	$cookiedomain = $board_config['cookie_domain'];
	$cookiesecure = $board_config['cookie_secure'];

	if ( isset($HTTP_COOKIE_VARS[$cookiename . '_sid']) || isset($HTTP_COOKIE_VARS[$cookiename . '_data']) )
	{
		$session_id = isset($HTTP_COOKIE_VARS[$cookiename . '_sid']) ? $HTTP_COOKIE_VARS[$cookiename . '_sid'] : '';
		$sessiondata = isset($HTTP_COOKIE_VARS[$cookiename . '_data']) ? unserialize(stripslashes($HTTP_COOKIE_VARS[$cookiename . '_data'])) : array();
		$sessionmethod = SESSION_METHOD_COOKIE;
	}
	else
	{
		$sessiondata = array();
		$session_id = ( isset($HTTP_GET_VARS['sid']) ) ? $HTTP_GET_VARS['sid'] : '';
		$sessionmethod = SESSION_METHOD_GET;
	}

	//
	if (!preg_match('/^[A-Za-z0-9]*$/', $session_id)) 
	{
		$session_id = '';
	}

	$page_id = (int) $page_id;

	$last_visit = 0;
	$current_time = time();

	if (isset($board_config['allow_autologin']) && !$board_config['allow_autologin'])
	{
		$enable_autologin = $sessiondata['autologinid'] = false;
	}

	$userdata = array();

	if ($user_id != ANONYMOUS)
	{
		if (isset($sessiondata['autologinid']) && (string) $sessiondata['autologinid'] != '' && $user_id)
		{
			$sql = 'SELECT u.* 
				FROM ' . USERS_TABLE . ' u, ' . SESSIONS_KEYS_TABLE . ' k
				WHERE u.user_id = ' . (int) $user_id . "
					AND u.user_active = 1
					AND k.user_id = u.user_id
					AND k.key_id = '" . md5($sessiondata['autologinid']) . "'";
			if (!($result = $db->sql_query($sql)))
			{
				message_die(CRITICAL_ERROR, 'Error doing DB query userdata row fetch', '', __LINE__, __FILE__, $sql);
			}

			$userdata = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		
			$enable_autologin = $login = 1;
		}
		else if (!$auto_create)
		{
			$sessiondata['autologinid'] = '';
			$sessiondata['userid'] = $user_id;

			$sql = 'SELECT *
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $user_id . '
					AND user_active = 1';
			if (!($result = $db->sql_query($sql)))
			{
				message_die(CRITICAL_ERROR, 'Error doing DB query userdata row fetch', '', __LINE__, __FILE__, $sql);
			}

			$userdata = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$login = 1;
		}
	}

	if (!sizeof($userdata) || !is_array($userdata) || !$userdata)
	{
		$sessiondata['autologinid'] = '';
		$sessiondata['userid'] = $user_id = ANONYMOUS;
		$enable_autologin = $login = 0;

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		if (!($result = $db->sql_query($sql)))
		{
			message_die(CRITICAL_ERROR, 'Error doing DB query userdata row fetch', '', __LINE__, __FILE__, $sql);
		}

		$userdata = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}

	$sql = "UPDATE " . SESSIONS_TABLE . "
		SET session_user_id = $user_id, session_start = $current_time, session_time = $current_time, session_page = $page_id, session_logged_in = $login, session_admin = $admin
		WHERE session_id = '" . $session_id . "' 
			AND session_ip = '$user_ip'";
	if ( !$db->sql_query($sql) || !$db->sql_affectedrows() )
	{
		$session_id = md5(dss_rand());

		$sql = "INSERT INTO " . SESSIONS_TABLE . "
			(session_id, session_user_id, session_start, session_time, session_ip, session_page, session_logged_in, session_admin)
			VALUES ('$session_id', $user_id, $current_time, $current_time, '$user_ip', $page_id, $login, $admin)";
		if ( !$db->sql_query($sql) )
		{
			message_die(CRITICAL_ERROR, 'Error creating new session', '', __LINE__, __FILE__, $sql);
		}
	}

	if ( $user_id != ANONYMOUS )
	{
		$last_visit = ( $userdata['user_session_time'] > 0 ) ? $userdata['user_session_time'] : $current_time; 

		if (!$admin)
		{
			$sql = "UPDATE " . USERS_TABLE . " 
				SET user_session_time = $current_time, user_session_page = $page_id, user_lastvisit = $last_visit
				WHERE user_id = $user_id";
			if ( !$db->sql_query($sql) )
			{
				message_die(CRITICAL_ERROR, 'Error updating last visit time', '', __LINE__, __FILE__, $sql);
			}
		}

		$userdata['user_lastvisit'] = $last_visit;

		if ($enable_autologin)
		{
			$auto_login_key = dss_rand() . dss_rand();
			
			if (isset($sessiondata['autologinid']) && (string) $sessiondata['autologinid'] != '')
			{
				$sql = 'UPDATE ' . SESSIONS_KEYS_TABLE . "
					SET last_ip = '$user_ip', key_id = '" . md5($auto_login_key) . "', last_login = $current_time
					WHERE key_id = '" . md5($sessiondata['autologinid']) . "'";
			}
			else
			{
				$sql = 'INSERT INTO ' . SESSIONS_KEYS_TABLE . "(key_id, user_id, last_ip, last_login)
					VALUES ('" . md5($auto_login_key) . "', $user_id, '$user_ip', $current_time)";
			}

			if ( !$db->sql_query($sql) )
			{
				message_die(CRITICAL_ERROR, 'Error updating session key', '', __LINE__, __FILE__, $sql);
			}
			
			$sessiondata['autologinid'] = $auto_login_key;
			unset($auto_login_key);
		}
		else
		{
			$sessiondata['autologinid'] = '';
		}

//		$sessiondata['autologinid'] = (!$admin) ? (( $enable_autologin && $sessionmethod == SESSION_METHOD_COOKIE ) ? $auto_login_key : '') : $sessiondata['autologinid'];
		$sessiondata['userid'] = $user_id;
	}

	$userdata['session_id'] = $session_id;
	$userdata['session_ip'] = $user_ip;
	$userdata['session_user_id'] = $user_id;
	$userdata['session_logged_in'] = $login;
	$userdata['session_page'] = $page_id;
	$userdata['session_start'] = $current_time;
	$userdata['session_time'] = $current_time;
	$userdata['session_admin'] = $admin;
	$userdata['session_key'] = $sessiondata['autologinid'];

	setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
	setcookie($cookiename . '_sid', $session_id, 0, $cookiepath, $cookiedomain, $cookiesecure);

	$SID = 'sid=' . $session_id;

	return $userdata;
}

function session_pagestart($user_ip, $thispage_id)
{
	global $db, $lang, $board_config;
	global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;

	$cookiename = $board_config['cookie_name'];
	$cookiepath = $board_config['cookie_path'];
	$cookiedomain = $board_config['cookie_domain'];
	$cookiesecure = $board_config['cookie_secure'];

	$current_time = time();
	unset($userdata);

	if ( isset($HTTP_COOKIE_VARS[$cookiename . '_sid']) || isset($HTTP_COOKIE_VARS[$cookiename . '_data']) )
	{
		$sessiondata = isset( $HTTP_COOKIE_VARS[$cookiename . '_data'] ) ? unserialize(stripslashes($HTTP_COOKIE_VARS[$cookiename . '_data'])) : array();
		$session_id = isset( $HTTP_COOKIE_VARS[$cookiename . '_sid'] ) ? $HTTP_COOKIE_VARS[$cookiename . '_sid'] : '';
		$sessionmethod = SESSION_METHOD_COOKIE;
	}
	else
	{
		$sessiondata = array();
		$session_id = ( isset($HTTP_GET_VARS['sid']) ) ? $HTTP_GET_VARS['sid'] : '';
		$sessionmethod = SESSION_METHOD_GET;
	}

	if (!preg_match('/^[A-Za-z0-9]*$/', $session_id))
	{
		$session_id = '';
	}

	$thispage_id = (int) $thispage_id;

	if ( !empty($session_id) )
	{
		$sql = "SELECT u.*, s.*
			FROM " . SESSIONS_TABLE . " s, " . USERS_TABLE . " u
			WHERE s.session_id = '$session_id'
				AND u.user_id = s.session_user_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(CRITICAL_ERROR, 'Error doing DB query userdata row fetch', '', __LINE__, __FILE__, $sql);
		}

		$userdata = $db->sql_fetchrow($result);

		if ( isset($userdata['user_id']) )
		{
			$ip_check_s = substr($userdata['session_ip'], 0, 6);
			$ip_check_u = substr($user_ip, 0, 6);

			if ($ip_check_s == $ip_check_u)
			{
				$SID = ($sessionmethod == SESSION_METHOD_GET || defined('IN_ADMIN')) ? 'sid=' . $session_id : '';

				if ( $current_time - $userdata['session_time'] > 60 )
				{
					$update_admin = (!defined('IN_ADMIN') && $current_time - $userdata['session_time'] > ($board_config['session_length']+60)) ? ', session_admin = 0' : '';

					$sql = "UPDATE " . SESSIONS_TABLE . " 
						SET session_time = $current_time, session_page = $thispage_id$update_admin
						WHERE session_id = '" . $userdata['session_id'] . "'";
					if ( !$db->sql_query($sql) )
					{
						message_die(CRITICAL_ERROR, 'Error updating sessions table', '', __LINE__, __FILE__, $sql);
					}

					if ( $userdata['user_id'] != ANONYMOUS )
					{
						$sql = "UPDATE " . USERS_TABLE . " 
							SET user_session_time = $current_time, user_session_page = $thispage_id
							WHERE user_id = " . $userdata['user_id'];
						if ( !$db->sql_query($sql) )
						{
							message_die(CRITICAL_ERROR, 'Error updating sessions table', '', __LINE__, __FILE__, $sql);
						}
					}

					session_clean($userdata['session_id']);

					setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
					setcookie($cookiename . '_sid', $session_id, 0, $cookiepath, $cookiedomain, $cookiesecure);
				}

				if ( isset($sessiondata['autologinid']) && $sessiondata['autologinid'] != '' )
				{
					$userdata['session_key'] = $sessiondata['autologinid'];
				}

				return $userdata;
			}
		}
	}

	$user_id = ( isset($sessiondata['userid']) ) ? intval($sessiondata['userid']) : ANONYMOUS;

	if ( !($userdata = session_begin($user_id, $user_ip, $thispage_id, TRUE)) )
	{
		message_die(CRITICAL_ERROR, 'Error creating user session', '', __LINE__, __FILE__, $sql);
	}

	return $userdata;

}

function session_end($session_id, $user_id)
{
	global $db, $lang, $board_config, $userdata;
	global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $SID;

	$cookiename = $board_config['cookie_name'];
	$cookiepath = $board_config['cookie_path'];
	$cookiedomain = $board_config['cookie_domain'];
	$cookiesecure = $board_config['cookie_secure'];

	$current_time = time();

	if (!preg_match('/^[A-Za-z0-9]*$/', $session_id))
	{
		return;
	}

	$sql = 'DELETE FROM ' . SESSIONS_TABLE . " 
		WHERE session_id = '$session_id' 
			AND session_user_id = $user_id";
	if ( !$db->sql_query($sql) )
	{
		message_die(CRITICAL_ERROR, 'Error removing user session', '', __LINE__, __FILE__, $sql);
	}

	if ( isset($userdata['session_key']) && $userdata['session_key'] != '' )
	{
		$autologin_key = md5($userdata['session_key']);
		$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
			WHERE user_id = ' . (int) $user_id . "
				AND key_id = '$autologin_key'";
		if ( !$db->sql_query($sql) )
		{
			message_die(CRITICAL_ERROR, 'Error removing auto-login key', '', __LINE__, __FILE__, $sql);
		}
	}

	$sql = 'SELECT *
		FROM ' . USERS_TABLE . '
		WHERE user_id = ' . ANONYMOUS;
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(CRITICAL_ERROR, 'Error obtaining user details', '', __LINE__, __FILE__, $sql);
	}
	if ( !($userdata = $db->sql_fetchrow($result)) )
	{
		message_die(CRITICAL_ERROR, 'Error obtaining user details', '', __LINE__, __FILE__, $sql);
	}
	$db->sql_freeresult($result);


	setcookie($cookiename . '_data', '', $current_time - 31536000, $cookiepath, $cookiedomain, $cookiesecure);
	setcookie($cookiename . '_sid', '', $current_time - 31536000, $cookiepath, $cookiedomain, $cookiesecure);

	return true;
}

function session_clean($session_id)
{
	global $board_config, $db;

	$sql = 'DELETE FROM ' . SESSIONS_TABLE . ' 
		WHERE session_time < ' . (time() - (int) $board_config['session_length']) . " 
			AND session_id <> '$session_id'";
	if ( !$db->sql_query($sql) )
	{
		message_die(CRITICAL_ERROR, 'Error clearing sessions table', '', __LINE__, __FILE__, $sql);
	}

	if (!empty($board_config['max_autologin_time']) && $board_config['max_autologin_time'] > 0)
	{
		$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
			WHERE last_login < ' . (time() - (86400 * (int) $board_config['max_autologin_time']));
		$db->sql_query($sql);
	}

	return true;
}

function session_reset_keys($user_id, $user_ip)
{
	global $db, $userdata, $board_config;

	$key_sql = ($user_id == $userdata['user_id'] && !empty($userdata['session_key'])) ? "AND key_id != '" . md5($userdata['session_key']) . "'" : '';

	$sql = 'DELETE FROM ' . SESSIONS_KEYS_TABLE . '
		WHERE user_id = ' . (int) $user_id . "
			$key_sql";

	if ( !$db->sql_query($sql) )
	{
		message_die(CRITICAL_ERROR, 'Error removing auto-login keys', '', __LINE__, __FILE__, $sql);
	}

	$where_sql = 'session_user_id = ' . (int) $user_id;
	$where_sql .= ($user_id == $userdata['user_id']) ? " AND session_id <> '" . $userdata['session_id'] . "'" : '';
	$sql = 'DELETE FROM ' . SESSIONS_TABLE . "
		WHERE $where_sql";
	if ( !$db->sql_query($sql) )
	{
		message_die(CRITICAL_ERROR, 'Error removing user session(s)', '', __LINE__, __FILE__, $sql);
	}

	if ( !empty($key_sql) )
	{
		$auto_login_key = dss_rand() . dss_rand();

		$current_time = time();
		
		$sql = 'UPDATE ' . SESSIONS_KEYS_TABLE . "
			SET last_ip = '$user_ip', key_id = '" . md5($auto_login_key) . "', last_login = $current_time
			WHERE key_id = '" . md5($userdata['session_key']) . "'";
		
		if ( !$db->sql_query($sql) )
		{
			message_die(CRITICAL_ERROR, 'Error updating session key', '', __LINE__, __FILE__, $sql);
		}

		$sessiondata['userid'] = $user_id;
		$sessiondata['autologinid'] = $auto_login_key;
		$cookiename = $board_config['cookie_name'];
		$cookiepath = $board_config['cookie_path'];
		$cookiedomain = $board_config['cookie_domain'];
		$cookiesecure = $board_config['cookie_secure'];

		setcookie($cookiename . '_data', serialize($sessiondata), $current_time + 31536000, $cookiepath, $cookiedomain, $cookiesecure);
		
		$userdata['session_key'] = $auto_login_key;
		unset($sessiondata);
		unset($auto_login_key);
	}
}

function append_sid($url, $non_html_amp = false)
{
	global $SID;

	if ( !empty($SID) && !preg_match('#sid=#', $url) )
	{
		$url .= ( ( strpos($url, '?') !== false ) ?  ( ( $non_html_amp ) ? '&' : '&amp;' ) : '?' ) . $SID;
	}

	return $url;
}

function session_userban($user_ip, $user_id)
{
	global $db, $userdata;
	preg_match('/(..)(..)(..)(..)/', $user_ip, $user_ip_parts);

	$sql = "SELECT ban_ip, ban_userid, ban_email 
		FROM " . BANLIST_TABLE . " 
		WHERE ban_ip IN ('" . $user_ip_parts[1] . $user_ip_parts[2] . $user_ip_parts[3] . $user_ip_parts[4] . "', '" . $user_ip_parts[1] . $user_ip_parts[2] . $user_ip_parts[3] . "ff', '" . $user_ip_parts[1] . $user_ip_parts[2] . "ffff', '" . $user_ip_parts[1] . "ffffff')
			OR ban_userid = $user_id";
	if ( $user_id != ANONYMOUS )
	{
		$sql .= " OR ban_email LIKE '" . str_replace("\'", "''", $userdata['user_email']) . "' 
			OR ban_email LIKE '" . substr(str_replace("\'", "''", $userdata['user_email']), strpos(str_replace("\'", "''", $userdata['user_email']), "@")) . "'";
	}
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(CRITICAL_ERROR, 'Could not obtain ban information', '', __LINE__, __FILE__, $sql);
	}

	if ( $ban_info = $db->sql_fetchrow($result) )
	{
		if ( $ban_info['ban_ip'] || $ban_info['ban_userid'] || $ban_info['ban_email'] )
		{
			$resban = $db->sql_query("SELECT * FROM " . REPUTATION_TABLE . " WHERE modification = 4 AND user_id = $user_id");
         			if ( $row = $db->sql_fetchrow($resban) )
			{
             			$moderid = $row['voter_id'];
				$resmoder = $db->sql_query("SELECT * FROM " . USERS_TABLE . " WHERE user_id = $moderid");
				if ( $mod = $db->sql_fetchrow($resmoder) )
				{
					$modername = $mod['username'];
				}
				$razbloktime = $row['expire'] - (time() + (3600 * $board_config['board_timezone']));
				$chas = floor(($razbloktime / 3600) + $board_config['board_timezone']);
				$min = floor(($razbloktime / 60) + ($board_config['board_timezone'] * 60));
				$dni = ceil((($razbloktime / 3600) + $board_config['board_timezone']) / 24);
				$ban_information = 'аккаунт был заблокирован модератором ' . $modername . ' в связи с несоблюдением правил форума!<br/>До автоматического восстановления осталось ' . $dni . ' дней (в часах ' . $chas . ' ч, в минутах  '. $min . ' мин)<br/>А пока есть время, читаем <a href="rules.php">правила</a> и думаем над своим поведением...';
			}
			else
			{
				$ban_information = 'аккаунт был заблокирован на неопределённое время в связи с несоблюдением правил форума!<br/>За дополнительной информацией обращайтесь к администрации форума...';
           			}
		}
	}
	else
	{
		$ban_information = false;
	}
	return $ban_information;
}

?>