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

define('IN_PHPBB', true);
define('ROOT_PATH', './');
include(ROOT_PATH . 'common.php');

/**
* 取得用户信息
*
**/
function generate_user_info(&$row, $date_format, $group_mod, &$from, &$posts, &$joined, &$poster_avatar, &$profile_img, &$profile, &$search_img, &$search, &$pm_img, &$pm, &$email_img, &$email, &$www_img, &$www, &$qq_status_img, &$qq_img, &$qq, &$aim_img, &$aim, &$msn_img, &$msn, &$yim_img, &$yim)
{
	global $board_config;

	$from 	= ( !empty($row['user_from']) ) ? $row['user_from'] : '&nbsp;';
	$joined	= create_date($date_format, $row['user_regdate'], $board_config['board_timezone']);
	$posts 	= ( $row['user_posts'] ) ? $row['user_posts'] : 0;

	$poster_avatar = '';
	if ( $row['user_avatar_type'] && $row['user_id'] != ANONYMOUS && $row['user_allowavatar'] )
	{
		switch( $row['user_avatar_type'] )
		{
			case USER_AVATAR_UPLOAD:
				$poster_avatar = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
				break;
			case USER_AVATAR_REMOTE:
				$poster_avatar = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" alt="" border="0" />' : '';
				break;
			//case USER_AVATAR_GALLERY:
			//	$poster_avatar = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
			//	break;
		}
	}

	if ( !empty($row['user_viewemail']) || $group_mod )
	{
		$email_uri = ( $board_config['board_email_form'] ) ? append_sid('ucp.php?mode=email&amp;' . POST_USERS_URL .'=' . $row['user_id']) : 'mailto:' . $row['user_email'];
		$email = '<a href="' . $email_uri . '">发邮件</a>';
	}
	else
	{
		$email = '';
	}

	$temp_url	= append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['user_id']);
	$profile	= '<a href="' . $temp_url . '">个人资料</a>';

	$temp_url	= append_sid('privmsg.php?mode=post&amp;' . POST_USERS_URL . '=' . $row['user_id']);
	$pm 		= '<a href="' . $temp_url . '">发信息</a>';

	//$www = ( $row['user_website'] ) ? '<a href="' . $row['user_website'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';
	
	//这里可以用作ＱＱ在线状态
	//if ( !empty($row['user_qq']) )
	//{
	//	$icq_status_img 	= '<a href="http://wwp.icq.com/' . $row['user_qq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $row['user_qq'] . '&img=5" width="18" height="18" border="0" /></a>';
	//	$icq_img 			= '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0" /></a>';
	//	$icq 				=  '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '">' . $lang['ICQ'] . '</a>';
	//}
	//else
	//{
	//	$icq_status_img = '';
	//	$icq_img = '';
	//	$icq = '';
	//}

	//$aim = ( $row['user_aim'] ) ? $row['user_aim'] : '';

	//$temp_url = append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['user_id']);
	//$msn = ( $row['user_msnm'] ) ? '<a href="' . $temp_url . '">' . $lang['MSNM'] . '</a>' : '';

	//$yim = ( $row['user_yim'] ) ? $row['user_yim'] : '';

	//$temp_url = append_sid('search.php?search_author=' . urlencode($row['username']) . '&amp;showresults=posts');
	//$search = '<a href="' . $temp_url . '">' . sprintf($lang['Search_user_posts'], $row['username']) . '</a>';

	return;
}

$userdata = $session->start($user_ip, PAGE_GROUPCP);
init_userprefs($userdata);

$script_name 		= preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
$script_name 		= ( $script_name != '' ) ? $script_name . '/groupcp.php' : 'groupcp.php';
$server_name 		= trim($board_config['server_name']);
$server_protocol 	= ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port 		= ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';
$server_url 		= $server_protocol . $server_name . $server_port . $script_name;

if ( isset($_GET[POST_GROUPS_URL]) || isset($_POST[POST_GROUPS_URL]) )
{
	$group_id = ( isset($_POST[POST_GROUPS_URL]) ) ? intval($_POST[POST_GROUPS_URL]) : intval($_GET[POST_GROUPS_URL]);
}
else
{
	$group_id = '';
}

if ( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

$confirm = ( isset($_POST['confirm']) ) ? TRUE : 0;
$cancel = ( isset($_POST['cancel']) ) ? TRUE : 0;
$sid = ( isset($_POST['sid']) ) ? $_POST['sid'] : '';
if ( isset($_POST['start1']) )
{
	$start1 = abs(intval($_POST['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['posts_per_page']);
}
else
{
	$start = ( isset($_GET['start']) ) ? intval($_GET['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}
$start_gb = $start;
if ( isset($_GET['gb']) )
{
	$start = 0;
}
else
{
	$start_gb = 0;
}

$is_moderator = FALSE;

if ( isset($_POST['groupstatus']) && $group_id )
{
	if ( !$userdata['session_logged_in'] )
	{
		login_back("groupcp.php?" . POST_GROUPS_URL . "=$group_id");
	}

	$sql = "SELECT group_moderator 
		FROM " . GROUPS_TABLE . "  
		WHERE group_id = $group_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain user and group information', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);

	if ( $row['group_moderator'] != $userdata['user_id'] && $userdata['user_level'] != ADMIN )
	{
		$message = '您不是该团队的管理员，无法执行团队管理功能！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组页面！<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

		trigger_error($message);
	}

	$sql = 'UPDATE ' . GROUPS_TABLE . ' 
		SET group_type = ' . intval($_POST['group_type']) . '
		WHERE group_id = ' . $group_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain user and group information', E_USER_WARNING);
	}

	$message = '已经成功更新小组状态！<br /><br /><br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组页面！<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

	trigger_error($message);

}
else if ( isset($_POST['joingroup']) && $group_id )
{
	if ( !$userdata['session_logged_in'] )
	{
		login_back('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id);
	}
	else if ( $sid !== $userdata['session_id'] )
	{
		trigger_error('错误！请重新加载页面！', E_USER_ERROR);
	}

	$sql = 'SELECT ug.user_id, g.group_type
		FROM ' . USER_GROUP_TABLE . ' ug, ' . GROUPS_TABLE . " g 
		WHERE g.group_id = $group_id 
			AND g.group_type <> " . GROUP_HIDDEN . ' 
			AND ug.group_id = g.group_id';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain user and group information', E_USER_WARNING);
	}

	if ( $row = $db->sql_fetchrow($result))
	{
		if ( $row['group_type'] == GROUP_OPEN )
		{
			do
			{
				if ( $userdata['user_id'] == $row['user_id'] )
				{
					$message = '您已经是该小组的成员！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a>返回小组页面！<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';	
					trigger_error($message);
				}
			} while ( $row = $db->sql_fetchrow($result) );
		}
		else
		{
			$message = '这个小组已经关闭！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a>返回小组页面！<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';	
			trigger_error($message);
		}
	}
	else
	{
		trigger_error('不存在的小组！', E_USER_ERROR); 
	}

	$sql = 'INSERT INTO ' . USER_GROUP_TABLE . " (group_id, user_id, user_pending) 
		VALUES ($group_id, " . $userdata['user_id'] . ', 1)';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error inserting user group subscription', E_USER_WARNING);
	}

	$sql = 'SELECT u.user_email, u.username, g.group_name 
		FROM ' . USERS_TABLE . ' u, ' . GROUPS_TABLE . ' g 
		WHERE u.user_id = g.group_moderator 
			AND g.group_id = ' .  $group_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting group moderator data', E_USER_WARNING);
	}

	$moderator = $db->sql_fetchrow($result);

	include(ROOT_PATH . 'includes/class/emailer.php');
	$emailer = new emailer();
	$emailer->from($board_config['board_email']);
	$emailer->replyto($board_config['board_email']);
	$emailer->use_template('group_request');
	$emailer->email_address($moderator['user_email']);
	$emailer->set_subject('小组申请要求');

	$emailer->assign_vars(array(
		'SITENAME' 			=> $board_config['sitename'], 
		'GROUP_MODERATOR' 	=> $moderator['username'],
		'EMAIL_SIG' 		=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 
		'U_GROUPCP' 		=> $server_url . '?' . POST_GROUPS_URL . '=' . $group_id . '&amp;validate=true')
	);
	$emailer->send();
	$emailer->reset();

	$message = '您的申请已经提交，接下来您需要等待该小组的管理员批准，通过审核时您会收到邮件通知。<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组首页<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

	trigger_error($message);
}
else if ( (isset($_POST['unsub']) || isset($_POST['unsubpending']) && $group_id) )
{
	if ( $cancel )
	{
		redirect(append_sid('groupcp.php', true));
	}
	else if ( !$userdata['session_logged_in'] )
	{
		login_back('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id);
	}
	else if ( $sid !== $userdata['session_id'] )
	{
		trigger_error('错误!请刷新页面', E_USER_ERROR);
	}


	if ( $confirm )
	{
		$sql = 'DELETE FROM ' . USER_GROUP_TABLE . ' 
			WHERE user_id = ' . $userdata['user_id'] . ' 
				AND group_id = ' . $group_id;
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not delete group memebership data', E_USER_WARNING);
		}

		if ( $userdata['user_level'] != ADMIN && $userdata['user_level'] == MOD )
		{
			$sql = 'SELECT COUNT(auth_mod) AS is_auth_mod 
				FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . ' ug 
				WHERE ug.user_id = ' . $userdata['user_id'] . ' 
					AND aa.group_id = ug.group_id 
					AND aa.auth_mod = 1';
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain moderator status', E_USER_WARNING);
			}

			if ( !($row = $db->sql_fetchrow($result)) || $row['is_auth_mod'] == 0 )
			{
				$sql = 'UPDATE ' . USERS_TABLE . ' 
					SET user_level = ' . USER . ' 
					WHERE user_id = ' . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not update user level', E_USER_WARNING);
				}
			}
		}

		$message = '您已经成功退出该小组！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组首页<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

		trigger_error($message);
	}
	else
	{
		$unsub_msg = ( isset($_POST['unsub']) ) ? '您是否要退出这个小组？' : '你是否要加入该小组？';

		$s_hidden_fields = '<input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" /><input type="hidden" name="unsub" value="1" />';
		$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

		$page_title = '确认';
		page_header($page_title);

		$template->set_filenames(array(
			'confirm' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '确认',
			'MESSAGE_TEXT' 		=> $unsub_msg,
			'L_YES' 			=> '是',
			'L_NO' 				=> '否',
			'S_CONFIRM_ACTION' 	=> append_sid('groupcp.php'),
			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
		);

		$template->pparse('confirm');

		page_footer();
	}

}
else if ( $group_id )
{
	if ( isset($_GET['validate']) )
	{
		if ( !$userdata['session_logged_in'] )
		{
			login_back('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id);
		}
	}
	
	$sql = 'SELECT g.group_moderator, g.group_type, aa.auth_mod 
		FROM ( ' . GROUPS_TABLE . ' g 
		LEFT JOIN ' . AUTH_ACCESS_TABLE . " aa ON aa.group_id = g.group_id )
		WHERE g.group_id = $group_id
		ORDER BY aa.auth_mod DESC";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not get moderator information', E_USER_WARNING);
	}

	if ( $group_info = $db->sql_fetchrow($result) )
	{
		$group_moderator = $group_info['group_moderator'];
	
		if ( $group_moderator == $userdata['user_id'] || $userdata['user_level'] == ADMIN )
		{
			$is_moderator = TRUE;
		}

		if ( !empty($_POST['add']) || !empty($_POST['remove']) || isset($_POST['approve']) || isset($_POST['deny']) )
		{
			if ( !$userdata['session_logged_in'] )
			{
				login_back('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id);
			} 
			else if ( $sid !== $userdata['session_id'] )
			{
				trigger_error('错误！请重新加载页面！', E_USER_ERROR);
			}

			if ( !$is_moderator )
			{

				$message = '您不是该团队的管理员，无法执行团队管理功能！<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

				trigger_error($message);
			}

			if ( isset($_POST['add']) )
			{
				$username = ( isset($_POST['username']) ) ? phpbb_clean_username($_POST['username']) : '';
				
				$sql = 'SELECT user_id, user_email, user_level  
					FROM ' . USERS_TABLE . " 
					WHERE username = '" . str_replace("\'", "''", $username) . "'";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not get user information', E_USER_WARNING);
				}

				if ( !($row = $db->sql_fetchrow($result)) )
				{

					$message = '无法添加这个用户！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组页面<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

					trigger_error($message);
				}

				if ( $row['user_id'] == ANONYMOUS )
				{
				
					$message = '无法添加匿名用户！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组页面<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';

					trigger_error($message);
				}
				
				$sql = 'SELECT ug.user_id, u.user_level 
					FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u 
					WHERE u.user_id = ' . $row['user_id'] . ' 
						AND ug.user_id = u.user_id 
						AND ug.group_id = ' . $group_id;
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not get user information', E_USER_WARNING);
				}

				if ( !($db->sql_fetchrow($result)) )
				{
					$sql = 'INSERT INTO ' . USER_GROUP_TABLE . " (user_id, group_id, user_pending) 
						VALUES (" . $row['user_id'] .", $group_id, 0)";
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not add user to group', E_USER_WARNING);
					}
					
					if ( $row['user_level'] != ADMIN && $row['user_level'] != MOD && $group_info['auth_mod'] )
					{
						$sql = 'UPDATE ' . USERS_TABLE . ' 
							SET user_level = ' . MOD . ' 
							WHERE user_id = ' . $row['user_id'];
						if ( !$db->sql_query($sql) )
						{
							trigger_error('Could not update user level', E_USER_WARNING);
						}
					}

					$group_sql = "SELECT group_name 
						FROM " . GROUPS_TABLE . " 
						WHERE group_id = $group_id";
					if ( !($result = $db->sql_query($group_sql)) )
					{
						trigger_error('Could not get group information', E_USER_WARNING);
					}

					$group_name_row = $db->sql_fetchrow($result);

					$group_name = $group_name_row['group_name'];

					include(ROOT_PATH . 'includes/class/emailer.php');
					$emailer = new emailer();

					$emailer->from($board_config['board_email']);
					$emailer->replyto($board_config['board_email']);

					$emailer->use_template('group_added');
					$emailer->email_address($row['user_email']);
					$emailer->set_subject('您已经加入该小组');

					$emailer->assign_vars(array(
						'SITENAME' 		=> $board_config['sitename'], 
						'GROUP_NAME' 	=> $group_name,
						'EMAIL_SIG' 	=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

						'U_GROUPCP' 	=> $server_url . '?' . POST_GROUPS_URL . '=' . $group_id)
					);
					$emailer->send();
					$emailer->reset();
				}
				else
				{
					$message = '用户已经是该小组的成员！<br /><br />点击 <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id) . '">这里</a> 返回小组页面<br /><br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回首页';
					trigger_error($message);
				}
			}
			else 
			{
				if ( ( ( isset($_POST['approve']) || isset($_POST['deny']) ) && isset($_POST['pending_members']) ) || ( isset($_POST['remove']) && isset($_POST['members']) ) )
				{

					$members = ( isset($_POST['approve']) || isset($_POST['deny']) ) ? $_POST['pending_members'] : $_POST['members'];

					$sql_in = '';
					for($i = 0; $i < count($members); $i++)
					{
						$sql_in .= ( ( $sql_in != '' ) ? ', ' : '' ) . intval($members[$i]);
					}

					if ( isset($_POST['approve']) )
					{
						if ( $group_info['auth_mod'] )
						{
							$sql = 'UPDATE ' . USERS_TABLE . ' 
								SET user_level = ' . MOD . " 
								WHERE user_id IN ($sql_in) 
									AND user_level NOT IN (" . MOD . ', ' . ADMIN . ')';
							if ( !$db->sql_query($sql) )
							{
								trigger_error('Could not update user level', E_USER_WARNING);
							}
						}

						$sql = 'UPDATE ' . USER_GROUP_TABLE . " 
							SET user_pending = 0 
							WHERE user_id IN ($sql_in) 
								AND group_id = $group_id";
						$sql_select = 'SELECT user_email 
							FROM ' . USERS_TABLE . " 
							WHERE user_id IN ($sql_in)"; 
					}
					else if ( isset($_POST['deny']) || isset($_POST['remove']) )
					{
						if ( $group_info['auth_mod'] )
						{
							$sql = 'SELECT ug.user_id, ug.group_id 
								FROM ' . AUTH_ACCESS_TABLE . ' aa, ' . USER_GROUP_TABLE . " ug 
								WHERE ug.user_id IN  ($sql_in) 
									AND aa.group_id = ug.group_id 
									AND aa.auth_mod = 1 
								GROUP BY ug.user_id, ug.group_id 
								ORDER BY ug.user_id, ug.group_id";
							if ( !($result = $db->sql_query($sql)) )
							{
								trigger_error('Could not obtain moderator status', E_USER_WARNING);
							}

							if ( $row = $db->sql_fetchrow($result) )
							{
								$group_check = array();
								$remove_mod_sql = '';

								do
								{
									$group_check[$row['user_id']][] = $row['group_id'];
								}
								while ( $row = $db->sql_fetchrow($result) );

								foreach($group_check as $user_id => $group_list)
								{
									if ( count($group_list) == 1 )
									{
										$remove_mod_sql .= ( ( $remove_mod_sql != '' ) ? ', ' : '' ) . $user_id;
									}
								}

								if ( $remove_mod_sql != '' )
								{
									$sql = 'UPDATE ' . USERS_TABLE . ' 
										SET user_level = ' . USER . " 
										WHERE user_id IN ($remove_mod_sql) 
											AND user_level NOT IN (" . ADMIN . ')';
									if ( !$db->sql_query($sql) )
									{
										trigger_error('Could not update user level', E_USER_WARNING);
									}
								}
							}
						}

						$sql = 'DELETE FROM ' . USER_GROUP_TABLE . " 
							WHERE user_id IN ($sql_in) 
								AND group_id = $group_id";
					}

					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update user group table', E_USER_WARNING);
					}

					if ( isset($_POST['approve']) )
					{
						if ( !($result = $db->sql_query($sql_select)) )
						{
							trigger_error('Could not get user email information', E_USER_WARNING);
						}

						$bcc_list = array();
						while ($row = $db->sql_fetchrow($result))
						{
							$bcc_list[] = $row['user_email'];
						}

						$group_sql = 'SELECT group_name 
							FROM ' . GROUPS_TABLE . " 
							WHERE group_id = $group_id";
						if ( !($result = $db->sql_query($group_sql)) )
						{
							trigger_error('Could not get group information', E_USER_WARNING);
						}

						$group_name_row = $db->sql_fetchrow($result);
						$group_name = $group_name_row['group_name'];

						include(ROOT_PATH . 'includes/class/emailer.php');
						$emailer = new emailer();

						$emailer->from($board_config['board_email']);
						$emailer->replyto($board_config['board_email']);

						for ($i = 0; $i < count($bcc_list); $i++)
						{
							$emailer->bcc($bcc_list[$i]);
						}

						$emailer->use_template('group_approved');
						$emailer->set_subject('您申请加入小组的审核已通过');

						$emailer->assign_vars(array(
							'SITENAME' => $board_config['sitename'], 
							'GROUP_NAME' => $group_name,
							'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

							'U_GROUPCP' => $server_url . '?' . POST_GROUPS_URL . "=$group_id")
						);
						$emailer->send();
						$emailer->reset();
					}
				}
			}
		}
	}
	else
	{
		trigger_error('目前还没有创建任何小组！', E_USER_ERROR);
	}

	$sql = 'SELECT *
		FROM ' . GROUPS_TABLE . "
		WHERE group_id = $group_id
			AND group_single_user = 0";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting group information', E_USER_WARNING);
	}

	if ( !($group_info = $db->sql_fetchrow($result)) )
	{
		trigger_error('小组不存在！', E_USER_ERROR);
	}

	$sql = 'SELECT username, user_id, user_viewemail, user_posts, user_regdate, user_from, user_website, user_email, user_qq, user_aim, user_yim, user_msnm, user_avatar_type, user_allowavatar 
		FROM ' . USERS_TABLE . ' 
		WHERE user_id = ' . $group_info['group_moderator'];

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting user list for group', E_USER_WARNING);
	}

	if ( !$group_moderator = $db->sql_fetchrow($result))
	{
		trigger_error('小组的创始人ID[' . $group_info['group_moderator'] . ']不存在或已被管理员删除', E_USER_ERROR);
	}

	$sql = 'SELECT u.username, u.user_id, u.user_viewemail, u.user_posts, u.user_regdate, u.user_from, u.user_website, u.user_email, u.user_qq, u.user_aim, u.user_yim, u.user_msnm, ug.user_pending 
		FROM ' . USERS_TABLE . ' u, ' . USER_GROUP_TABLE . " ug
		WHERE ug.group_id = $group_id
			AND u.user_id = ug.user_id
			AND ug.user_pending = 0 
			AND ug.user_id <> " . $group_moderator['user_id'] . " 
		ORDER BY u.username ASC"; 

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting user list for group', E_USER_WARNING);
	}

	$group_members = $db->sql_fetchrowset($result); 
	$members_count = count($group_members);
	$db->sql_freeresult($result);

	$sql = 'SELECT u.username, u.user_id, u.user_viewemail, u.user_posts, u.user_regdate, u.user_from, u.user_website, u.user_email, u.user_qq, u.user_aim, u.user_yim, u.user_msnm
		FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . " u
		WHERE ug.group_id = $group_id
			AND g.group_id = ug.group_id
			AND ug.user_pending = 1
			AND u.user_id = ug.user_id
		ORDER BY u.username"; 
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting user pending information', E_USER_WARNING);
	}

	$modgroup_pending_list = $db->sql_fetchrowset($result);
	$modgroup_pending_count = count($modgroup_pending_list);
	$db->sql_freeresult($result);

	$is_group_member = 0;
	if ( $members_count )
	{
		for($i = 0; $i < $members_count; $i++)
		{
			if ( $group_members[$i]['user_id'] == $userdata['user_id'] && $userdata['session_logged_in'] )
			{
				$is_group_member = TRUE; 
			}
		}
	}

	$is_group_pending_member = 0;
	if ( $modgroup_pending_count )
	{
		for($i = 0; $i < $modgroup_pending_count; $i++)
		{
			if ( $modgroup_pending_list[$i]['user_id'] == $userdata['user_id'] && $userdata['session_logged_in'] )
			{
				$is_group_pending_member = TRUE;
			}
		}
	}

	if ( $userdata['user_level'] == ADMIN )
	{
		$is_moderator = TRUE;
	}

	if ( $userdata['user_id'] == $group_info['group_moderator'] )
	{
		$is_moderator = TRUE;

		$group_details =  '您是这个小组的管理员';

		$s_hidden_fields = '<input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';
	}
	else if ( $is_group_member || $is_group_pending_member )
	{
		$template->assign_block_vars('switch_unsubscribe_group_input', array());

		$group_details =  ( $is_group_pending_member ) ? '您申请加入这个小组的请求正在审核中' : '您是这个小组的成员';

		$s_hidden_fields = '<input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';
	}
	else if ( $userdata['user_id'] == ANONYMOUS )
	{
		$group_details =  '请登录网站后再申请加入小组';
		$s_hidden_fields = '';
	}
	else
	{
		if ( $group_info['group_type'] == GROUP_OPEN )
		{
			$template->assign_block_vars('switch_subscribe_group_input', array());

			$group_details =  '这是一个开放的小组，可以点击申请成员';
			$s_hidden_fields = '<input type="hidden" name="' . POST_GROUPS_URL . '" value="' . $group_id . '" />';
		}
		else if ( $group_info['group_type'] == GROUP_CLOSED )
		{
			$group_details =  '这是一个封闭的小组，不接受新的成员';
			$s_hidden_fields = '';
		}
		else if ( $group_info['group_type'] == GROUP_HIDDEN )
		{
			$group_details =  '这是一个隐藏的小组, 不容许会员申请和增加成员';
			$s_hidden_fields = '';
		}
	}

	if($userdata['user_id'] == $group_info['group_moderator'] || $userdata['user_level'] == ADMIN) 
	{
		$error = FALSE;
		$error_msg = '';
		include(ROOT_PATH . 'includes/functions/group_logo.php');

		$avatar_sql = '';
		$sendd = append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id);

		if ( isset($_POST['avatardel']) )
		{
			$avatar_sql = "group_logo = ''";
		}
		if ( isset($_POST['groupicon']) )
		{
			if(strpos($user_agent, "Opera Mini") && !strpos($user_agent, "Opera Mini/3") && !strpos($user_agent, "Opera Mini/4") && !strpos($user_agent, "Opera Mini/5"))
			{ 
				$result_ua = 1; 
			}
	
			if ( $result_ua )
			{
				$opera_mini = './opera_mini';
				$uploadedfile = $_POST['fileupload'];

				if ( strlen($uploadedfile) ) 
				{
					$array = explode('file=', $uploadedfile);
					$tmp_name = $array[0];
					$filebase64 = $array[1]; 
				}

				$tmp_name = basename($tmp_name);

				if ( strlen($filebase64) ) 
				{
					$filedata = base64_decode($filebase64);
				}

				$fileom = @fopen($opera_mini . "/" . $tmp_name, "wb");

				if ( $fileom ) 
				{
					if ( flock($fileom, LOCK_EX) ) 
					{
						fwrite($fileom, $filedata);
						flock($fileom, LOCK_UN); 
					}
					fclose($fileom); 
				}

				$file = $opera_mini . "/" . $tmp_name;
				$size = @filesize($file);
				$tmp_name_type = strrchr($tmp_name, '.');
				$repl=array("."=>"");
				$type = strtr($tmp_name_type, $repl);
				$user_logo_upload = ( !empty($_POST['avatarurl']) ) ? trim($_POST['avatarurl']) : ( ( $file != $opera_mini . "/") ? $file : '' );
				$user_logo_name = ( !empty($tmp_name) ) ? $tmp_name : '';
				$user_logo_size = ( !empty($size) ) ? $size : 0;
				$user_logo_filetype = ( !empty($type) ) ? 'image/'.$type : '';
			} 
			else 
			{
				$user_logo_upload = ( !empty($_POST['avatarurl']) ) ? trim($_POST['avatarurl']) : ( ( $_FILES['avatar']['tmp_name'] != "none") ? $_FILES['avatar']['tmp_name'] : '' );
				$user_logo_name = ( !empty($_FILES['avatar']['name']) ) ? $_FILES['avatar']['name'] : '';
				$user_logo_size = ( !empty($_FILES['avatar']['size']) ) ? $_FILES['avatar']['size'] : 0;
				$user_logo_filetype = ( !empty($_FILES['avatar']['type']) ) ? $_FILES['avatar']['type'] : '';
			}

			if ( ( !empty($user_logo_upload) || !empty($user_logo_name) ))
			{
				if ( !empty($user_logo_upload) )
				{
					$avatar_mode = (empty($user_logo_name)) ? 'remote' : 'local';
					$avatar_sql = user_logo_upload('', $avatar_mode, $group_info['group_logo'], 1, $error, $error_msg, $user_logo_upload, $user_logo_name, $user_logo_size, $user_logo_filetype);
				}
				else if ( !empty($user_logo_name) )
				{
					$l_avatar_size = '头像文件不大于 ' . round($board_config['avatar_filesize'] / 1024) . ' KB';

					$error = true;
					$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $l_avatar_size;
				}
			}
			$logo_filename  = 'images/group_logo/' . $group_info['group_logo'];
		}
		if( $avatar_sql != null )
		{
			@unlink($logo_filename);
			$sql = "UPDATE " . GROUPS_TABLE . "
				SET " . $avatar_sql . "
				WHERE group_id = $group_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update users table', E_USER_WARNING);
			}
			header("Location: " . $sendd);
		}

		$ini_val = ( phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
		$form_enctype = ( @$ini_val('file_uploads') == '0' || strtolower(@$ini_val('file_uploads') == 'off') || phpversion() == '4.0.4pl1' || !$board_config['allow_avatar_upload'] || ( phpversion() < '4.0.3' && @$ini_val('open_basedir') != '' ) ) ? '' : 'enctype="multipart/form-data"';
		$template->assign_block_vars('yeah', array());
		if ($form_enctype != '' )
		{
			$template->assign_block_vars('yeah.switch_avatar_local_upload', array() );
		}
		elseif ($form_enctype != '' )
		{
			$template->assign_block_vars('yeah.switch_avatar_local_upload_om', array() );
		}
				
		if ( $error )
		{
			error_box('ERROR_BOX', $error_msg);
		}
		$delete_image = '<input type="submit" name="avatardel" value="删除" />';
	}

	$page_title = '小组';
	page_header($page_title);

	$template->set_filenames(array(
		'info' 			=> 'groupcp/info_body.tpl', 
		'pendinginfo' 	=> 'groupcp/pending_info.tpl')
	);

	$username = $group_moderator['username'];
	$user_id = $group_moderator['user_id'];


	generate_user_info($group_moderator, $board_config['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $qq_status_img, $qq_img, $qq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

	$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

	$template->assign_vars(array(
		'L_AVATAR_EXPLAIN' 			=> sprintf('最高限制 (像素%s x %s 和 %s KB)', $board_config['avatar_max_width'], $board_config['avatar_max_height'], (round($board_config['avatar_filesize'] / 1024))),
		'S_FORM_ENCTYPE' 			=> $form_enctype,
		'CURRENT_LOGO' 				=> ( $group_info['group_logo'] != null ) ? '<br/><img src="images/group_logo/'.$group_info['group_logo'].'" alt="" />' . $delete_image : '',
		'GROUP_NAME' 				=> $group_info['group_name'],
		'GROUP_DESC' 				=> str_replace(PHP_EOL, '<br />', $group_info['group_description']),
		'GROUP_DETAILS' 			=> $group_details,
		'MOD_USERNAME' 				=> $username,

		'U_MOD_VIEWPROFILE' 		=> append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id), 
		'U_SEARCH_USER' 			=> append_sid('search.php?mode=searchuser'), 

		'S_GROUP_OPEN_TYPE' 		=> GROUP_OPEN,
		'S_GROUP_CLOSED_TYPE' 		=> GROUP_CLOSED,
		'S_GROUP_HIDDEN_TYPE' 		=> GROUP_HIDDEN,
		'S_GROUP_OPEN_CHECKED' 		=> ( $group_info['group_type'] == GROUP_OPEN ) ? ' checked="checked"' : '',
		'S_GROUP_CLOSED_CHECKED' 	=> ( $group_info['group_type'] == GROUP_CLOSED ) ? ' checked="checked"' : '',
		'S_GROUP_HIDDEN_CHECKED' 	=> ( $group_info['group_type'] == GROUP_HIDDEN ) ? ' checked="checked"' : '',
		'S_HIDDEN_FIELDS'		 	=> $s_hidden_fields, 
		'S_GROUPCP_ACTION' 			=> append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id))
	);

	for($i = $start; $i < min($board_config['topics_per_page'] + $start, $members_count); $i++)
	{
		$username = $group_members[$i]['username'];
		$user_id = $group_members[$i]['user_id'];

		generate_user_info($group_members[$i], $board_config['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $qq_status_img, $qq_img, $qq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

		if ( $group_info['group_type'] != GROUP_HIDDEN || $is_group_member || $is_moderator )
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

			$template->assign_block_vars('member_row', array(
				'ROW_CLASS' => $row_class,
				'USERNAME' => $username,
				'FROM' => $from,
				'JOINED' => $joined,
				'POSTS' => $posts,
				'USER_ID' => $user_id, 
				'AVATAR_IMG' => $poster_avatar,
				'PROFILE_IMG' => $profile_img, 
				'PROFILE' => $profile, 
				'SEARCH_IMG' => $search_img,
				'SEARCH' => $search,
				'PM_IMG' => $pm_img,
				'PM' => $pm,
				'EMAIL_IMG' => $email_img,
				'EMAIL' => $email,
				'WWW_IMG' => $www_img,
				'WWW' => $www,
				'QQ_STATUS_IMG' => $qq_status_img,
				'QQ_IMG' => $qq_img, 
				'QQ' => $qq, 
				'AIM_IMG' => $aim_img,
				'AIM' => $aim,
				'MSN_IMG' => $msn_img,
				'MSN' => $msn,
				'YIM_IMG' => $yim_img,
				'YIM' => $yim,
				
				'U_VIEWPROFILE' => append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id))
			);

			if ( $is_moderator )
			{
				$template->assign_block_vars('member_row.switch_mod_option', array());
			}
		}
	}

	if ( !$members_count )
	{
		$template->assign_block_vars('switch_no_members', array());
	}

	$current_page = ( !$members_count ) ? 1 : ceil( $members_count / $board_config['topics_per_page'] );

	if ( $members_count > $board_config['topics_per_page'] )
	{
		$template->assign_vars(array(
			'PAGINATION' => generate_pagination('groupcp.php?' . POST_GROUPS_URL . '=' . $group_id, $members_count, $board_config['topics_per_page'], $start))
		);
	}

	if ( $group_info['group_type'] == GROUP_HIDDEN && !$is_group_member && !$is_moderator )
	{
		$template->assign_block_vars('switch_hidden_group', array());
	}

	if ( $is_moderator )
	{
		if ( $modgroup_pending_count )
		{
			for($i = 0; $i < $modgroup_pending_count; $i++)
			{
				$username = $modgroup_pending_list[$i]['username'];
				$user_id = $modgroup_pending_list[$i]['user_id'];

				generate_user_info($modgroup_pending_list[$i], $board_config['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $qq_status_img, $qq_img, $qq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$user_select = '<input type="checkbox" name="member[]" value="' . $user_id . '">';

				$template->assign_block_vars('pending_members_row', array(
					'ROW_CLASS' => $row_class,
					'USERNAME' => $username,
					//'FROM' => $from,
					//'JOINED' => $joined,
					'POSTS' => $posts,
					'USER_ID' => $user_id, 
					//'AVATAR_IMG' => $poster_avatar,
					//'PROFILE_IMG' => $profile_img, 
					'PROFILE' => $profile, 
					//'SEARCH_IMG' => $search_img,
					//'SEARCH' => $search,
					//'PM_IMG' => $pm_img,
					'PM' => $pm,
					//'EMAIL_IMG' => $email_img,
					'EMAIL' => $email,
					//'WWW_IMG' => $www_img,
					//'WWW' => $www,
					//'ICQ_STATUS_IMG' => $icq_status_img,
					//'ICQ_IMG' => $icq_img, 
					//'ICQ' => $icq, 
					//'AIM_IMG' => $aim_img,
					//'AIM' => $aim,
					//'MSN_IMG' => $msn_img,
					//'MSN' => $msn,
					//'YIM_IMG' => $yim_img,
					//'YIM' => $yim,
					
					'U_VIEWPROFILE' => append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id))
				);
			}

			$template->assign_block_vars('switch_pending_members', array() );

			$template->assign_var_from_handle('PENDING_USER_BOX', 'pendinginfo');
		
		}
	}

	if ( $is_moderator )
	{
		$template->assign_block_vars('switch_mod_option', array());
		if ( $members_count )
		{
			$template->assign_block_vars('switch_mod_option.switch_no_members', array());
		}
		$template->assign_block_vars('switch_add_member', array());
	}

	$template->pparse('info');
}
else
{
	$in_group = array();
	
	if ( $userdata['session_logged_in'] ) 
	{
		$sql = 'SELECT g.group_id, g.group_name, g.group_type, ug.user_pending 
			FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
			WHERE ug.user_id = ' . $userdata['user_id'] . '  
				AND ug.group_id = g.group_id
				AND g.group_single_user <> ' . TRUE . '
			ORDER BY g.group_name, ug.user_id';
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Error getting group information', E_USER_WARNING);
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
			$in_group = array();
			$s_member_groups_opt = '';
			$s_pending_groups_opt = '';

			do
			{
				$in_group[] = $row['group_id'];
				if ( $row['user_pending'] )
				{
					$s_pending_groups_opt 	.= '- <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $row['group_id'] . '&amp;sid=' . $userdata['session_id']) . '">' . $row['group_name'] . '</a><br/>';
				}
				else
				{
					$s_member_groups_opt 	.= '- <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $row['group_id'] . '&amp;sid=' . $userdata['session_id']) . '">' . $row['group_name'] . '</a><br/>';
				}
			}
			while( $row = $db->sql_fetchrow($result) );

			$s_pending_groups = $s_pending_groups_opt;
			$s_member_groups = $s_member_groups_opt;
		}
	}

	$ignore_group_sql =	( count($in_group) ) ? 'AND group_id NOT IN (' . implode(', ', $in_group) . ')' : ''; 
	$sql = 'SELECT group_id, group_name, group_type 
		FROM ' . GROUPS_TABLE . ' g 
		WHERE group_single_user <> ' . TRUE . " 
			$ignore_group_sql 
		ORDER BY g.group_name";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting group information', E_USER_WARNING);
	}

	$s_group_list_opt = '';
	while( $row = $db->sql_fetchrow($result) )
	{
		if  ( $row['group_type'] != GROUP_HIDDEN || $userdata['user_level'] == ADMIN )
		{
			$s_group_list_opt .= '- <a href="' . append_sid('groupcp.php?' . POST_GROUPS_URL . '=' . $row['group_id'] . '&amp;sid=' . $userdata['session_id']) . '">' . $row['group_name'] . '</a><br/>';
		}
	}
	$s_group_list = $s_group_list_opt;

	if ( $s_group_list_opt != '' || $s_pending_groups_opt != '' || $s_member_groups_opt != '' )
	{
		$page_title = '小组';
		page_header($page_title);

		$template->set_filenames(array(
			'user' => 'groupcp/user_body.tpl')
		);

		if ( $s_pending_groups_opt != '' || $s_member_groups_opt != '' )
		{
			$template->assign_block_vars('switch_groups_joined', array() );
		}

		if ( $s_member_groups_opt != '' )
		{
			$template->assign_block_vars('switch_groups_joined.switch_groups_member', array() );
		}

		if ( $s_pending_groups_opt != '' )
		{
			$template->assign_block_vars('switch_groups_joined.switch_groups_pending', array() );
		}

		if ( $s_group_list_opt != '' )
		{
			$template->assign_block_vars('switch_groups_remaining', array() );
		}

		$s_hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

		$template->assign_vars(array(
			'S_USERGROUP_ACTION' 			=> append_sid('groupcp.php'), 
			'S_HIDDEN_FIELDS' 				=> $s_hidden_fields, 
			'GROUP_LIST_SELECT' 			=> $s_group_list,
			'GROUP_PENDING_SELECT' 			=> $s_pending_groups,
			'GROUP_MEMBER_SELECT' 			=> $s_member_groups)
		);

		$template->pparse('user');
	}
	else
	{
		trigger_error('目前还没有创建任何小组！', E_USER_ERROR);
	}
}
page_footer();
?>