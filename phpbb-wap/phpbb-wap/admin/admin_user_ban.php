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

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['会员']['黑名单'] = $filename;

	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');

require('pagestart.php');

if ( isset($_POST['submit']) )
{
	$user_bansql = '';
	$email_bansql = '';
	$ip_bansql = '';

	$user_list = array();
	if ( !empty($_POST['username']) )
	{
		$this_userdata = get_userdata($_POST['username'], true);
		if( !$this_userdata )
		{
			trigger_error('您输入的用户可能不存在', E_USER_ERROR);
		}

		$user_list[] = $this_userdata['user_id'];
	}

	$ip_list = array();
	if ( isset($_POST['ban_ip']) )
	{
		$ip_list_temp = explode(',', $_POST['ban_ip']);

		for($i = 0; $i < count($ip_list_temp); $i++)
		{
			if ( preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', trim($ip_list_temp[$i]), $ip_range_explode) )
			{

				$ip_1_counter 	= $ip_range_explode[1];
				$ip_1_end 		= $ip_range_explode[5];

				while ( $ip_1_counter <= $ip_1_end )
				{
					$ip_2_counter 	= ( $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[2] : 0;
					$ip_2_end 		= ( $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[6];

					if ( $ip_2_counter == 0 && $ip_2_end == 254 )
					{
						$ip_2_counter 	= 255;
						$ip_2_fragment 	= 255;

						$ip_list[] 		= encode_ip("$ip_1_counter.255.255.255");
					}

					while ( $ip_2_counter <= $ip_2_end )
					{
						$ip_3_counter 	= ( $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[3] : 0;
						$ip_3_end 		= ( $ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[7];

						if ( $ip_3_counter == 0 && $ip_3_end == 254 )
						{
							$ip_3_counter 	= 255;
							$ip_3_fragment 	= 255;

							$ip_list[] 		= encode_ip("$ip_1_counter.$ip_2_counter.255.255");
						}

						while ( $ip_3_counter <= $ip_3_end )
						{
							$ip_4_counter 	= ( $ip_3_counter == $ip_range_explode[3] && $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[4] : 0;
							$ip_4_end 		= ( $ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end ) ? 254 : $ip_range_explode[8];

							if ( $ip_4_counter == 0 && $ip_4_end == 254 )
							{
								$ip_4_counter 	= 255;
								$ip_4_fragment 	= 255;

								$ip_list[] 		= encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.255");
							}

							while ( $ip_4_counter <= $ip_4_end )
							{
								$ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.$ip_4_counter");
								$ip_4_counter++;
							}
							$ip_3_counter++;
						}
						$ip_2_counter++;
					}
					$ip_1_counter++;
				}
			}
			else if ( preg_match('/^([\w\-_]\.?){2,}$/is', trim($ip_list_temp[$i])) )
			{
				$ip = gethostbynamel(trim($ip_list_temp[$i]));

				for($j = 0; $j < count($ip); $j++)
				{
					if ( !empty($ip[$j]) )
					{
						$ip_list[] = encode_ip($ip[$j]);
					}
				}
			}
			else if ( preg_match('/^([0-9]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$/', trim($ip_list_temp[$i])) )
			{
				$ip_list[] = encode_ip(str_replace('*', '255', trim($ip_list_temp[$i])));
			}
		}
	}

	$email_list = array();
	if ( isset($_POST['ban_email']) )
	{
		$email_list_temp = explode(',', $_POST['ban_email']);

		for($i = 0; $i < count($email_list_temp); $i++)
		{
			if (preg_match('/^(([a-z0-9&\'\.\-_\+])|(\*))+@(([a-z0-9\-])|(\*))+\.([a-z0-9\-]+\.)*?[a-z]+$/is', trim($email_list_temp[$i])))
			{
				$email_list[] = trim($email_list_temp[$i]);
			}
		}
	}

	$sql = 'SELECT *
		FROM ' . BANLIST_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error("Couldn't obtain banlist information", E_USER_WARNING);
	}

	$current_banlist = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$kill_session_sql = '';
	for($i = 0; $i < count($user_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $user_list[$i] == $current_banlist[$j]['ban_userid'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			$kill_session_sql .= ( ( $kill_session_sql != '' ) ? ' OR ' : '' ) . "session_user_id = " . $user_list[$i];

			$sql = 'INSERT INTO ' . BANLIST_TABLE . ' (ban_userid)
				VALUES (' . $user_list[$i] . ')';
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t insert ban_userid info into database', E_USER_WARNING);
			}
		}
	}

	for($i = 0; $i < count($ip_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $ip_list[$i] == $current_banlist[$j]['ban_ip'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($ip_list[$i], 2, '.')) )
			{
				$kill_ip_sql = "session_ip LIKE '" . str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($ip_list[$i], 2, "."))) . "'";
			}
			else
			{
				$kill_ip_sql = "session_ip = '" . $ip_list[$i] . "'";
			}

			$kill_session_sql .= ( ( $kill_session_sql != '' ) ? ' OR ' : '' ) . $kill_ip_sql;

			$sql = 'INSERT INTO ' . BANLIST_TABLE . " (ban_ip)
				VALUES ('" . $ip_list[$i] . "')";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t insert ban_ip info into database', E_USER_WARNING);
			}
		}
	}

	if ( $kill_session_sql != '' )
	{
		$sql = 'DELETE FROM ' . SESSIONS_TABLE . "
			WHERE $kill_session_sql";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Couldn\'t delete banned sessions from database', E_USER_WARNING);
		}
	}

	for($i = 0; $i < count($email_list); $i++)
	{
		$in_banlist = false;
		for($j = 0; $j < count($current_banlist); $j++)
		{
			if ( $email_list[$i] == $current_banlist[$j]['ban_email'] )
			{
				$in_banlist = true;
			}
		}

		if ( !$in_banlist )
		{
			$sql = 'INSERT INTO ' . BANLIST_TABLE . " (ban_email)
				VALUES ('" . $db->sql_escape($email_list[$i]) . "')";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t insert ban_email info into database', E_USER_WARNING);
			}
		}
	}

	$where_sql = '';

	if ( isset($_POST['unban_user']) )
	{
		$user_list = $_POST['unban_user'];

		for($i = 0; $i < count($user_list); $i++)
		{
			if ( $user_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . intval($user_list[$i]);
			}
		}
	}

	if ( isset($_POST['unban_ip']) )
	{
		$ip_list = $_POST['unban_ip'];

		for($i = 0; $i < count($ip_list); $i++)
		{
			if ( $ip_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . $db->sql_escape($ip_list[$i]);
			}
		}
	}

	if ( isset($_POST['unban_email']) )
	{
		$email_list = $_POST['unban_email'];

		for($i = 0; $i < count($email_list); $i++)
		{
			if ( $email_list[$i] != -1 )
			{
				$where_sql .= ( ( $where_sql != '' ) ? ', ' : '' ) . $db->sql_escape($email_list[$i]);
			}
		}
	}

	if ( $where_sql != '' )
	{
		$sql = 'DELETE FROM ' . BANLIST_TABLE . "
			WHERE ban_id IN ($where_sql)";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Couldn\'t delete ban info from database', E_USER_WARNING);
		}
	}

	$message = '黑名单列表已经成功更新<br />点击 <a href="' . append_sid('admin_user_ban.php') . '">这里</a> 返回黑名单列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

	trigger_error($message);

}
else
{
	$template->set_filenames(array(
		'body' => 'admin/user_ban_body.tpl')
	);

	$template->assign_vars(array(
		'S_BANLIST_ACTION' 		=> append_sid('admin_user_ban.php'))
	);

	$userban_count = 0;
	$ipban_count = 0;
	$emailban_count = 0;

	$sql = 'SELECT b.ban_id, u.user_id, u.username
		FROM ' . BANLIST_TABLE . ' b, ' . USERS_TABLE . ' u
		WHERE u.user_id = b.ban_userid
			AND b.ban_userid <> 0
			AND u.user_id <> ' . ANONYMOUS . '
		ORDER BY u.user_id ASC';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not select current user_id ban list', E_USER_WARNING);
	}

	$user_list = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$select_userlist = '';
	for($i = 0; $i < count($user_list); $i++)
	{
		$select_userlist .= '<input type="checkbox" name="unban_user[]" value="' . $user_list[$i]['ban_id'] . '" /> ' . $user_list[$i]['username'] . '<br/>';
		$userban_count++;
	}

	if( $select_userlist == '' )
	{
		$select_userlist = '没有禁止任何用户';
	}

	$sql = 'SELECT ban_id, ban_ip, ban_email
		FROM ' . BANLIST_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not select current ip ban list', E_USER_WARNING);
	}

	$banlist = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	$select_iplist = '';
	$select_emaillist = '';

	for($i = 0; $i < count($banlist); $i++)
	{
		$ban_id = $banlist[$i]['ban_id'];

		if ( !empty($banlist[$i]['ban_ip']) )
		{
			$ban_ip = str_replace('255', '*', decode_ip($banlist[$i]['ban_ip']));
			$select_iplist .= '<input type="checkbox" name="unban_ip[]" value="' . $ban_id . '" /> ' . $ban_ip . '<br/>';
			$ipban_count++;
		}
		else if ( !empty($banlist[$i]['ban_email']) )
		{
			$ban_email = $banlist[$i]['ban_email'];
			$select_emaillist .= '<input type="checkbox" name="unban_email[]" value="' . $ban_id . '" /> ' . $ban_email . '<br/>';
			$emailban_count++;
		}
	}

	if ( $select_iplist == '' )
	{
		$select_iplist = '没有禁止任何IP';
	}

	if ( $select_emaillist == '' )  
	{
		$select_emaillist = '没有禁止任何E-mail';
	}

	$template->assign_vars(array(
		'U_SEARCH_USER' 			=> append_sid(ROOT_PATH . 'search.php?mode=searchuser'), 
		'S_UNBAN_USERLIST_SELECT' 	=> $select_userlist,
		'S_UNBAN_IPLIST_SELECT' 	=> $select_iplist,
		'S_UNBAN_EMAILLIST_SELECT' 	=> $select_emaillist,
		'S_BAN_ACTION' 				=> append_sid('admin_user_ban.php'))
	);
}

$template->pparse('body');

page_footer();

?>