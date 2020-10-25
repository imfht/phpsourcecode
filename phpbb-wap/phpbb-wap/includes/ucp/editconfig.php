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

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

$unhtml_specialchars_match 		= array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace 	= array('>', '<', '"', '&');

$error = FALSE;
$error_msg = '';

if ( isset($_POST['submit']) || isset($_POST['avatargallery']) || isset($_POST['submitavatar']) || isset($_POST['cancelavatar']) )
{

	if (isset($_POST['topics_per_page']) )
	{
		$user_topics_per_page = abs(( intval($_POST['topics_per_page']) == 0 ) ? $board_config['topics_per_page'] : intval($_POST['topics_per_page']));
		$user_topics_per_page = abs(( $user_topics_per_page > $board_config['max_user_topics_per_page'] ) ? $board_config['topics_per_page'] : $user_topics_per_page);
	}
	if (isset($_POST['posts_per_page']) )
	{
		$user_posts_per_page = abs(( intval($_POST['topics_per_page']) == 0 ) ? $board_config['posts_per_page'] : intval($_POST['posts_per_page']));
		$user_posts_per_page = abs(( $user_topics_per_page > $board_config['max_user_posts_per_page'] ) ? $board_config['posts_per_page'] : $user_posts_per_page);
	}

	$viewemail 				= ( isset($_POST['viewemail']) ) ? ( ($_POST['viewemail']) ? TRUE : 0 ) : 0;
	$allowviewonline 		= ( isset($_POST['hideonline']) ) ? ( ($_POST['hideonline']) ? 0 : TRUE ) : TRUE;
	$notifyreply_to_email 	= ( isset($_POST['notifyreply_to_email']) ) ? ( ($_POST['notifyreply_to_email']) ? TRUE : 0 ) : 0;
	$notifyreply_to_pm 		= ( isset($_POST['notifyreply_to_pm']) ) ? ( ($_POST['notifyreply_to_pm']) ? TRUE : 0 ) : 0;
	$notifypm 				= ( isset($_POST['notifypm']) ) ? ( ($_POST['notifypm']) ? TRUE : 0 ) : TRUE;
	$popup_pm 				= ( isset($_POST['popup_pm']) ) ? ( ($_POST['popup_pm']) ? TRUE : 0 ) : TRUE;
	$user_can_gb 			= ( isset($_POST['gb_can']) ) ? ( ($_POST['gb_can']) ? TRUE : 0 ) : 1;

	$attachsig 				= ( isset($_POST['attachsig']) ) ? ( ($_POST['attachsig']) ? TRUE : 0 ) : $userdata['user_attachsig'];
	$allowhtml 				= ( isset($_POST['allowhtml']) ) ? ( ($_POST['allowhtml']) ? TRUE : 0 ) : $userdata['user_allowhtml'];
	$allowbbcode 			= ( isset($_POST['allowbbcode']) ) ? ( ($_POST['allowbbcode']) ? TRUE : 0 ) : $userdata['user_allowbbcode'];
	$allowsmilies 			= ( isset($_POST['allowsmilies']) ) ? ( ($_POST['allowsmilies']) ? TRUE : 0 ) : $userdata['user_allowsmile'];
	$on_off 				= ( isset($_POST['on_off']) ) ? ( ($_POST['on_off']) ? TRUE : 0 ) : 1;
	$attach_on 				= ( isset($_POST['attach_on']) ) ? ( ($_POST['attach_on']) ? TRUE : 0 ) : 1;
	
	$quick_answer 			= ( isset($_POST['quick_answer']) ) ? ( ($_POST['quick_answer']) ? TRUE : 0 ) : 1;
	$bb_panel 				= ( isset($_POST['bb_panel']) ) ? ( ($_POST['bb_panel']) ? TRUE : 0 ) : 1;
	$view_latest_post 		= ( isset($_POST['view_latest_post']) ) ? ( ($_POST['view_latest_post']) ? 1 : 0 ) : 0;
	$java_otv 				= ( isset($_POST['java_otv']) ) ? ( ($_POST['java_otv']) ? TRUE : 0 ) : 1;
	$message_quote 			= ( isset($_POST['message_quote']) ) ? ( ($_POST['message_quote']) ? 1 : 0 ) : 1;
	$index_spisok 			= ( isset($_POST['index_spisok']) ) ? ( ($_POST['index_spisok']) ? TRUE : 0 ) : 1;
	$posl_red 				= ( isset($_POST['posl_red']) ) ? ( ($_POST['posl_red']) ? TRUE : 0 ) : 1;
	$post_leng 				= ( isset($_POST['post_leng']) ) ? intval($_POST['post_leng']) : $userdata['user_post_leng'];

	$user_timezone 			= ( isset($_POST['timezone']) ) ? doubleval($_POST['timezone']) : $board_config['board_timezone'];
	$user_style				= ( isset($_POST['style']) ) ? intval($_POST['style']) : $board_config['default_style']; 

	$sql = "SELECT config_value
		FROM " . CONFIG_TABLE . "
		WHERE config_name = 'default_dateformat'";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not select default dateformat', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);
	$board_config['default_dateformat'] = $row['config_value'];
	$user_dateformat = ( !empty($_POST['dateformat']) ) ? trim(htmlspecialchars($_POST['dateformat'])) : $board_config['default_dateformat'];

	$user_avatar_local = ( isset($_POST['avatarselect']) && !empty($_POST['submitavatar']) && $board_config['allow_avatar_local'] ) ? htmlspecialchars($_POST['avatarselect']) : ( ( isset($_POST['avatarlocal'])  ) ? htmlspecialchars($_POST['avatarlocal']) : '' );
	$user_avatar_category = ( isset($_POST['avatarcatname']) && $board_config['allow_avatar_local'] ) ? htmlspecialchars($_POST['avatarcatname']) : '' ;

	$user_avatar_remoteurl = ( !empty($_POST['avatarremoteurl']) ) ? trim(htmlspecialchars($_POST['avatarremoteurl'])) : '';

	if (isset($_FILES['avatar']) && !empty($_FILES['avatar']))
	{
		$user_avatar_upload = ( !empty($_POST['avatarurl']) ) ? trim($_POST['avatarurl']) : ( ( $_FILES['avatar']['tmp_name'] != "none") ? $_FILES['avatar']['tmp_name'] : '' );
		$user_avatar_name = ( !empty($_FILES['avatar']['name']) ) ? $_FILES['avatar']['name'] : '';
		$user_avatar_size = ( !empty($_FILES['avatar']['size']) ) ? $_FILES['avatar']['size'] : 0;
		$user_avatar_filetype = ( !empty($_FILES['avatar']['type']) ) ? $_FILES['avatar']['type'] : '';
	}
	else
	{
		$user_avatar_upload = '';
		$user_avatar_upload = '';
		$user_avatar_size	= 0;
		$user_avatar_filetype = '';
	}

	$user_avatar = ( empty($user_avatar_local) ) ? $userdata['user_avatar'] : '';
	$user_avatar_type = ( empty($user_avatar_local) ) ? $userdata['user_avatar_type'] : '';

	if ( (isset($_POST['avatargallery']) || isset($_POST['submitavatar']) || isset($_POST['cancelavatar'])) && (!isset($_POST['submit'])) )
	{
		$user_dateformat = stripslashes($user_dateformat);

		if ( !isset($_POST['cancelavatar']))
		{
			$user_avatar = $user_avatar_category . '/' . $user_avatar_local;
			$user_avatar_type = USER_AVATAR_GALLERY;
		}
	}
}

if ( isset($_POST['submit']) )
{
	include(ROOT_PATH . 'includes/ucp/avatar.php');

	$user_id = intval($_POST['user_id']);
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . '您不能编辑他人的资料';
	}

	$avatar_sql = '';

	if ( isset($_POST['avatardel']) )
	{
		$avatar_sql = user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
	}
	else
	if ( ( !empty($user_avatar_upload) || !empty($user_avatar_name) ) && $board_config['allow_avatar_upload'] )
	{
		if ( !empty($user_avatar_upload) )
		{
			$avatar_mode = (empty($user_avatar_name)) ? 'remote' : 'local';
			$avatar_sql = user_avatar_upload($mode, $avatar_mode, $userdata['user_avatar'], $userdata['user_avatar_type'], $error, $error_msg, $user_avatar_upload, $user_avatar_name, $user_avatar_size, $user_avatar_filetype);
		}
		else if ( !empty($user_avatar_name) )
		{
			$l_avatar_size = '图片不能大于' . round($board_config['avatar_filesize'] / 1024) . 'KB';

			$error = true;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $l_avatar_size;
		}
	}
	else if ( $user_avatar_remoteurl != '' && $board_config['allow_avatar_remote'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_url($mode, $error, $error_msg, $user_avatar_remoteurl);
	}
	else if ( $user_avatar_local != '' && $board_config['allow_avatar_local'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_gallery($mode, $error, $error_msg, $user_avatar_local, $user_avatar_category);
	}

	if ( !$error )
	{
		$user_active = 1;
		$user_actkey = '';

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_topics_per_page = '$user_topics_per_page', user_posts_per_page = '$user_posts_per_page', user_viewemail = $viewemail, user_on_off = '$on_off', user_attachsig = $attachsig, user_allowsmile = $allowsmilies, user_allowhtml = $allowhtml, user_allowbbcode = $allowbbcode, user_allow_viewonline = $allowviewonline, user_notify_to_email = $notifyreply_to_email, user_notify_to_pm = $notifyreply_to_pm, user_notify_pm = $notifypm, user_popup_pm = $popup_pm, user_timezone = $user_timezone, user_style = $user_style, user_dateformat = '" . str_replace("\'", "''", $user_dateformat) . "', user_attach_mod = '$attach_on', user_quick_answer = '$quick_answer', user_bb_panel = '$bb_panel', user_java_otv = '$java_otv', user_message_quote = '$message_quote', user_view_latest_post = '$view_latest_post', user_index_spisok = '$index_spisok', user_can_gb = '$user_can_gb', user_posl_red = '$posl_red', user_post_leng = '$post_leng', user_active = $user_active, user_actkey = '" . str_replace("\'", "''", $user_actkey) . "'" . $avatar_sql . "
			WHERE user_id = $user_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not update users table', E_USER_WARNING);
		}

		$message = '您的用户资料已更新<br />点击 <a href="' . append_sid('ucp.php?mode=editconfig') . '">这里</a> 返回上一页<br />点击 <a href="' . append_sid("index.php") . '">这里</a> 返回首页';

		trigger_error($message);
	}
}


if ( $error )
{
	$user_dateformat 		= stripslashes($user_dateformat);
	$user_topics_per_page 	= $userdata['user_topics_per_page'];
	$user_posts_per_page 	= $userdata['user_posts_per_page'];
	$post_leng 				= $userdata['user_post_leng'];
}
else if ( !isset($_POST['avatargallery']) && !isset($_POST['submitavatar']) && !isset($_POST['cancelavatar']) )
{
	$user_id = $userdata['user_id'];

	
	$viewemail 				= $userdata['user_viewemail'];
	$notifypm 				= $userdata['user_notify_pm'];
	$popup_pm 				= $userdata['user_popup_pm'];
	$user_can_gb 			= $userdata['user_can_gb'];
	$notifyreply_to_email 	= $userdata['user_notify_to_email'];
	$notifyreply_to_pm 		= $userdata['user_notify_to_pm'];
	$attachsig 				= $userdata['user_attachsig'];
	$allowhtml 				= $userdata['user_allowhtml'];
	$allowbbcode 			= $userdata['user_allowbbcode'];
	$allowsmilies 			= $userdata['user_allowsmile'];
	$allowviewonline 		= $userdata['user_allow_viewonline'];

	$user_avatar 			= ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar'] : '';
	$user_avatar_type 		= ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar_type'] : USER_AVATAR_NONE;

	$user_timezone 			= $userdata['user_timezone'];
	$user_style				= $userdata['user_style'];
	$user_dateformat 		= $userdata['user_dateformat'];
	$user_topics_per_page 	= $userdata['user_topics_per_page'];
	$user_posts_per_page 	= $userdata['user_posts_per_page'];
	$post_leng 				= $userdata['user_post_leng'];
}

page_header($page_title);

$user_id = ( !empty($_POST['user_id']) ) ? intval($_POST['user_id']) : $user_id;
if ( $user_id != $userdata['user_id'] )
{
	$error = TRUE;
	$error_msg = '您不能编辑他人的资料';
}

if( isset($_POST['avatargallery']) && !$error )
{
	include(ROOT_PATH . 'includes/ucp/avatar.php');

	$avatar_category = ( !empty($_POST['avatarcategory']) ) ? htmlspecialchars($_POST['avatarcategory']) : '';

	$template->set_filenames(array(
		'body' => 'ucp/avatar_gallery.tpl')
	);

	$allowviewonline = !$allowviewonline;

	display_avatar_gallery($mode, $avatar_category, $user_id, $viewemail, $notifypm, $popup_pm, $user_can_gb, $notifyreply_to_email, $notifyreply_to_pm, $attachsig, $allowhtml, $allowbbcode, $allowsmilies, $allowviewonline, $user_style, $user_lang, $user_timezone, $user_dateformat, $userdata['session_id'], $user_topics_per_page, $user_posts_per_page, $birthday, $gender);
}
else
{
	include(ROOT_PATH . 'includes/functions/selects.php');

	if ( !isset($coppa) )
	{
		$coppa = FALSE;
	}

	$avatar_img = '';
	if ( $user_avatar_type )
	{
		switch( $user_avatar_type )
		{
			case USER_AVATAR_UPLOAD:
				$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $user_avatar . '" alt="" /><br /><input type="checkbox" name="avatardel" /> 删除头像<br />' : '';
				break;
			case USER_AVATAR_REMOTE:
				$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $user_avatar . '" alt="" /><br /><input type="checkbox" name="avatardel" />  删除头像<br />' : '';
				break;
			/**
			*case USER_AVATAR_GALLERY:
			*	$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $user_avatar . '" alt="" /><br/><input type="checkbox" name="avatardel" /> '.$lang['Delete_Image'].'<br/>' : '';
			*	break;
			**/
		}
	}

	$qq_login = '';
	if ($userdata['qq_openid'] != '')
	{
		$u_unbind_qq_login 	= append_sid('loading.php?mod=qqlogin&amp;load=unbind');
		$qq_login 				= '已绑定（<a href="' . $u_unbind_qq_login . '">解绑</a>）';
	}
	else
	{
		$u_bind_qq_login 		= append_sid('loading.php?mod=qqlogin&amp;load=bind');
		$qq_login 				= '未绑定（<a href="' . $u_bind_qq_login . '">绑定</a>）';
	}	

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';
	$s_hidden_fields .= '<input type="hidden" name="agreed" value="true" />';
	$s_hidden_fields .= '<input type="hidden" name="coppa" value="' . $coppa . '" />';
	$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
	$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';

	if ( !empty($user_avatar_local) )
	{
		$s_hidden_fields .= '<input type="hidden" name="avatarlocal" value="' . $user_avatar_local . '" />';
		$s_hidden_fields .= '<input type="hidden" name="avatarcatname" value="' . $user_avatar_category . '" />';
	}


	if ( $error )
	{
		error_box('ERROR_BOX', $error_msg);
	}

	$template->set_filenames(array(
		'body' => 'ucp/edit_config.tpl')
	);

	$form_enctype = ( !$board_config['allow_avatar_upload'] ) ? '' : 'enctype="multipart/form-data"';

	$template->assign_vars(array(
		'U_PROFILE'					=> append_sid('ucp.php?mode=viewprofile&amp;u=' . $userdata['user_id']),
		
		'VIEW_EMAIL_YES' 			=> ( $viewemail ) ? 'checked="checked"' : '',
		'VIEW_EMAIL_NO' 			=> ( !$viewemail ) ? 'checked="checked"' : '',
		
		'HIDE_USER_YES' 			=> ( !$allowviewonline ) ? 'checked="checked"' : '',
		'HIDE_USER_NO' 				=> ( $allowviewonline ) ? 'checked="checked"' : '',
		
		'NOTIFY_PM_YES' 			=> ( $notifypm ) ? 'checked="checked"' : '',
		'NOTIFY_PM_NO' 				=> ( !$notifypm ) ? 'checked="checked"' : '',
		
		'POPUP_PM_YES' 				=> ( $popup_pm ) ? 'checked="checked"' : '',
		'POPUP_PM_NO' 				=> ( !$popup_pm ) ? 'checked="checked"' : '',

		'GB_CAN_YES' 				=> ( $user_can_gb ) ? 'checked="checked"' : '',
		'GB_CAN_NO' 				=> ( !$user_can_gb ) ? 'checked="checked"' : '',
		
		'ALWAYS_ADD_SIGNATURE_YES' 	=> ( $attachsig ) ? 'checked="checked"' : '',
		'ALWAYS_ADD_SIGNATURE_NO' 	=> ( !$attachsig ) ? 'checked="checked"' : '',
		
		'NOTIFY_REPLY_TO_EMAIL' 	=> ( $notifyreply_to_email ) ? ' checked="checked"' : '',
		'NOTIFY_REPLY_TO_PM'		=> ( $notifyreply_to_pm ) ? ' checked="checked"' : '',
		
		'ALWAYS_ALLOW_BBCODE_YES' 	=> ( $allowbbcode ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_BBCODE_NO' 	=> ( !$allowbbcode ) ? 'checked="checked"' : '',
		
		'ALWAYS_ALLOW_HTML_YES' 	=> ( $allowhtml ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_HTML_NO' 		=> ( !$allowhtml ) ? 'checked="checked"' : '',
		
		'ALWAYS_ALLOW_SMILIES_YES' 	=> ( $allowsmilies ) ? 'checked="checked"' : '',
		'ALWAYS_ALLOW_SMILIES_NO' 	=> ( !$allowsmilies ) ? 'checked="checked"' : '',
		
		'ON_OFF_YES' 				=> ( $userdata['user_on_off'] ) ? 'checked="checked"' : '',
		'ON_OFF_NO' 				=> ( !$userdata['user_on_off'] ) ? 'checked="checked"' : '',
		
		'ATTACH_ON_NO' 				=> ( !$userdata['user_attach_mod'] ) ? 'checked="checked"' : '',
		'ATTACH_ON_YES' 			=> ( $userdata['user_attach_mod'] ) ? 'checked="checked"' : '',
		
		'BB_PANEL_NO' 				=> ( !$userdata['user_bb_panel'] ) ? 'checked="checked"' : '',
		'BB_PANEL_YES' 				=> ( $userdata['user_bb_panel'] ) ? 'checked="checked"' : '',
		
		'JAVA_OTV_NO'			 	=> ( !$userdata['user_java_otv'] ) ? 'checked="checked"' : '',
		'JAVA_OTV_YES' 				=> ( $userdata['user_java_otv'] ) ? 'checked="checked"' : '',
		
		'MESSAGE_QUOTE_NO' 			=> ( !$userdata['user_message_quote'] ) ? 'checked="checked"' : '',
		'MESSAGE_QUOTE_YES' 		=> ( $userdata['user_message_quote'] ) ? 'checked="checked"' : '',
		
		'VIEW_LATEST_POST_NO' 		=> ( !$userdata['user_view_latest_post'] ) ? 'checked="checked"' : '',
		'VIEW_LATEST_POST_YES'	 	=> ( $userdata['user_view_latest_post'] ) ? 'checked="checked"' : '',
		
		'QUICK_ANSWER_NO' 			=> ( !$userdata['user_quick_answer'] ) ? 'checked="checked"' : '',
		'QUICK_ANSWER_YES' 			=> ( $userdata['user_quick_answer'] ) ? 'checked="checked"' : '',
		
		'INDEX_SPISOK_NO' 			=> ( !$userdata['user_index_spisok'] ) ? 'checked="checked"' : '',
		'INDEX_SPISOK_YES' 			=> ( $userdata['user_index_spisok'] ) ? 'checked="checked"' : '',
		
		'POSL_RED_NO' 				=> ( !$userdata['user_posl_red'] ) ? 'checked="checked"' : '',
		'POSL_RED_YES' 				=> ( $userdata['user_posl_red'] ) ? 'checked="checked"' : '',
		
		'ALLOW_AVATAR' 				=> $board_config['allow_avatar_upload'],
		'AVATAR' 					=> $avatar_img,
		'AVATAR_SIZE' 				=> $board_config['avatar_filesize'],
		'TIMEZONE_SELECT' 			=> tz_select($user_timezone, 'timezone'),
		'STYLE_SELECT'				=> style_select($user_style, $style->data, 'style'),
		'DATE_FORMAT' 				=> select_dateformat($user_dateformat, 'dateformat'),

		'TOPICS_PER_PAGE' 			=> $user_topics_per_page,
		'POSTS_PER_PAGE' 			=> $user_posts_per_page,
		'POST_LENG' 				=> $post_leng,
		'QQ_LOGIN'					=> $qq_login,
		'L_AVATAR_EXPLAIN' 			=> '像素不能大于' . $board_config['avatar_max_width'] . '(宽) x ' . $board_config['avatar_max_height'] . '(高) 和大小不能超过 ' . round($board_config['avatar_filesize'] / 1024) . ' KB',
		'S_ALLOW_AVATAR_UPLOAD' 	=> $board_config['allow_avatar_upload'],
		'S_ALLOW_AVATAR_REMOTE' 	=> $board_config['allow_avatar_remote'],
		'S_HIDDEN_FIELDS' 			=> $s_hidden_fields,
		'S_FORM_ENCTYPE' 			=> $form_enctype,
		'S_PROFILE_ACTION' 			=> append_sid("ucp.php"))
	);

	if ( $board_config['message_quote'] )
	{
		$template->assign_block_vars('switch_message_quote', array());
	}

	if ( $userdata['user_allowavatar'] && ( $board_config['allow_avatar_upload'] || $board_config['allow_avatar_remote'] ) )
	{
		$template->assign_block_vars('switch_avatar_block', array() );

		if ( $board_config['allow_avatar_upload'] && file_exists(@phpbb_realpath('./' . $board_config['avatar_path'])) )
		{
			$template->assign_block_vars('switch_avatar_block.switch_avatar_local_upload', array() );
			$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_upload', array() );
		}

		if ( $board_config['allow_avatar_remote'] )
		{
			$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_link', array() );
		}

	}
}

$template->pparse('body');

page_footer();
?>