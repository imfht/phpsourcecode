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

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['会员']['管理'] = $filename;

	return;
}
define('IN_PHPBB', true);
define('ROOT_PATH', './../');

require('./pagestart.php');
require_once(ROOT_PATH . 'includes/functions/bbcode.php');
require(ROOT_PATH . 'includes/functions/post.php');
require(ROOT_PATH . 'includes/functions/selects.php');
require(ROOT_PATH . 'includes/attach/functions_selects.php');
require(ROOT_PATH . 'includes/attach/functions_admin.php');
require(ROOT_PATH . 'includes/functions/validate.php');

$html_entities_match 	= array('#<#', '#>#');
$html_entities_replace 	= array('&lt;', '&gt;');
$lang = array(
	'datetime' => array(
		'Sunday' => '星期日','Monday' => '星期一','Tuesday' => '星期二','Wednesday' => '星期三','Thursday' => '星期四','Friday' => '星期五','Saturday' => '星期六',

		'Sun' => '日','Mon' => '一', 'Tue' => '二','Wed' => '三', 'Thu' => '四','Fri' => '五', 'Sat' => '六',
		
		'January' => '1', 'February' => '2', 'March' => '3', 'April' => '4', 'Mays' => '5', 'June' => '6', 'July' => '7', 'August' => '8', 'September' => '9', 'October' => '10', 'November' => '11', 'December' => '12',

		'Jan' => '1', 'Feb' => '2', 'Mar' => '3', 'Apr' => '4', 'May' => '5', 'Jun' => '6', 'Jul' => '7', 'Aug' => '8', 'Sep' => '9', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12',
	)
);
if( isset( $_POST['mode'] ) || isset( $_GET['mode'] ) )
{
	$mode = ( isset( $_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

if ( $mode == 'edit' || $mode == 'save' && ( isset($_POST['username']) || isset($_GET[POST_USERS_URL]) || isset( $_POST[POST_USERS_URL]) ) )
{

	//attachment_quota_settings('user', $_POST['submit'], $mode);
	$admin_mode = 'user';
	if (!intval($board_config['allow_ftp_upload']))
	{
		if ($board_config['upload_dir'][0] == '/' || ($board_config['upload_dir'][0] != '/' && $board_config['upload_dir'][1] == ':'))
		{
			$upload_dir = $board_config['upload_dir'];
		}
		else
		{
			$upload_dir = ROOT_PATH . $board_config['upload_dir'];
		}
	}
	else
	{
		$upload_dir = $board_config['download_path'];
	}

	$user_id = 0;

	$submit = (isset($_POST['submit'])) ? true : false;

	if (!$submit && $mode != 'save')
	{
		$user_id 	= get_var(POST_USERS_URL, 0);
		$u_name 	= get_var('username', '');

		if (!$user_id && !$u_name)
		{
			trigger_error('对不起，用户不存在', E_USER_ERROR);
		}

		if ($user_id)
		{
			$this_userdata['user_id'] = $user_id;
		}
		else
		{
			$this_userdata = get_userdata($_POST['username'], true);
		}

		$user_id = (int) $this_userdata['user_id'];
	}
	else
	{
		$user_id = get_var('id', 0);
		
		if (!$user_id)
		{
			trigger_error('对不起，用户不存在', E_USER_ERROR);
		}
	}
	
	if ($mode != 'save')
	{
		$sql = 'SELECT quota_limit_id, quota_type FROM ' . QUOTA_TABLE . ' 
			WHERE user_id = ' . (int) $user_id;

		if( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Unable to get Quota Settings', E_USER_WARNING);
		}

		$pm_quota = $upload_quota = 0;
		
		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if ($row['quota_type'] == QUOTA_UPLOAD_LIMIT)
				{
					$upload_quota = $row['quota_limit_id'];
				}
				else if ($row['quota_type'] == QUOTA_PM_LIMIT)
				{
					$pm_quota = $row['quota_limit_id'];
				}
			}
			while ($row = $db->sql_fetchrow($result));
		}
		else
		{
			$upload_quota = $board_config['default_upload_quota'];
			$pm_quota = $board_config['default_pm_quota'];
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_SELECT_UPLOAD_QUOTA'		=> quota_limit_select('user_upload_quota', $upload_quota),
			'S_SELECT_PM_QUOTA'			=> quota_limit_select('user_pm_quota', $pm_quota))
		);
	}

	if (isset($_POST['deleteuser']))
	{
		process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, 0);
		process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, 0);
	}
	else if ($mode == 'save')
	{
		$upload_quota = get_var('user_upload_quota', 0);
		$pm_quota = get_var('user_pm_quota', 0);

		process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, $upload_quota);
		process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, $pm_quota);
	}

	if ( ( $mode == 'save' && isset( $_POST['submit'] ) ) || isset( $_POST['avatargallery'] ) || isset( $_POST['submitavatar'] ) || isset( $_POST['cancelavatar'] ) )
	{
		$user_id = intval($_POST['id']);

		if (!($this_userdata = get_userdata($user_id)))
		{
			trigger_error('对不起，用户不存在', E_USER_ERROR);
		}

		if( isset($_POST['deleteuser']) && ( $userdata['user_id'] != $user_id ) )
		{
			$sql = "SELECT g.group_id 
				FROM " . USER_GROUP_TABLE . " ug, " . GROUPS_TABLE . " g  
				WHERE ug.user_id = $user_id 
					AND g.group_id = ug.group_id 
					AND g.group_single_user = 1";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain group information for this user', E_USER_WARNING);
			}

			$row = $db->sql_fetchrow($result);
			
			$sql = "UPDATE " . POSTS_TABLE . "
				SET poster_id = " . DELETED . ", post_username = '" . str_replace("\\'", "''", addslashes($this_userdata['username'])) . "' 
				WHERE poster_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not update posts for this user', E_USER_WARNING);
			}

			$sql = "UPDATE " . TOPICS_TABLE . "
				SET topic_poster = " . DELETED . " 
				WHERE topic_poster = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not update topics for this user', E_USER_WARNING);
			}
			
			$sql = "UPDATE " . VOTE_USERS_TABLE . "
				SET vote_user_id = " . DELETED . "
				WHERE vote_user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not update votes for this user', E_USER_WARNING);
			}
			
			$sql = "UPDATE " . GROUPS_TABLE . "
				SET group_moderator = " . $userdata['user_id'] . "
				WHERE group_moderator = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not update group moderators', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . USERS_TABLE . "
				WHERE user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete user', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . USER_GROUP_TABLE . "
				WHERE user_id = $user_id";
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete user from user_group table', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . GROUPS_TABLE . "
				WHERE group_id = " . $row['group_id'];
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete group for this user', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . AUTH_ACCESS_TABLE . "
				WHERE group_id = " . $row['group_id'];
			if( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete group for this user', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
				WHERE user_id = $user_id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete user from topic watch table', E_USER_WARNING);
			}
			
			$sql = "DELETE FROM " . BANLIST_TABLE . "
				WHERE ban_userid = $user_id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete user from banlist table', E_USER_WARNING);
			}

			$sql = "DELETE FROM " . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete sessions for this user', E_USER_WARNING);
			}
			
			$sql = "DELETE FROM " . SESSIONS_KEYS_TABLE . "
				WHERE user_id = $user_id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not delete auto-login keys for this user', E_USER_WARNING);
			}

			$sql = "SELECT privmsgs_id
				FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_from_userid = $user_id 
					OR privmsgs_to_userid = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not select all users private messages', E_USER_WARNING);
			}

			while ( $row_privmsgs = $db->sql_fetchrow($result) )
			{
				$mark_list[] = $row_privmsgs['privmsgs_id'];
			}
			
			if ( count($mark_list) )
			{
				$delete_sql_id = implode(', ', $mark_list);
				
				$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
					WHERE privmsgs_text_id IN ($delete_sql_id)";
				$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_id IN ($delete_sql_id)";
				
				if ( !$db->sql_query($delete_sql) )
				{
					trigger_error('Could not delete private message info', E_USER_WARNING);
				}
				
				if ( !$db->sql_query($delete_text_sql) )
				{
					trigger_error('Could not delete private message text', E_USER_WARNING);
				}
			}

			$message = '用户已删除<br />点击 <a href="' . append_sid('admin_users.php') . '">这里</a> 返回用户管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
		}

		$username = ( !empty($_POST['username']) ) ? phpbb_clean_username($_POST['username']) : '';
		$email = ( !empty($_POST['email']) ) ? trim(strip_tags(htmlspecialchars( $_POST['email'] ) )) : '';
		$nic_color = ( !empty($_POST['nic_color']) ) ? trim(strip_tags(htmlspecialchars( $_POST['nic_color'] ) )) : '';
		$user_zvanie = ( !empty($_POST['user_zvanie']) ) ? trim(strip_tags(htmlspecialchars($_POST['user_zvanie']))) : '';

		$password = ( !empty($_POST['password']) ) ? trim(strip_tags(htmlspecialchars( $_POST['password'] ) )) : '';
		$password_confirm = ( !empty($_POST['password_confirm']) ) ? trim(strip_tags(htmlspecialchars( $_POST['password_confirm'] ) )) : '';

		$qq = ( !empty($_POST['qq']) ) ? trim(strip_tags( $_POST['qq'] ) ) : '';
		$aim = ( !empty($_POST['aim']) ) ? trim(strip_tags( $_POST['aim'] ) ) : '';
		$msn = ( !empty($_POST['msn']) ) ? trim(strip_tags( $_POST['msn'] ) ) : '';
		$yim = ( !empty($_POST['yim']) ) ? trim(strip_tags( $_POST['yim'] ) ) : '';

		$website = ( !empty($_POST['website']) ) ? trim(strip_tags( $_POST['website'] ) ) : '';
		$signature = ( !empty($_POST['signature']) ) ? trim(strip_tags( $_POST['signature'] ) ) : '';
		$location = ( !empty($_POST['location']) ) ? trim(strip_tags( $_POST['location'] ) ) : '';
		$occupation = ( !empty($_POST['occupation']) ) ? trim(strip_tags( $_POST['occupation'] ) ) : '';
		$interests = ( !empty($_POST['interests']) ) ? trim(strip_tags( $_POST['interests'] ) ) : '';
		$number = ( !empty($_POST['number']) ) ? trim(strip_tags( $_POST['number'] ) ) : '';
		$gender = ( isset($_POST['gender']) ) ? intval ($_POST['gender']) : 0;

		if (isset($_POST['birthday']) )
		{
			$birthday = intval ($_POST['birthday']);
			$b_day = realdate('j',$birthday);
			$b_md = realdate('n',$birthday);
			$b_year = realdate('Y',$birthday);
		}
		else
		{
			$b_day = ( isset($_POST['b_day']) ) ? intval ($_POST['b_day']) : 0;
			$b_md = ( isset($_POST['b_md']) ) ? intval ($_POST['b_md']) : 0;
			$b_year = ( isset($_POST['b_year']) ) ? intval ($_POST['b_year']) : 0;
			$birthday = mkrealdate($b_day,$b_md,$b_year);
		}
		$next_birthday_greeting = ( !empty($_POST['next_birthday_greeting']) ) ? intval( $_POST['next_birthday_greeting'] ) : 0;

		$signature = ( !empty($_POST['signature']) ) ? trim(str_replace('<br />', "\n", $_POST['signature'] ) ) : '';

		validate_optional_fields($qq, $aim, $msn, $yim, $website, $location, $occupation, $interests, $signature);

		$viewemail = ( isset( $_POST['viewemail']) ) ? ( ( $_POST['viewemail'] ) ? TRUE : 0 ) : 0;
		$allowviewonline = ( isset( $_POST['hideonline']) ) ? ( ( $_POST['hideonline'] ) ? 0 : TRUE ) : TRUE;
		$notifyreply_to_email = ( isset( $_POST['notifyreply_to_email']) ) ? ( ( $_POST['notifyreply_to_email'] ) ? TRUE : 0 ) : 0;
		$notifyreply_to_pm = ( isset( $_POST['notifyreply_to_pm']) ) ? ( ( $_POST['notifyreply_to_pm'] ) ? TRUE : 0 ) : 0;
		$notifypm = ( isset( $_POST['notifypm']) ) ? ( ( $_POST['notifypm'] ) ? TRUE : 0 ) : TRUE;
		$attachsig = ( isset( $_POST['attachsig']) ) ? ( ( $_POST['attachsig'] ) ? TRUE : 0 ) : 0;

		$gb_can = ( isset($_POST['gb_can']) ) ? ( ($_POST['gb_can']) ? TRUE : 0 ) : TRUE;

		$allowhtml = ( isset( $_POST['allowhtml']) ) ? intval( $_POST['allowhtml'] ) : $board_config['allow_html'];
		$allowbbcode = ( isset( $_POST['allowbbcode']) ) ? intval( $_POST['allowbbcode'] ) : $board_config['allow_bbcode'];
		$allowsmilies = ( isset( $_POST['allowsmilies']) ) ? intval( $_POST['allowsmilies'] ) : $board_config['allow_smilies'];
		$user_timezone = ( isset( $_POST['timezone']) ) ? doubleval( $_POST['timezone'] ) : $board_config['board_timezone'];
		$user_dateformat = ( $_POST['dateformat'] ) ? trim( $_POST['dateformat'] ) : $board_config['default_dateformat'];

		$user_avatar_local = ( isset( $_POST['avatarselect'] ) && !empty($_POST['submitavatar'] ) && $board_config['allow_avatar_local'] ) ? $_POST['avatarselect'] : ( ( isset( $_POST['avatarlocal'] )  ) ? $_POST['avatarlocal'] : '' );
		$user_avatar_category = ( isset($_POST['avatarcatname']) && $board_config['allow_avatar_local'] ) ? htmlspecialchars($_POST['avatarcatname']) : '' ;

		$user_avatar_remoteurl = ( !empty($_POST['avatarremoteurl']) ) ? trim( $_POST['avatarremoteurl'] ) : '';
		$user_avatar_url = ( !empty($_POST['avatarurl']) ) ? trim( $_POST['avatarurl'] ) : '';
		$user_avatar_loc = ( $_FILES['avatar']['tmp_name'] != "none") ? $_FILES['avatar']['tmp_name'] : '';
		$user_avatar_name = ( !empty($_FILES['avatar']['name']) ) ? $_FILES['avatar']['name'] : '';
		$user_avatar_size = ( !empty($_FILES['avatar']['size']) ) ? $_FILES['avatar']['size'] : 0;
		$user_avatar_filetype = ( !empty($_FILES['avatar']['type']) ) ? $_FILES['avatar']['type'] : '';

		$user_avatar = ( empty($user_avatar_loc) ) ? $this_userdata['user_avatar'] : '';
		$user_avatar_type = ( empty($user_avatar_loc) ) ? $this_userdata['user_avatar_type'] : '';		

		$user_status = ( !empty($_POST['user_status']) ) ? intval( $_POST['user_status'] ) : 0;
		$user_allowpm = ( !empty($_POST['user_allowpm']) ) ? intval( $_POST['user_allowpm'] ) : 0;
		$user_rank = ( !empty($_POST['user_rank']) ) ? intval( $_POST['user_rank'] ) : 0;
		$user_allowavatar = ( !empty($_POST['user_allowavatar']) ) ? intval( $_POST['user_allowavatar'] ) : 0;

		if (isset($_POST['topics_per_page']) )
		{
			$user_topics_per_page = ( intval($_POST['topics_per_page']) == 0 ) ? $board_config['topics_per_page'] : intval($_POST['topics_per_page']);
			$user_topics_per_page = ( $user_topics_per_page > $board_config['max_user_topics_per_page'] ) ? $board_config['topics_per_page'] : $user_topics_per_page;
		}
		if (isset($_POST['posts_per_page']) )
		{
			$user_posts_per_page = ( intval($_POST['topics_per_page']) == 0 ) ? $board_config['posts_per_page'] : intval($_POST['posts_per_page']);
			$user_posts_per_page = ( $user_topics_per_page > $board_config['max_user_posts_per_page'] ) ? $board_config['posts_per_page'] : $user_posts_per_page;
		}


		if( isset( $_POST['avatargallery'] ) || isset( $_POST['submitavatar'] ) || isset( $_POST['cancelavatar'] ) )
		{
			$username = stripslashes($username);
			$email = stripslashes($email);
			$nic_color = stripslashes($nic_color);
			$user_zvanie = stripslashes($user_zvanie);
			$password = '';
			$password_confirm = '';

			$qq = stripslashes($qq);
			$aim = htmlspecialchars(stripslashes($aim));
			$msn = htmlspecialchars(stripslashes($msn));
			$yim = htmlspecialchars(stripslashes($yim));
			$number = stripslashes($number);

			$website = htmlspecialchars(stripslashes($website));
			$signature = htmlspecialchars(stripslashes($signature));
			$location = htmlspecialchars(stripslashes($location));
			$occupation = htmlspecialchars(stripslashes($occupation));
			$interests = htmlspecialchars(stripslashes($interests));
			$user_dateformat = htmlspecialchars(stripslashes($user_dateformat));

			if ( !isset($_POST['cancelavatar'])) 
			{
				$user_avatar = $user_avatar_category . '/' . $user_avatar_local;
				$user_avatar_type = USER_AVATAR_GALLERY;
			}
		}
	}

	if( isset( $_POST['submit'] ) )
	{
		include(ROOT_PATH . 'includes/ucp/avatar.php');

		$error = FALSE;

		if (stripslashes($username) != $this_userdata['username'])
		{
			unset($rename_user);

			if ( stripslashes(strtolower($username)) != strtolower($this_userdata['username']) ) 
			{
				$result = validate_username($username);
				if ( $result['error'] )
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $result['error_msg'];
				}
				else if ( strtolower(str_replace("\\'", "''", $username)) == strtolower($userdata['username']) )
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . '对不起，该用户名已经存在';
				}
			}

			if (!$error)
			{
				$username_sql = "username = '" . str_replace("\\'", "''", $username) . "', ";
				$rename_user = $username;
			}
		}

		$passwd_sql = '';
		if( !empty($password) && !empty($password_confirm) )
		{

			if($password != $password_confirm)
			{
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . '两次输入密码不一样';
			}
			else
			{
				$password = md5($password);
				$passwd_sql = "user_password = '$password', ";
			}
		}
		else if( $password && !$password_confirm )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . '两次输入密码不一样';
		}
		else if( !$password && $password_confirm )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . '两次输入密码不一样';
		}

		if ($signature != '')
		{
			$sig_length_check = preg_replace('/(\[.*?)(=.*?)\]/is', '\\1]', stripslashes($signature));
			if ( $allowhtml )
			{
				$sig_length_check = preg_replace('/(\<.*?)(=.*?)( .*?=.*?)?([ \/]?\>)/is', '\\1\\3\\4', $sig_length_check);
			}

			// Only create a new bbcode_uid when there was no uid yet.
			if ( $signature_bbcode_uid == '' )
			{
				$signature_bbcode_uid = ( $allowbbcode ) ? make_bbcode_uid() : '';
			}
			$signature = prepare_message($signature, $allowhtml, $allowbbcode, $allowsmilies, $signature_bbcode_uid);

			if ( strlen($sig_length_check) > $board_config['max_sig_chars'] )
			{ 
				$error = TRUE;
				$error_msg .=  ( ( isset($error_msg) ) ? '<br />' : '' ) . '签名过长';
			}
		}

		$avatar_sql = "";
		if( isset($_POST['avatardel']) )
		{
			if( $this_userdata['user_avatar_type'] == USER_AVATAR_UPLOAD && $this_userdata['user_avatar'] != "" )
			{
				if( @file_exists(@phpbb_realpath('./../' . $board_config['avatar_path'] . "/" . $this_userdata['user_avatar'])) )
				{
					@unlink('./../' . $board_config['avatar_path'] . "/" . $this_userdata['user_avatar']);
				}
			}
			$avatar_sql = ", user_avatar = '', user_avatar_type = " . USER_AVATAR_NONE;
		}
		else if( ( $user_avatar_loc != "" || !empty($user_avatar_url) ) && !$error )
		{

			if( !empty($user_avatar_loc) && !empty($user_avatar_url) )
			{
				$error = TRUE;
				if( isset($error_msg) )
				{
					$error_msg .= "<br />";
				}
				$error_msg .= '只能设置一个头像';
			}

			if( $user_avatar_loc != "" )
			{
				if( file_exists(@phpbb_realpath($user_avatar_loc)) && preg_match("/\.jpg$|\.gif$|\.png$/", $user_avatar_name) )
				{
					if( $user_avatar_size <= $board_config['avatar_filesize'] && $user_avatar_size > 0)
					{
						$error_type = false;

						preg_match("'image\/[x\-]*([a-z]+)'", $user_avatar_filetype, $user_avatar_filetype);
						$user_avatar_filetype = $user_avatar_filetype[1];

						switch( $user_avatar_filetype )
						{
							case "jpeg":
							case "pjpeg":
							case "jpg":
								$imgtype = '.jpg';
								break;
							case "gif":
								$imgtype = '.gif';
								break;
							case "png":
								$imgtype = '.png';
								break;
							default:
								$error = true;
								$error_msg = (!empty($error_msg)) ? $error_msg . '<br />' . '头像文件必须是.JPG .gif或.png' : '头像文件必须是.JPG .gif或.png';
								break;
						}

						if( !$error )
						{
							list($width, $height) = @getimagesize($user_avatar_loc);

							if( $width <= $board_config['avatar_max_width'] && $height <= $board_config['avatar_max_height'] )
							{
								$user_id = $this_userdata['user_id'];

								$avatar_filename = $user_id . $imgtype;

								if( $this_userdata['user_avatar_type'] == USER_AVATAR_UPLOAD && $this_userdata['user_avatar'] != "" )
								{
									if( @file_exists(@phpbb_realpath("./../" . $board_config['avatar_path'] . "/" . $this_userdata['user_avatar'])) )
									{
										@unlink("./../" . $board_config['avatar_path'] . "/". $this_userdata['user_avatar']);
									}
								}
								@copy($user_avatar_loc, "./../" . $board_config['avatar_path'] . "/$avatar_filename");

								$avatar_sql = ", user_avatar = '$avatar_filename', user_avatar_type = " . USER_AVATAR_UPLOAD;
							}
							else
							{
								$l_avatar_size = '头像不得超过' . $board_config['avatar_max_width'] . '像素宽和' . $board_config['avatar_max_height'] . '像素高';

								$error = true;
								$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . $l_avatar_size : $l_avatar_size;
							}
						}
					}
					else
					{
						$l_avatar_size = '头像文件不大于 ' . round($board_config['avatar_filesize'] / 1024) . ' KB';

						$error = true;
						$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . $l_avatar_size : $l_avatar_size;
					}
				}
				else
				{
					$error = true;
					$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . '头像文件必须是.JPG .gif或.png' : '头像文件必须是.JPG .gif或.png';
				}
			}
			else if( !empty($user_avatar_url) )
			{

				preg_match("/^(http:\/\/)?([\w\-\.]+)\:?([0-9]*)\/(.*)$/", $user_avatar_url, $url_ary);

				if( !empty($url_ary[4]) )
				{
					$port = (!empty($url_ary[3])) ? $url_ary[3] : 80;

					$fsock = @fsockopen($url_ary[2], $port, $errno, $errstr);
					if( $fsock )
					{
						$base_get = "/" . $url_ary[4];

						@fputs($fsock, "GET $base_get HTTP/1.1\r\n");
						@fputs($fsock, "HOST: " . $url_ary[2] . "\r\n");
						@fputs($fsock, "Connection: close\r\n\r\n");

						unset($avatar_data);
						while( !@feof($fsock) )
						{
							$avatar_data .= @fread($fsock, $board_config['avatar_filesize']);
						}
						@fclose($fsock);

						if( preg_match("/Content-Length\: ([0-9]+)[^\/ ][\s]+/i", $avatar_data, $file_data1) && preg_match("/Content-Type\: image\/[x\-]*([a-z]+)[\s]+/i", $avatar_data, $file_data2) )
						{
							$file_size = $file_data1[1]; 
							$file_type = $file_data2[1];

							switch( $file_type )
							{
								case "jpeg":
								case "pjpeg":
								case "jpg":
									$imgtype = '.jpg';
									break;
								case "gif":
									$imgtype = '.gif';
									break;
								case "png":
									$imgtype = '.png';
									break;
								default:
									$error = true;
									$error_msg = (!empty($error_msg)) ? $error_msg . "<br />" . '头像文件必须是.JPG .gif或.png' : '头像文件必须是.JPG .gif或.png';
									break;
							}

							if( !$error && $file_size > 0 && $file_size < $board_config['avatar_filesize'] )
							{
								$avatar_data = substr($avatar_data, strlen($avatar_data) - $file_size, $file_size);

								$tmp_filename = tempnam ("/tmp", $this_userdata['user_id'] . "-");
								$fptr = @fopen($tmp_filename, "wb");
								$bytes_written = @fwrite($fptr, $avatar_data, $file_size);
								@fclose($fptr);

								if( $bytes_written == $file_size )
								{
									list($width, $height) = @getimagesize($tmp_filename);

									if( $width <= $board_config['avatar_max_width'] && $height <= $board_config['avatar_max_height'] )
									{
										$user_id = $this_userdata['user_id'];

										$avatar_filename = $user_id . $imgtype;

										if( $this_userdata['user_avatar_type'] == USER_AVATAR_UPLOAD && $this_userdata['user_avatar'] != "")
										{
											if( file_exists(@phpbb_realpath("./../" . $board_config['avatar_path'] . "/" . $this_userdata['user_avatar'])) )
											{
												@unlink("./../" . $board_config['avatar_path'] . "/" . $this_userdata['user_avatar']);
											}
										}
										@copy($tmp_filename, "./../" . $board_config['avatar_path'] . "/$avatar_filename");
										@unlink($tmp_filename);

										$avatar_sql = ", user_avatar = '$avatar_filename', user_avatar_type = " . USER_AVATAR_UPLOAD;
									}
									else
									{
										$l_avatar_size = '头像不得超过' . $board_config['avatar_max_width'] . '像素宽和' . $board_config['avatar_max_height'] . '像素高';

										$error = true;
										$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . $l_avatar_size : $l_avatar_size;
									}
								}
								else
								{

									@unlink($tmp_filename);
									trigger_error("Could not write avatar file to local storage. Please contact the board administrator with this message", E_USER_WARNING);
								}
							}
						}
						else
						{

							$error = true;
							$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . '您指定的 URL 没有包含相关的文件数据' : '您指定的 URL 没有包含相关的文件数据';
						}
					}
					else
					{

						$error = true;
						$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . '无法连接到您指定的 URL' : '无法连接到您指定的 URL';
					}
				}
				else
				{
					$error = true;
					$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . '您输入 URL 不完整' : '您输入 URL 不完整';
				}
			}
			else if( !empty($user_avatar_name) )
			{
				$l_avatar_size = '头像文件不大于 ' . round($board_config['avatar_filesize'] / 1024) . ' KB';

				$error = true;
				$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . $l_avatar_size : $l_avatar_size;
			}
		}
		else if( $user_avatar_remoteurl != "" && $avatar_sql == "" && !$error )
		{
			if( !preg_match("#^http:\/\/#i", $user_avatar_remoteurl) )
			{
				$user_avatar_remoteurl = "http://" . $user_avatar_remoteurl;
			}

			if( preg_match("#^(http:\/\/[a-z0-9\-]+?\.([a-z0-9\-]+\.)*[a-z]+\/.*?\.(gif|jpg|png)$)#is", $user_avatar_remoteurl) )
			{
				$avatar_sql = ", user_avatar = '" . str_replace("\'", "''", $user_avatar_remoteurl) . "', user_avatar_type = " . USER_AVATAR_REMOTE;
			}
			else
			{
				$error = true;
				$error_msg = ( !empty($error_msg) ) ? $error_msg . "<br />" . '指定的 URL 远程头像不正确' : '指定的 URL 远程头像不正确';
			}
		}
		else if( $user_avatar_local != "" && $avatar_sql == "" && !$error )
		{
			$avatar_sql = ", user_avatar = '" . str_replace("\'", "''", phpbb_ltrim(basename($user_avatar_category), "'") . '/' . phpbb_ltrim(basename($user_avatar_local), "'")) . "', user_avatar_type = " . USER_AVATAR_GALLERY;
		}

		if ($b_day || $b_md || $b_year) 
		{
			$user_age=(date('md')>=$b_md.(($b_day <= 9) ? '0':'').$b_day) ? date('Y') - $b_year : date('Y') - $b_year - 1 ;
			if (!checkdate($b_md,$b_day,$b_year))
			{
				$error = TRUE;
				if( isset($error_msg) )$error_msg .= "<br />";
				$error_msg .= '生日格式无效';
			} 
			else if ($user_age>$board_config['max_user_age'])
			{
				$error = TRUE;
				if( isset($error_msg) )$error_msg .= "<br />";
				$error_msg .= '对不起，您必须在 ' . $board_config['max_user_age'] . ' 月';
			}
			else if ($user_age<$board_config['min_user_age'])
			{
				$error = TRUE;
				if( isset($error_msg) )$error_msg .= "<br />";
				$error_msg .= '对不起，您必须至少 ' . $board_config['min_user_age'] . ' 月';
			} 
			else
			{
				$birthday = ($error) ? $birthday : mkrealdate($b_day,$b_md,$b_year);
			}
		} else $birthday = ($error) ? '' : 999999;

		if( !$error )
		{
			$sql = "UPDATE " . USERS_TABLE . "
				SET " . $username_sql . $passwd_sql . "user_email = '" . str_replace("\'", "''", $email) . "', user_qq = '" . str_replace("\'", "''", $qq) . "', user_number = '" . str_replace("\'", "''", $number) . "', user_nic_color = '" . str_replace("\'", "''", $nic_color) . "', user_zvanie = '" . str_replace("\'", "''", $user_zvanie) . "', user_website = '" . str_replace("\'", "''", $website) . "', user_sig = '" . str_replace("\'", "''", $signature) . "', user_occ = '" . str_replace("\'", "''", $occupation) . "', user_from = '" . str_replace("\'", "''", $location) . "', user_interests = '" . str_replace("\'", "''", $interests) . "', user_topics_per_page = '$user_topics_per_page', user_posts_per_page = '$user_posts_per_page', user_birthday='$birthday', user_next_birthday_greeting=$next_birthday_greeting, user_viewemail = $viewemail, user_aim = '" . str_replace("\'", "''", $aim) . "', user_yim = '" . str_replace("\'", "''", $yim) . "', user_msnm = '" . str_replace("\'", "''", $msn) . "', user_attachsig = $attachsig, user_sig_bbcode_uid = '$signature_bbcode_uid', user_allowsmile = $allowsmilies, user_allowhtml = $allowhtml, user_allowavatar = $user_allowavatar, user_allowbbcode = $allowbbcode, user_allow_viewonline = $allowviewonline, user_notify_to_email = $notifyreply_to_email, user_notify_to_pm = $notifyreply_to_pm, user_allow_pm = $user_allowpm, user_notify_pm = $notifypm, user_can_gb = $gb_can, user_timezone = $user_timezone, user_dateformat = '" . str_replace("\'", "''", $user_dateformat) . "', user_active = $user_status, user_rank = $user_rank, user_gender = '$gender'" . $avatar_sql . "
				WHERE user_id = $user_id";

			if( $result = $db->sql_query($sql) )
			{
				if( isset($rename_user) )
				{
					$sql = "UPDATE " . GROUPS_TABLE . "
						SET group_name = '".str_replace("\'", "''", $rename_user)."'
						WHERE group_name = '".str_replace("'", "''", $this_userdata['username'] )."'";
					if( !$result = $db->sql_query($sql) )
					{
						trigger_error('Could not rename users group', E_USER_WARNING);
					}
				}

				if (!$user_status)
				{
					$sql = "DELETE FROM " . SESSIONS_TABLE . " 
						WHERE session_user_id = " . $user_id;

					if ( !$db->sql_query($sql) )
					{
						trigger_error('Error removing user session', E_USER_WARNING);
					}
				}

				if ( !empty($passwd_sql) )
				{
					$session->reset_keys($user_id, $user_ip);
				}
			}
			else
			{
				trigger_error('无法更新用户的个人资料', E_USER_WARNING);
			}

			$message = '用户的个人资料已经成功更新<br />点击 <a href="' . append_sid("admin_users.php") . '">这里</a> 返回用户管理页面<br />点击 <a href="' . append_sid("index.php") . '">这里</a> 返回超级面板';

			trigger_error($message);
		}
		else
		{
			error_box('ERROR_BOX', $error_msg);

			$username = htmlspecialchars(stripslashes($username));
			$email = stripslashes($email);
			$nic_color = stripslashes($nic_color);
			$user_zvanie = stripslashes($user_zvanie);
			$password = '';
			$password_confirm = '';

			$qq = stripslashes($qq);
			$number = stripslashes($number);
			$aim = htmlspecialchars(str_replace('+', ' ', stripslashes($aim)));
			$msn = htmlspecialchars(stripslashes($msn));
			$yim = htmlspecialchars(stripslashes($yim));

			$website = htmlspecialchars(stripslashes($website));
			$signature = htmlspecialchars(stripslashes($signature));
			$location = htmlspecialchars(stripslashes($location));
			$occupation = htmlspecialchars(stripslashes($occupation));
			$interests = htmlspecialchars(stripslashes($interests));
			$user_dateformat = htmlspecialchars(stripslashes($user_dateformat));
		}
	}
	else if( !isset( $_POST['submit'] ) && $mode != 'save' && !isset( $_POST['avatargallery'] ) && !isset( $_POST['submitavatar'] ) && !isset( $_POST['cancelavatar'] ) )
	{
		if( isset( $_GET[POST_USERS_URL]) || isset( $_POST[POST_USERS_URL]) )
		{
			$user_id = ( isset( $_POST[POST_USERS_URL]) ) ? intval( $_POST[POST_USERS_URL]) : intval( $_GET[POST_USERS_URL]);
			$this_userdata = get_userdata($user_id);
			if( !$this_userdata )
			{
				trigger_error('对不起，用户不存在', E_USER_ERROR);
			}
		}
		else
		{
			$this_userdata = get_userdata($_POST['username'], true);
			if( !$this_userdata )
			{
				trigger_error('对不起，用户不存在', E_USER_ERROR);
			}
		}

		$user_id = $this_userdata['user_id'];
		$username = $this_userdata['username'];
		$email = $this_userdata['user_email'];
		$password = '';
		$password_confirm = '';

		$qq = $this_userdata['user_qq'];
		$aim = htmlspecialchars(str_replace('+', ' ', $this_userdata['user_aim'] ));
		$msn = htmlspecialchars($this_userdata['user_msnm']);
		$yim = htmlspecialchars($this_userdata['user_yim']);
		$nic_color = htmlspecialchars($this_userdata['user_nic_color']);
		$user_zvanie = htmlspecialchars($this_userdata['user_zvanie']);

		$website = htmlspecialchars($this_userdata['user_website']);
		$signature = htmlspecialchars($this_userdata['user_sig']);
		$location = htmlspecialchars($this_userdata['user_from']);
		$occupation = htmlspecialchars($this_userdata['user_occ']);
		$interests = htmlspecialchars($this_userdata['user_interests']);
		$number = $this_userdata['user_number'];
		$gender = $this_userdata['user_gender'];

		$next_birthday_greeting = $this_userdata['user_next_birthday_greeting'];
		if ($this_userdata['user_birthday']!=999999)
		{
			$birthday = realdate('y-m-d',$this_userdata['user_birthday']);
			$b_day = realdate('j',$this_userdata['user_birthday']);
			$b_md = realdate('n',$this_userdata['user_birthday']);
			$b_year = realdate('Y',$this_userdata['user_birthday']);
		}
		else
		{
			$b_day = '';
			$b_md = '';
			$b_year = '';
			$birthday = '';
		}

		$viewemail = $this_userdata['user_viewemail'];
		$notifypm = $this_userdata['user_notify_pm'];
		$gb_can = $this_userdata['user_can_gb'];
		$notifyreply_to_email = $this_userdata['user_notify_to_email'];
		$notifyreply_to_pm = $this_userdata['user_notify_to_pm'];
		$attachsig = $this_userdata['user_attachsig'];
		$allowhtml = $this_userdata['user_allowhtml'];
		$allowbbcode = $this_userdata['user_allowbbcode'];
		$allowsmilies = $this_userdata['user_allowsmile'];
		$allowviewonline = $this_userdata['user_allow_viewonline'];

		$user_avatar = $this_userdata['user_avatar'];
		$user_avatar_type = $this_userdata['user_avatar_type'];
		$user_timezone = $this_userdata['user_timezone'];
		$user_dateformat = htmlspecialchars($this_userdata['user_dateformat']);
		
		$user_status = $this_userdata['user_active'];
		$user_allowavatar = $this_userdata['user_allowavatar'];
		$user_allowpm = $this_userdata['user_allow_pm'];
		$user_topics_per_page = $this_userdata['user_topics_per_page'];
		$user_posts_per_page = $this_userdata['user_posts_per_page'];
		
		$COPPA = false;

		$html_status =  ($this_userdata['user_allowhtml'] ) ? 'HTML::允许' : 'HTML::禁用';
		$bbcode_status = ($this_userdata['user_allowbbcode'] ) ? 'BBCode::允许' : 'BBCode::禁用';
		$smilies_status = ($this_userdata['user_allowsmile'] ) ? '表情::允许' : '表情::禁用';
	}

	if( isset($_POST['avatargallery']) && !$error )
	{
		if( !$error )
		{
			$user_id = intval($_POST['id']);

			$template->set_filenames(array(
				"body" => "admin/user_avatar_gallery.tpl")
			);

			$dir = @opendir("../" . $board_config['avatar_gallery_path']);

			$avatar_images = array();
			while( $file = @readdir($dir) )
			{
				if( $file != "." && $file != ".." && !is_file(phpbb_realpath("./../" . $board_config['avatar_gallery_path'] . "/" . $file)) && !is_link(phpbb_realpath("./../" . $board_config['avatar_gallery_path'] . "/" . $file)) )
				{
					$sub_dir = @opendir("../" . $board_config['avatar_gallery_path'] . "/" . $file);

					$avatar_row_count = 0;
					$avatar_col_count = 0;

					while( $sub_file = @readdir($sub_dir) )
					{
						if( preg_match("/(\.gif$|\.png$|\.jpg)$/is", $sub_file) )
						{
							$avatar_images[$file][$avatar_row_count][$avatar_col_count] = $sub_file;

							$avatar_col_count++;
							if( $avatar_col_count == 5 )
							{
								$avatar_row_count++;
								$avatar_col_count = 0;
							}
						}
					}
				}
			}
	
			@closedir($dir);

			if( isset($_POST['avatarcategory']) )
			{
				$category = htmlspecialchars($_POST['avatarcategory']);
			}
			else
			{
				list($category, ) = each($avatar_images);
			}
			@reset($avatar_images);

			$s_categories = "";
			foreach($avatar_images as $key)
			{
				$selected = ( $key == $category ) ? "selected=\"selected\"" : "";
				if( count($avatar_images[$key]) )
				{
					$s_categories .= '<option value="' . $key . '"' . $selected . '>' . ucfirst($key) . '</option>';
				}
			}

			$s_colspan = 0;
			for($i = 0; $i < count($avatar_images[$category]); $i++)
			{
				$template->assign_block_vars("avatar_row", array());

				$s_colspan = max($s_colspan, count($avatar_images[$category][$i]));

				for($j = 0; $j < count($avatar_images[$category][$i]); $j++)
				{
					$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

					$template->assign_block_vars("avatar_row.avatar_column", array(
						"AVATAR_IMAGE" => "../" . $board_config['avatar_gallery_path'] . '/' . $category . '/' . $avatar_images[$category][$i][$j],
						"ROW_CLASS" => $row_class,
						"S_OPTIONS_AVATAR" => $avatar_images[$category][$i][$j])
					);
				}
			}

			$coppa = ( ( !$_POST['coppa'] && !$_GET['coppa'] ) || $mode == "register") ? 0 : TRUE;

			$s_hidden_fields = '<input type="hidden" name="mode" value="edit" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="avatarcatname" value="' . $category . '" />';
			$s_hidden_fields .= '<input type="hidden" name="id" value="' . $user_id . '" />';

			$s_hidden_fields .= '<input type="hidden" name="username" value="' . str_replace("\"", "&quot;", $username) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="email" value="' . str_replace("\"", "&quot;", $email) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="qq" value="' . str_replace("\"", "&quot;", $qq) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="aim" value="' . str_replace("\"", "&quot;", $aim) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="msn" value="' . str_replace("\"", "&quot;", $msn) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="yim" value="' . str_replace("\"", "&quot;", $yim) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="website" value="' . str_replace("\"", "&quot;", $website) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="signature" value="' . str_replace("\"", "&quot;", $signature) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="location" value="' . str_replace("\"", "&quot;", $location) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="occupation" value="' . str_replace("\"", "&quot;", $occupation) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="interests" value="' . str_replace("\"", "&quot;", $interests) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="number" value="' . str_replace("\"", "&quot;", $number) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="birthday" value="'.$birthday.'" />';
			$s_hidden_fields .= '<input type="hidden" name="next_birthday_greeting" value="'.$next_birthday_greeting.'" />';
			$s_hidden_fields .= '<input type="hidden" name="viewemail" value="' . $viewemail . '" />';
			$s_hidden_fields .= '<input type="hidden" name="gender" value="' . $gender . '" />';
			$s_hidden_fields .= '<input type="hidden" name="notifypm" value="' . $notifypm . '" />';
			$s_hidden_fields .= '<input type="hidden" name="notifyreply_to_email" value="' . $notifyreply_to_email . '" />';
			$s_hidden_fields .= '<input type="hidden" name="notifyreply_to_pm" value="' . $notifyreply_to_pm . '" />';
			$s_hidden_fields .= '<input type="hidden" name="attachsig" value="' . $attachsig . '" />';
			$s_hidden_fields .= '<input type="hidden" name="allowhtml" value="' . $allowhtml . '" />';
			$s_hidden_fields .= '<input type="hidden" name="allowbbcode" value="' . $allowbbcode . '" />';
			$s_hidden_fields .= '<input type="hidden" name="allowsmilies" value="' . $allowsmilies . '" />';
			$s_hidden_fields .= '<input type="hidden" name="hideonline" value="' . !$allowviewonline . '" />';
			$s_hidden_fields .= '<input type="hidden" name="timezone" value="' . $user_timezone . '" />';
			$s_hidden_fields .= '<input type="hidden" name="dateformat" value="' . str_replace("\"", "&quot;", $user_dateformat) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="user_status" value="' . $user_status . '" />';
			$s_hidden_fields .= '<input type="hidden" name="user_allowpm" value="' . $user_allowpm . '" />';
			$s_hidden_fields .= '<input type="hidden" name="user_allowavatar" value="' . $user_allowavatar . '" />';
			$s_hidden_fields .= '<input type="hidden" name="user_rank" value="' . $user_rank . '" />';
			$s_hidden_fields .= '<input type="hidden" name="topics_per_page" value="' . $user_topics_per_page . '" />';
			$s_hidden_fields .= '<input type="hidden" name="posts_per_page" value="' . $user_posts_per_page . '" />';

			$template->assign_vars(array(
				"U_USERS_ADMIN"			=> append_sid("admin_users.php"),
				"S_OPTIONS_CATEGORIES" 	=> $s_categories, 
				"S_COLSPAN" 			=> $s_colspan, 
				"S_PROFILE_ACTION" 		=> append_sid("admin_users.php?mode=$mode"), 
				"S_HIDDEN_FIELDS" 		=> $s_hidden_fields)
			);
		}
	}
	else
	{
		$s_hidden_fields = '<input type="hidden" name="mode" value="save" /><input type="hidden" name="agreed" value="true" />';
		$s_hidden_fields .= '<input type="hidden" name="id" value="' . $this_userdata['user_id'] . '" />';

		if( !empty($user_avatar_local) )
		{
			$s_hidden_fields .= '<input type="hidden" name="avatarlocal" value="' . $user_avatar_local . '" /><input type="hidden" name="avatarcatname" value="' . $user_avatar_category . '" />';
		}

		if( $user_avatar_type )
		{
			switch( $user_avatar_type )
			{
				case USER_AVATAR_UPLOAD:
					$avatar = '<img src="../' . $board_config['avatar_path'] . '/' . $user_avatar . '" alt="" />';
					break;
				case USER_AVATAR_REMOTE:
					$avatar = '<img src="' . $user_avatar . '" alt="" />';
					break;
				case USER_AVATAR_GALLERY:
					$avatar = '<img src="../' . $board_config['avatar_gallery_path'] . '/' . $user_avatar . '" alt="" />';
					break;
			}
		}
		else
		{
			$avatar = "";
		}

		$sql = "SELECT * FROM " . RANKS_TABLE . "
			WHERE rank_special = 1
			ORDER BY rank_title";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain ranks data', E_USER_WARNING);
		}

		$rank_select_box = '<option value="0">不指定</option>';
		while( $row = $db->sql_fetchrow($result) )
		{
			$rank = $row['rank_title'];
			$rank_id = $row['rank_id'];
			
			$selected = ( $this_userdata['user_rank'] == $rank_id ) ? ' selected="selected"' : '';
			$rank_select_box .= '<option value="' . $rank_id . '"' . $selected . '>' . $rank . '</option>';
		}

		$template->set_filenames(array(
			"body" => "admin/user_edit_body.tpl")
		);

		$s_b_day = '<select name="b_day" size="1" class="gensmall"> 
			<option value="0">-</option> 
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
			<option value="16">16</option>
			<option value="17">17</option>
			<option value="18">18</option>
			<option value="19">19</option>
			<option value="20">20</option>
			<option value="21">21</option>
			<option value="22">22</option>
			<option value="23">23</option>
			<option value="24">24</option>
			<option value="25">25</option>
			<option value="26">26</option>
			<option value="27">27</option>
			<option value="28">28</option>
			<option value="29">29</option>
			<option value="30">30</option>
			<option value="31">31</option>
			</select>日';
		$s_b_md = '<select name="b_md" size="1" class="gensmall"> 
				<option value="0">-</option> 
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			</select>月';
		$s_b_day	= str_replace("value=\"".$b_day."\">", "value=\"".$b_day."\" SELECTED>" ,$s_b_day);
		$s_b_md 	= str_replace("value=\"".$b_md."\">", "value=\"".$b_md."\" SELECTED>" ,$s_b_md);
		$s_b_year 	= '<input type="text" name="b_year" size="4" maxlength="4" value="' . $b_year . '" />年'; 
		$s_birthday	= $s_b_year . $s_b_md . $s_b_day;

		$gender_male_checked = '';
		$gender_female_checked = '';
		$gender_no_specify_checked = '';
		
		switch ($gender) 
		{ 
		   case 1: $gender_male_checked="checked=\"checked\"";break; 
		   case 2: $gender_female_checked="checked=\"checked\"";break; 
		   default:$gender_no_specify_checked="checked=\"checked\""; 
		}

		$ini_val = ( phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
		$form_enctype = ( !@$ini_val('file_uploads') || phpversion() == '4.0.4pl1' || !$board_config['allow_avatar_upload'] || ( phpversion() < '4.0.3' && @$ini_val('open_basedir') != '' ) ) ? '' : 'enctype="multipart/form-data"';

		$template->assign_vars(array(
			'USERNAME' 							=> $username,
			'EMAIL' 							=> $email,
			'YIM' 								=> $yim,
			'QQ' 								=> $qq,
			'NUMBER' 							=> $number,
			'MSN' 								=> $msn,
			'AIM' 								=> $aim,
			'OCCUPATION' 						=> $occupation,
			'INTERESTS' 						=> $interests,
			'NEXT_BIRTHDAY_GREETING' 			=> $next_birthday_greeting,
			'S_BIRTHDAY' 						=> $s_birthday,
			'GENDER' 							=> $gender, 
			'GENDER_MALE_CHECKED' 				=> $gender_male_checked, 
			'GENDER_FEMALE_CHECKED' 			=> $gender_female_checked,
			'GENDER_NO_SPECIFY_CHECKED'			=> $gender_no_specify_checked,
			'LOCATION' 							=> $location,
			'WEBSITE' 							=> $website,
			'SIGNATURE' 						=> $signature,
			'VIEW_EMAIL_YES' 					=> ($viewemail) ? 'checked="checked"' : '',
			'VIEW_EMAIL_NO' 					=> (!$viewemail) ? 'checked="checked"' : '',
			'HIDE_USER_YES' 					=> (!$allowviewonline) ? 'checked="checked"' : '',
			'HIDE_USER_NO' 						=> ($allowviewonline) ? 'checked="checked"' : '',
			'NOTIFY_PM_YES' 					=> ($notifypm) ? 'checked="checked"' : '',
			'NOTIFY_PM_NO' 						=> (!$notifypm) ? 'checked="checked"' : '',
			'GB_CAN_YES' 						=> ($gb_can) ? 'checked="checked"' : '',
			'GB_CAN_NO'	 						=> (!$gb_can) ? 'checked="checked"' : '',
			'ALWAYS_ADD_SIGNATURE_YES' 			=> ($attachsig) ? 'checked="checked"' : '',
			'ALWAYS_ADD_SIGNATURE_NO' 			=> (!$attachsig) ? 'checked="checked"' : '',
			'NOTIFY_REPLY_TO_EMAIL' 			=> ( $notifyreply_to_email ) ? ' checked="checked"' : '',
			'NOTIFY_REPLY_TO_PM' 				=> ( $notifyreply_to_pm ) ? ' checked="checked"' : '',
			'ALWAYS_ALLOW_BBCODE_YES' 			=> ($allowbbcode) ? 'checked="checked"' : '',
			'ALWAYS_ALLOW_BBCODE_NO' 			=> (!$allowbbcode) ? 'checked="checked"' : '',
			'ALWAYS_ALLOW_HTML_YES' 			=> ($allowhtml) ? 'checked="checked"' : '',
			'ALWAYS_ALLOW_HTML_NO' 				=> (!$allowhtml) ? 'checked="checked"' : '',
			'ALWAYS_ALLOW_SMILIES_YES' 			=> ($allowsmilies) ? 'checked="checked"' : '',
			'ALWAYS_ALLOW_SMILIES_NO' 			=> (!$allowsmilies) ? 'checked="checked"' : '',
			'AVATAR' 							=> $avatar,
			'TIMEZONE_SELECT' 					=> tz_select($user_timezone),
			'DATE_FORMAT' 						=> $user_dateformat,
			'ALLOW_PM_YES' 						=> ($user_allowpm) ? 'checked="checked"' : '',
			'ALLOW_PM_NO' 						=> (!$user_allowpm) ? 'checked="checked"' : '',
			'ALLOW_AVATAR_YES' 					=> ($user_allowavatar) ? 'checked="checked"' : '',
			'ALLOW_AVATAR_NO' 					=> (!$user_allowavatar) ? 'checked="checked"' : '',
			'USER_ACTIVE_YES' 					=> ($user_status) ? 'checked="checked"' : '',
			'USER_ACTIVE_NO' 					=> (!$user_status) ? 'checked="checked"' : '', 
			'RANK_SELECT_BOX' 					=> $rank_select_box,
			'TOPICS_PER_PAGE' 					=> $user_topics_per_page,
			'POSTS_PER_PAGE' 					=> $user_posts_per_page,

			'NIC_COLOR' 						=> $nic_color, 
			'USER_ZVANIE' 						=> $user_zvanie, 
			//'L_SIGNATURE_EXPLAIN' 				=> sprintf($lang['Signature_explain'], $board_config['max_sig_chars'] ),
			'S_FORM_ENCTYPE' 					=> $form_enctype,

			'HTML_STATUS' 						=> $html_status,
			'BBCODE_STATUS' 					=> sprintf($bbcode_status, '<a href="../' . append_sid("faq.php?mode=bbcode") . '" target="_phpbbcode">', '</a>'), 
			'SMILIES_STATUS' 					=> $smilies_status,

			'U_USERS_ADMIN'						=> append_sid("admin_users.php"),
			'S_HIDDEN_FIELDS' 					=> $s_hidden_fields,
			'S_PROFILE_ACTION' 					=> append_sid("admin_users.php"))
		);

		if( file_exists(@phpbb_realpath('./../' . $board_config['avatar_path'])) && ($board_config['allow_avatar_upload'] == TRUE) )
		{
			if ( $form_enctype != '' )
			{
				$template->assign_block_vars('avatar_local_upload', array() );
			}
			$template->assign_block_vars('avatar_remote_upload', array() );
		}
		
		if( $board_config['allow_avatar_remote'] == TRUE )
		{
			$template->assign_block_vars('avatar_remote_link', array() );
		}
	}

	$template->pparse('body');
}
else
{

	$template->set_filenames(array(
		'body' => 'admin/user_select_body.tpl')
	);

	$template->assign_vars(array(
		'L_USER_TITLE'		=> '选择用户',
		'L_USER_SELECT'		=> '选择用户',

		'U_SEARCH_USER' 	=> append_sid("./../search.php?mode=searchuser"), 

		'S_USER_ACTION' 	=> append_sid("admin_users.php"),
		'S_USER_SELECT' 	=> '')
	);
	$template->pparse('body');

}

page_footer();

?>