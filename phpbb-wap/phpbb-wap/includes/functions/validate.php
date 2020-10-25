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
* 验证用户名
**/
function validate_username($username)
{
	global $db, $userdata;
		
	$illegal_username = array('*', '"', '&quot;', '@', ';');

	// 用户名不能包含空格
	$username = preg_replace('#\s+#', ' ', trim($username)); 
	$username = phpbb_clean_username($username);

	// 已注册的用户名
	$sql = 'SELECT username 
		FROM ' . USERS_TABLE . "
		WHERE LOWER(username) = '" . strtolower($username) . "'";
	if ($result = $db->sql_query($sql))
	{
		while ($row = $db->sql_fetchrow($result))
		{
			if (($userdata['session_logged_in'] && $row['username'] != $userdata['username']) || !$userdata['session_logged_in'])
			{
				$db->sql_freeresult($result);
				return array('error' => true, 'error_msg' => '对不起，已经有该用户了');
			}
		}
	}
	$db->sql_freeresult($result);

	// 用户名不能使用小组名称
	$sql = 'SELECT group_name
		FROM ' . GROUPS_TABLE . " 
		WHERE LOWER(group_name) = '" . strtolower($username) . "'";
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return array('error' => true, 'error_msg' => '对不起，已经有该用户了');
		}
	}
	$db->sql_freeresult($result);

	// 禁止使用的用户名
	$sql = "SELECT disallow_username
		FROM " . DISALLOW_TABLE;
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if (preg_match("#\b(" . str_replace("\*", ".*?", preg_quote($row['disallow_username'], '#')) . ")\b#i", $username))
				{
					$db->sql_freeresult($result);
					return array('error' => true, 'error_msg' => '对不起，这个用户名是系统禁止的');
				}
			}
			while($row = $db->sql_fetchrow($result));
		}
	}
	$db->sql_freeresult($result);

	// 用户名存在敏感字词
	$sql = "SELECT word 
		FROM  " . WORDS_TABLE;
	if ($result = $db->sql_query($sql))
	{
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if (preg_match("#\b(" . str_replace("\*", ".*?", preg_quote($row['word'], '#')) . ")\b#i", $username))
				{
					$db->sql_freeresult($result);
					return array('error' => true, 'error_msg' => '对不起，这个用户名包含敏感词');
				}
			}
			while ($row = $db->sql_fetchrow($result));
		}
	}
	$db->sql_freeresult($result);

	foreach ($illegal_username as $illegal_value)
	{
		if (strstr($username, $illegal_value))
		{
			return array('error' => true, 'error_msg' => '对不起，用户名存在不合法字符');
		}
	}

	return array('error' => false, 'error_msg' => '');
}

/**
* 验证电子邮件地址
**/
function validate_email($email)
{
	global $db;

	if ($email != '')
	{
		if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email))
		{
			$sql = "SELECT ban_email
				FROM " . BANLIST_TABLE;
			if ($result = $db->sql_query($sql))
			{
				if ($row = $db->sql_fetchrow($result))
				{
					do
					{
						$match_email = str_replace('*', '.*?', $row['ban_email']);
						if (preg_match('/^' . $match_email . '$/is', $email))
						{
							$db->sql_freeresult($result);
							return array('error' => true, 'error_msg' => '对不起，这个E-mail地址已经被列入黑名单');
						}
					}
					while($row = $db->sql_fetchrow($result));
				}
			}
			$db->sql_freeresult($result);

			$sql = "SELECT user_email
				FROM " . USERS_TABLE . "
				WHERE user_email = '" . str_replace("\'", "''", $email) . "'";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Couldn\'t obtain user email information.', E_USER_WARNING);
			}
		
			if ($row = $db->sql_fetchrow($result))
			{
				return array('error' => true, 'error_msg' => '对不起，这个E-mail地址已经被其他用户使用');
			}
			$db->sql_freeresult($result);

			return array('error' => false, 'error_msg' => '');
		}
	}

	return array('error' => true, 'error_msg' => '对不起，这个E-mail地址是不正确');
}

/**
* 验证其他选项，例如 QQ 等
**/
function validate_optional_fields(&$qq, &$aim, &$msnm, &$yim, &$website, &$location, &$occupation, &$interests, &$signature)
{
	$check_var_length = array('aim', 'msnm', 'yim', 'location', 'occupation', 'interests', 'signature');

	for($i = 0; $i < count($check_var_length); $i++)
	{
		if (strlen($$check_var_length[$i]) < 2)
		{
			$$check_var_length[$i] = '';
		}
	}

	if (!preg_match('/^[1-9][0-9]{4,9}$/', $qq))
	{
		$qq = '';
	}

	if ($website != '')
	{
		if (!preg_match('#^http[s]?:\/\/#i', $website))
		{
			$website = 'http://' . $website;
		}

		if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $website))
		{
			$website = '';
		}
	}
}

?>