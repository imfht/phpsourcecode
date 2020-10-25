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
	$file = basename(__FILE__);
	$module['系统']['配置'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');
require(ROOT_PATH . 'includes/functions/selects.php');

$sql = 'SELECT *
	FROM ' . CONFIG_TABLE;
	
if(!$result = $db->sql_query($sql))
{
	trigger_error('Could not query config information in admin_board', E_USER_WARNING);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($_POST['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($_POST[$config_name]) ) ? $_POST[$config_name] : $default_config[$config_name];
		
		if ($config_name == 'cookie_name')
		{
			$new['cookie_name'] = str_replace('.', '_', $new['cookie_name']);
		}

		if ($config_name == 'server_name')
		{
			$new['server_name'] = str_replace('http://', '', $new['server_name']);
		}

		if ($config_name == 'avatar_path')
		{
			$new['avatar_path'] = trim($new['avatar_path']);
			if (strstr($new['avatar_path'], "\0") || !is_dir(ROOT_PATH . $new['avatar_path']) || !is_writable(ROOT_PATH . $new['avatar_path']))
			{
				$new['avatar_path'] = $default_config['avatar_path'];
			}
		}

		if ($config_name == 'default_qq')
		{
			if (!preg_match('/^[0-9]+$/', $new['default_qq']))
			{
				$new['default_qq'] = '';
			}
		}

		if ($config_name == 'open_rewrite')
		{
			if ($new['open_rewrite'])
			{
				if ($handle = @fopen(ROOT_PATH . '.htaccess', 'wb'))
				{
					//锁定
					@flock($handle, LOCK_EX);
					fwrite($handle, '<IfModule mod_rewrite.c>');
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteEngine on'); 
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteRule viewpost-([0-9]{0,7})-([0-9]{0,7}).html$ viewtopic.php?p=$1#=$2'); 
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteRule viewposts-([0-9]{0,7}).html$ viewtopic.php?p=$1'); 
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteRule topic-([0-9]{0,7}).html$ viewtopic.php?t=$1'); 
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteRule forum-([0-9]{0,7}).html$ viewforum.php?f=$1'); 
					fwrite($handle, "\n");
					fwrite($handle, 'RewriteRule forum-cat-[0-9]{0,7}).html$ forum.php?c=$1');
					fwrite($handle, "\n");
					fwrite($handle, '</IfModule>');  

					//释放锁定
					@flock($handle, LOCK_UN);
					
					fclose($handle);
				}
			}
			else
			{
				if(file_exists(ROOT_PATH . '.htaccess'))
				{
					unlink(ROOT_PATH . '.htaccess');
				}
			}
		}

		if( isset($_POST['submit']) )
		{
			set_config($config_name, $new[$config_name]);
		}
	}

	if( isset($_POST['submit']) )
	{
		$cache->clear('global_config');
		
		$message = '设置更新成功！<br />点击 <a href="' . append_sid('admin_board.php') .'">这里</a> 返回系统配置管理<br />点击 <a href="' . append_sid('index.php?pane=left') . '">这里</a> 返回网站设置导航';

		trigger_error($message);
	}
}

$timezone_select 		= tz_select($new['board_timezone'], 'board_timezone');

$cookie_secure_yes 		= ( $new['cookie_secure'] ) ? 'checked="checked"' : '';
$cookie_secure_no 		= ( !$new['cookie_secure'] ) ? 'checked="checked"' : '';

$disable_board_yes 		= ( $new['board_disable'] ) ? 'checked="checked"' : '';
$disable_board_no 		= ( !$new['board_disable'] ) ? 'checked="checked"' : '';

$html_tags 				= $new['allow_html_tags'];
$html_yes 				= ( $new['allow_html'] ) ? 'checked="checked"' : '';
$html_no 				= ( !$new['allow_html'] ) ? 'checked="checked"' : '';

$bbcode_yes 			= ( $new['allow_bbcode'] ) ? 'checked="checked"' : '';
$bbcode_no 				= ( !$new['allow_bbcode'] ) ? 'checked="checked"' : '';

$activation_none 		= ( $new['require_activation'] == USER_ACTIVATION_NONE ) ? 'checked="checked"' : '';
$activation_user 		= ( $new['require_activation'] == USER_ACTIVATION_SELF ) ? 'checked="checked"' : '';
$activation_admin 		= ( $new['require_activation'] == USER_ACTIVATION_ADMIN ) ? 'checked="checked"' : '';

$confirm_yes 			= ($new['enable_confirm']) ? 'checked="checked"' : '';
$confirm_no 			= (!$new['enable_confirm']) ? 'checked="checked"' : '';

$message_quote_yes 		= ($new['message_quote']) ? 'checked="checked"' : '';
$message_quote_no 		= (!$new['message_quote']) ? 'checked="checked"' : '';

$quick_answer_off 		= ($new['quick_answer'] == QUICK_ANSWER_OFF) ? 'checked="checked"' : '';
$quick_answer_on 		= ($new['quick_answer'] == QUICK_ANSWER_ON) ? 'checked="checked"' : '';
$quick_answer_user 		= ($new['quick_answer'] == QUICK_ANSWER_USER) ? 'checked="checked"' : '';

$spisok_yes 			= ($new['index_spisok']) ? 'checked="checked"' : '';
$spisok_no 				= (!$new['index_spisok']) ? 'checked="checked"' : '';

$captcha_in_topic_yes 	= ($new['captcha_in_topic']) ? 'checked="checked"' : '';
$captcha_in_topic_no 	= (!$new['captcha_in_topic']) ? 'checked="checked"' : '';

$posl_yes 				= ($new['posl_red']) ? 'checked="checked"' : '';
$posl_no 				= (!$new['posl_red']) ? 'checked="checked"' : '';

$allow_autologin_yes 	= ($new['allow_autologin']) ? 'checked="checked"' : '';
$allow_autologin_no 	= (!$new['allow_autologin']) ? 'checked="checked"' : '';

$board_email_form_yes 	= ( $new['board_email_form'] ) ? 'checked="checked"' : '';
$board_email_form_no 	= ( !$new['board_email_form'] ) ? 'checked="checked"' : '';

$privmsg_on 			= ( !$new['privmsg_disable'] ) ? 'checked="checked"' : '';
$privmsg_off 			= ( $new['privmsg_disable'] ) ? 'checked="checked"' : '';

$prune_yes 				= ( $new['prune_enable'] ) ? 'checked="checked"' : '';
$prune_no 				= ( !$new['prune_enable'] ) ? 'checked="checked"' : '';

$birthday_greeting_yes 	= ( $new['birthday_greeting'] ) ? 'checked="checked"' : '';
$birthday_greeting_no 	= ( !$new['birthday_greeting'] ) ? 'checked="checked"' : '';

$birthday_look_yes 		= ( $new['birthday_check_day'] ) ? 'checked="checked"' : '';
$birthday_look_no 		= ( !$new['birthday_check_day'] ) ? 'checked="checked"' : '';

$smile_yes 				= ( $new['allow_smilies'] ) ? 'checked="checked"' : '';
$smile_no 				= ( !$new['allow_smilies'] ) ? 'checked="checked"' : '';

$sig_yes 				= ( $new['allow_sig'] ) ? 'checked="checked"' : '';
$sig_no 				= ( !$new['allow_sig'] ) ? 'checked="checked"' : '';

$gb_guest_yes 			= ( $new['allow_guests_gb'] ) ? "checked=\"checked\"" : "";
$gb_guest_no 			= ( !$new['allow_guests_gb'] ) ? "checked=\"checked\"" : "";

$gb_quick_yes 			= ( $new['gb_quick'] ) ? "checked=\"checked\"" : "";
$gb_quick_no 			= ( !$new['gb_quick'] ) ? "checked=\"checked\"" : "";

$namechange_yes 		= ( $new['allow_namechange'] ) ? 'checked="checked"' : '';
$namechange_no 			= ( !$new['allow_namechange'] ) ? 'checked="checked"' : '';

$avatars_remote_yes 	= ( $new['allow_avatar_remote'] ) ? 'checked="checked"' : '';
$avatars_remote_no 		= ( !$new['allow_avatar_remote'] ) ? 'checked="checked"' : '';

$avatars_upload_yes		= ( $new['allow_avatar_upload'] ) ? 'checked="checked"' : '';
$avatars_upload_no 		= ( !$new['allow_avatar_upload'] ) ? 'checked="checked"' : '';

$smtp_yes 				= ( $new['smtp_delivery'] ) ? 'checked="checked"' : '';
$smtp_no 				= ( !$new['smtp_delivery'] ) ? 'checked="checked"' : '';

$rewrite_yes 			= ( $new['open_rewrite'] ) ? 'checked="checked"' : '';
$rewrite_no 			= ( !$new['open_rewrite'] ) ? 'checked="checked"' : '';

$template->set_filenames(array(
	'body' => 'admin/board_config_body.tpl')
);

$new['site_desc'] 	= str_replace('"', '&quot;', $new['site_desc']);
$new['sitename'] 	= str_replace('"', '&quot;', strip_tags($new['sitename']));

$template->assign_vars(array(
	'S_CONFIG_ACTION' 				=> append_sid('admin_board.php'),

	'ONLINE_TIME' 					=> $new['online_time'],
	'MIN_LOGIN_REGDATE' 			=> $new['min_login_regdate'],
	
	'SERVER_NAME' 					=> $new['server_name'], 
	'SCRIPT_PATH' 					=> $new['script_path'], 
	'SERVER_PORT' 					=> $new['server_port'], 
	'SITENAME' 						=> $new['sitename'],
	'SITE_DESCRIPTION' 				=> $new['site_desc'],
	'BEIAN_INFO'					=> $new['beian_info'],
	'S_DISABLE_BOARD_YES' 			=> $disable_board_yes,
	'S_DISABLE_BOARD_NO' 			=> $disable_board_no,
	'ACTIVATION_NONE' 				=> USER_ACTIVATION_NONE, 
	'ACTIVATION_NONE_CHECKED' 		=> $activation_none,
	'ACTIVATION_USER' 				=> USER_ACTIVATION_SELF, 
	'ACTIVATION_USER_CHECKED' 		=> $activation_user,
	'ACTIVATION_ADMIN' 				=> USER_ACTIVATION_ADMIN, 
	'ACTIVATION_ADMIN_CHECKED' 		=> $activation_admin, 
	'CONFIRM_ENABLE' 				=> $confirm_yes,
	'CONFIRM_DISABLE' 				=> $confirm_no,
	'ALLOW_AUTOLOGIN_YES' 			=> $allow_autologin_yes,
	'ALLOW_AUTOLOGIN_NO' 			=> $allow_autologin_no,
	'AUTOLOGIN_TIME' 				=> (int) $new['max_autologin_time'],
	'BOARD_EMAIL_FORM_ENABLE' 		=> $board_email_form_yes, 
	'BOARD_EMAIL_FORM_DISABLE' 		=> $board_email_form_no, 
	'MAX_POLL_OPTIONS' 				=> $new['max_poll_options'], 
	'FLOOD_INTERVAL' 				=> $new['flood_interval'],
	'SEARCH_FLOOD_INTERVAL' 		=> $new['search_flood_interval'],
	'TOPICS_PER_PAGE' 				=> $new['topics_per_page'],
	'POSTS_PER_PAGE' 				=> $new['posts_per_page'],
	
	'MAX_USER_TOPICS_PER_PAGE' 		=> $new['max_user_topics_per_page'],
	'MAX_USER_POSTS_PER_PAGE' 		=> $new['max_user_posts_per_page'],
	'DEFAULT_DATEFORMAT' 			=> $new['default_dateformat'],
	'TIMEZONE_SELECT' 				=> $timezone_select,
	'S_PRIVMSG_ENABLED' 			=> $privmsg_on, 
	'S_PRIVMSG_DISABLED' 			=> $privmsg_off, 
	'INBOX_LIMIT' 					=> $new['max_inbox_privmsgs'], 
	'SENTBOX_LIMIT' 				=> $new['max_sentbox_privmsgs'],
	'SAVEBOX_LIMIT' 				=> $new['max_savebox_privmsgs'],
	'MAX_SMILES' 					=> $new['max_smiles_in_message'],
	'COOKIE_DOMAIN' 				=> $new['cookie_domain'], 
	'COOKIE_NAME' 					=> $new['cookie_name'], 
	'COOKIE_PATH' 					=> $new['cookie_path'], 
	'SESSION_LENGTH' 				=> $new['session_length'], 
	'S_COOKIE_SECURE_ENABLED' 		=> $cookie_secure_yes, 
	'S_COOKIE_SECURE_DISABLED' 		=> $cookie_secure_no, 
	'PRUNE_YES' 					=> $prune_yes,
	'PRUNE_NO' 						=> $prune_no, 
	'CAPTCHA_IN_TOPIC_YES' 			=> $captcha_in_topic_yes,
	'CAPTCHA_IN_TOPIC_NO' 			=> $captcha_in_topic_no, 
	'BIRTHDAY_GREETING_YES' 		=> $birthday_greeting_yes,
	'BIRTHDAY_GREETING_NO' 			=> $birthday_greeting_no,
	'MAX_USER_AGE' 					=> $new['max_user_age'],
	'MIN_USER_AGE' 					=> $new['min_user_age'],
	'INDEX_ANNOUNCEMENT' 			=> $new['index_announcement'],
	'BIRTHDAY_LOOK_YES' 			=> $birthday_look_yes,
	'BIRTHDAY_LOOK_NO' 				=> $birthday_look_no,

	'HTML_TAGS' 					=> $html_tags, 
	'BBCODE_YES' 					=> $bbcode_yes,
	'BBCODE_NO' 					=> $bbcode_no,
	'SMILE_YES' 					=> $smile_yes,
	'SMILE_NO' 						=> $smile_no,
	'SIG_YES' 						=> $sig_yes,
	'SIG_NO' 						=> $sig_no,

	"NO_GUEST_YES" 					=> $gb_guest_yes,
	"NO_GUEST_NO" 					=> $gb_guest_no,
	"GB_QUICK_YES" 					=> $gb_quick_yes,
	"GB_QUICK_NO" 					=> $gb_quick_no,
	"GB_CASH2" 						=> $new['gb_cash2'],
	"GB_POST" 						=> $new['gb_posts'],
	
	'SPISOK_YES' 					=> $spisok_yes,
	'SPISOK_NO' 					=> $spisok_no,
	'POSL_YES' 						=> $posl_yes,
	'POSL_NO' 						=> $posl_no,
	'MESSAGE_QUOTE_YES' 			=> $message_quote_yes,
	'MESSAGE_QUOTE_NO' 				=> $message_quote_no,
	'QUICK_ANSWER_OFF'				=> $quick_answer_off,
	'QUICK_ANSWER_ON'				=> $quick_answer_on,
	'QUICK_ANSWER_USER'				=> $quick_answer_user,
	'SIG_SIZE' 						=> $new['max_sig_chars'], 
	'NAMECHANGE_YES' 				=> $namechange_yes,
	'NAMECHANGE_NO' 				=> $namechange_no,
	'AVATARS_REMOTE_YES' 			=> $avatars_remote_yes,
	'AVATARS_REMOTE_NO' 			=> $avatars_remote_no,
	'AVATARS_UPLOAD_YES' 			=> $avatars_upload_yes,
	'AVATARS_UPLOAD_NO' 			=> $avatars_upload_no,
	'AVATAR_FILESIZE' 				=> $new['avatar_filesize'],
	'AVATAR_MAX_HEIGHT' 			=> $new['avatar_max_height'],
	'AVATAR_MAX_WIDTH' 				=> $new['avatar_max_width'],
	'AVATAR_PATH' 					=> $new['avatar_path'], 
	'SMILIES_PATH' 					=> $new['smilies_path'], 
	'INBOX_PRIVMSGS' 				=> $new['max_inbox_privmsgs'], 
	'SENTBOX_PRIVMSGS' 				=> $new['max_sentbox_privmsgs'], 
	'SAVEBOX_PRIVMSGS' 				=> $new['max_savebox_privmsgs'], 
	'EMAIL_FROM' 					=> $new['board_email'],
	'EMAIL_SIG' 					=> $new['board_email_sig'],
	'SMTP_YES' 						=> $smtp_yes,
	'SMTP_NO' 						=> $smtp_no,
	'REWRITE_YES' 					=> $rewrite_yes,
	'REWRITE_NO' 					=> $rewrite_no,
	'SMTP_HOST' 					=> $new['smtp_host'],
	'SMTP_USERNAME' 				=> $new['smtp_username'],
	'SMTP_PASSWORD' 				=> $new['smtp_password'])
);

$template->pparse('body');

page_footer();

?>