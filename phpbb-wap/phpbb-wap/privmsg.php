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

require(ROOT_PATH . 'common.php');
require(ROOT_PATH . 'includes/functions/bbcode.php');
require(ROOT_PATH . 'includes/functions/post.php');

if ( !empty($board_config['privmsg_disable']) )
{
	trigger_error('超级管理员没有开放信息功能，如需开启请联系超级管理员！', E_USER_ERROR);
}

$html_entities_match 	= array('#&(?!(\#[0-9]+;))#', '#<#', '#>#', '#"#');
$html_entities_replace 	= array('&amp;', '&lt;', '&gt;', '&quot;');
$submit 				= ( isset($_POST['post']) ) ? TRUE : 0;
$submit_search 			= ( isset($_POST['usersubmit']) ) ? TRUE : 0; 
$submit_msgdays 		= ( isset($_POST['submit_msgdays']) ) ? TRUE : 0;
$cancel 				= ( isset($_POST['cancel']) ) ? TRUE : 0;
$preview 				= ( isset($_POST['preview']) ) ? TRUE : 0;
$confirm 				= ( isset($_POST['confirm']) ) ? TRUE : 0;
$delete 				= ( isset($_POST['delete']) ) ? TRUE : 0;
$delete_all 			= ( isset($_POST['deleteall']) ) ? TRUE : 0;
$save 					= ( isset($_POST['save']) ) ? TRUE : 0;
$sid 					= (isset($_POST['sid'])) ? $_POST['sid'] : 0;
$refresh 				= $preview || $submit_search;
$mark_list 				= ( !empty($_POST['mark']) ) ? $_POST['mark'] : 0;

if ( isset($_POST['folder']) || isset($_GET['folder']) )
{
	$folder = ( isset($_POST['folder']) ) ? $_POST['folder'] : $_GET['folder'];
	$folder = htmlspecialchars($folder);

	if ( $folder != 'inbox' && $folder != 'outbox' && $folder != 'sentbox' && $folder != 'savebox' )
	{
		$folder = 'inbox';
	}
}
else
{
	$folder = 'inbox';
}

$userdata = $session->start($user_ip, PAGE_PRIVMSGS);
init_userprefs($userdata);

if ( $cancel )
{
	redirect(append_sid('privmsg.php?folder=' . $folder, true));
}

if ( !empty($_POST['mode']) || !empty($_GET['mode']) )
{
	$mode = ( !empty($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

//获取 $start
$start = get_pagination_start($board_config['posts_per_page']);

if ( isset($_POST[POST_POST_URL]) || isset($_GET[POST_POST_URL]) )
{
	$privmsg_id = ( isset($_POST[POST_POST_URL]) ) ? intval($_POST[POST_POST_URL]) : intval($_GET[POST_POST_URL]);
}
else
{
	$privmsg_id = '';
}

$error 			= FALSE;
//收信箱
$inbox_img 		= ( $folder != 'inbox' || $mode != '' ) ? make_style_image('privmsg_inbox') : '';
$inbox_url 		= ( $folder != 'inbox' || $mode != '' ) ? '&nbsp;<a href="' . append_sid("privmsg.php?folder=inbox") . '">收信箱</a>&nbsp;' : '';

//发件箱
$outbox_img 	= ( $folder != 'outbox' || $mode != '' ) ? make_style_image('privmsg_outbox') : '';
$outbox_url 	= ( $folder != 'outbox' || $mode != '' ) ? '&nbsp;<a href="' . append_sid("privmsg.php?folder=outbox") . '">发件箱</a>&nbsp;' : '';

//已发送
$sentbox_img 	= ( $folder != 'sentbox' || $mode != '' ) ? make_style_image('privmsg_sentbox') : '';
$sentbox_url 	= ( $folder != 'sentbox' || $mode != '' ) ? '&nbsp;<a href="' . append_sid("privmsg.php?folder=sentbox") . '">已发送</a>&nbsp;' : '';

//草稿
$savebox_img 	= ( $folder != 'savebox' || $mode != '' ) ? make_style_image('privmsg_savebox') : '';
$savebox_url 	= ( $folder != 'savebox' || $mode != '' ) ? '&nbsp;<a href="' . append_sid("privmsg.php?folder=savebox") . '">草稿箱</a>&nbsp;' : '';

if ( $mode == 'read' )
{
	if ( !empty($_GET[POST_POST_URL]) )
	{
		$privmsgs_id = intval($_GET[POST_POST_URL]);
	}
	else
	{
		trigger_error('对不起，您输入的网址错误，请重新载入页面！', E_USER_ERROR);
	}

	if ( !$userdata['session_logged_in'] )
	{
		login_back("privmsg.php?folder=$folder&mode=$mode&" . POST_POST_URL . "=$privmsgs_id");
	}

	switch( $folder )
	{
		case 'inbox':
			$l_box_name = '收信箱';
			$pm_sql_user = "AND pm.privmsgs_to_userid = " . $userdata['user_id'] . " 
				AND ( pm.privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
					OR pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
					OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
			break;
		case 'outbox':
			$l_box_name = '发件箱';
			$pm_sql_user = "AND pm.privmsgs_from_userid =  " . $userdata['user_id'] . " 
				AND ( pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
					OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) ";
			break;
		case 'sentbox':
			$l_box_name = '已发送';
			$pm_sql_user = "AND pm.privmsgs_from_userid =  " . $userdata['user_id'] . " 
				AND pm.privmsgs_type = " . PRIVMSGS_SENT_MAIL;
			break;
		case 'savebox':
			$l_box_name = '草稿箱';
			$pm_sql_user = "AND ( ( pm.privmsgs_to_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) 
				OR ( pm.privmsgs_from_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " ) 
				)";
			break;
		default:
			trigger_error('没有这样的文件夹', E_USER_ERROR);
			break;
	}

	$sql = 'SELECT u.username AS username_1, u.user_id AS user_id_1, u2.username AS username_2, u2.user_id AS user_id_2, pm.*, pmt.privmsgs_text
		FROM ' . PRIVMSGS_TABLE . ' pm, ' . PRIVMSGS_TEXT_TABLE . ' pmt, ' . USERS_TABLE . ' u, ' . USERS_TABLE . " u2 
		WHERE pm.privmsgs_id = $privmsgs_id
			AND pmt.privmsgs_text_id = pm.privmsgs_id 
			$pm_sql_user 
			AND u.user_id = pm.privmsgs_from_userid 
			AND u2.user_id = pm.privmsgs_to_userid";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法查询信息内容', E_USER_WARNING);
	}

	if ( !($privmsg = $db->sql_fetchrow($result)) )
	{
		redirect(append_sid("privmsg.php?folder=$folder", true));
	}

	$privmsg_id = $privmsg['privmsgs_id'];

	if (($privmsg['privmsgs_type'] == PRIVMSGS_NEW_MAIL || $privmsg['privmsgs_type'] == PRIVMSGS_UNREAD_MAIL) && $folder == 'inbox')
	{
		switch ($privmsg['privmsgs_type'])
		{
			case PRIVMSGS_NEW_MAIL:
				$sql = "user_new_privmsg = user_new_privmsg - 1";
				break;
			case PRIVMSGS_UNREAD_MAIL:
				$sql = "user_unread_privmsg = user_unread_privmsg - 1";
				break;
		}

		$sql = 'UPDATE ' . USERS_TABLE . " 
			SET $sql 
			WHERE user_id = " . $userdata['user_id'];
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法更新用户表信息数', E_USER_WARNING);
		}

		$sql = 'UPDATE ' . PRIVMSGS_TABLE . '
			SET privmsgs_type = ' . PRIVMSGS_READ_MAIL . '
			WHERE privmsgs_id = ' . $privmsg['privmsgs_id'];
		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法更新信息表', E_USER_WARNING);
		}

		$sql = "SELECT COUNT(privmsgs_id) AS sent_items, MIN(privmsgs_date) AS oldest_post_time 
			FROM " . PRIVMSGS_TABLE . " 
			WHERE privmsgs_type = " . PRIVMSGS_SENT_MAIL . " 
				AND privmsgs_from_userid = " . $privmsg['privmsgs_from_userid'];
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain sent message info for sendee', E_USER_WARNING);
		}

		if ( $sent_info = $db->sql_fetchrow($result) )
		{
			if ($board_config['max_sentbox_privmsgs'] && $sent_info['sent_items'] >= $board_config['max_sentbox_privmsgs'])
			{
				$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . " 
					WHERE privmsgs_type = " . PRIVMSGS_SENT_MAIL . " 
						AND privmsgs_date = " . $sent_info['oldest_post_time'] . " 
						AND privmsgs_from_userid = " . $privmsg['privmsgs_from_userid'];
				if ( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not find oldest privmsgs', E_USER_WARNING);
				}
				$old_privmsgs_id = $db->sql_fetchrow($result);
				$old_privmsgs_id = $old_privmsgs_id['privmsgs_id'];
			
				$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . " 
					WHERE privmsgs_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete oldest privmsgs (sent)', E_USER_WARNING);
				}

				$sql = 'DELETE FROM ' . PRIVMSGS_TEXT_TABLE . " 
					WHERE privmsgs_text_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete oldest privmsgs text (sent)', E_USER_WARNING);
				}
			}
		}

		$sql = 'INSERT INTO ' . PRIVMSGS_TABLE . ' (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip)
			VALUES (' . PRIVMSGS_SENT_MAIL . ", '" . str_replace("\'", "''", addslashes($privmsg['privmsgs_subject'])) . "', " . $privmsg['privmsgs_from_userid'] . ", " . $privmsg['privmsgs_to_userid'] . ", " . $privmsg['privmsgs_date'] . ", '" . $privmsg['privmsgs_ip'] . "')";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not insert private message sent info', E_USER_WARNING);
		}

		$privmsg_sent_id = $db->sql_nextid();

		$sql = 'INSERT INTO ' . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text)
			VALUES ($privmsg_sent_id, '" . str_replace("\'", "''", addslashes($privmsg['privmsgs_text'])) . "')";
		if ( !$db->sql_query($sql) )
		{
			trigger_error('Could not insert private message sent text', E_USER_WARNING);
		}
	}

	$pm = array(
		'post' 	=> '<a href="' . append_sid("privmsg.php?mode=post") . '" class="button">&nbsp;&nbsp;发信息&nbsp;&nbsp;</a>',
		'reply' => '<a href="' . append_sid("privmsg.php?mode=reply&amp;" . POST_POST_URL . "=$privmsg_id") . '" class="button">&nbsp;&nbsp;回复信息&nbsp;&nbsp;</a>',
		'quote' => '<a href="' . append_sid("privmsg.php?mode=quote&amp;" . POST_POST_URL . "=$privmsg_id") . '">引用信息</a>',
		'edit' 	=> '<a href="' . append_sid("privmsg.php?mode=edit&amp;" . POST_POST_URL . "=$privmsg_id") . '">编辑信息</a>'
	);

	if ( $folder == 'inbox' )
	{
		$post 		= $pm['post'];
		$reply 		= $pm['reply'];
		$quote 		= $pm['quote'];
		$edit 		= '';
		$l_box_name = '收信箱';
	}
	else if ( $folder == 'outbox' )
	{
		$post 		= $pm['post'];
		$reply 		= '';
		$quote 		= '';
		$edit 		= $pm['edit'];
		$l_box_name = '发件箱';
	}
	else if ( $folder == 'savebox' )
	{
		if ( $privmsg['privmsgs_type'] == PRIVMSGS_SAVED_IN_MAIL )
		{
			$post 		= $pm['post'];
			$reply 		= $pm['reply'];
			$quote 		= $pm['quote'];
			$edit 		= '';
		}
		else
		{
			$post 		= $pm['post'];
			$reply 		= '';
			$quote 		= '';
			$edit 		= '';
		}
		$l_box_name 	= '草稿箱';
	}
	else if ( $folder == 'sentbox' )
	{
		$post 			= $pm['post'];
		$reply 			= '';
		$quote 			= '';
		$edit 			= '';
		$l_box_name 	= '已发送';
	}
	
	$username_from 				= $privmsg['username_1'];
	$user_id_from				= $privmsg['user_id_1'];
	$username_to 				= $privmsg['username_2'];
	$user_id_to 				= $privmsg['user_id_2'];
	
	$post_date 					= create_date($board_config['default_dateformat'], $privmsg['privmsgs_date'], $board_config['board_timezone']);
	$temp_url 					= append_sid("privmsg.php?mode=post&amp;" . POST_USERS_URL . "=$user_id_from");
	$pm 						= '<a href="' . $temp_url . '">发信息</a>';

	$post_subject 				= $privmsg['privmsgs_subject'];
	$private_message 			= $privmsg['privmsgs_text'];
	
	$page_title = '阅读信息';
	page_header($page_title);

	$template->set_filenames(array(
		'body' => 'privmsgs_read_body.tpl')
	);
	
	$template->assign_vars(array(
		'INBOX' 				=> $inbox_url, 
		'U_FROM_USER_PROFILE' 	=> ($privmsg['user_id_1'] == ANONYMOUS) ? '' : append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . "=".$privmsg['user_id_1']),
		
		'POST_PM' 				=> $post, 
		'REPLY_PM' 				=> $reply, 
		'EDIT_PM' 				=> $edit, 
		'QUOTE_PM' 				=> $quote, 

		'SENTBOX' 				=> $sentbox_url, 
		'OUTBOX' 				=> $outbox_url, 
		'SAVEBOX' 				=> $savebox_url, 

		'BOX_NAME' 				=> $l_box_name, 
		
		'U_INBOX'				=> append_sid('privmsg.php?folder=inbox'),
		
		'S_HISTORY' 			=> append_sid("privmsg.php?history&amp;p=$privmsgs_id"),
		'S_PRIVMSGS_ACTION' 	=> append_sid("privmsg.php?folder=$folder"),
		'S_HIDDEN_FIELDS' 		=> '<input type="hidden" name="mark[]" value="' . $privmsgs_id . '" />')
	);

	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);

	if ( count($orig_word) )
	{
		$post_subject = str_replace($orig_word, $replacement_word, $post_subject);
		$private_message = str_replace($orig_word, $replacement_word, $private_message);
	}
	
	if ( $board_config['allow_smilies'] )
	{
		$private_message = smilies_pass($private_message);
	}
	
	$private_message = str_replace(PHP_EOL, '<br />', $private_message);
	
	$template->assign_vars(array(
		'MESSAGE_TO' 	=> $username_to,
		'MESSAGE_FROM' 	=> ($privmsg['user_id_1'] == ANONYMOUS) ? '系统管理员' : $username_from,
		'POST_SUBJECT' 	=> $post_subject,
		'POST_DATE' 	=> $post_date, 
		'MESSAGE' 		=> $private_message)
	);
	$template->pparse('body');
	page_footer();

}
else if ( ( $delete && $mark_list ) || $delete_all )
{
	if ( !$userdata['session_logged_in'] )
	{
		login_back("privmsg.php?folder=inbox");
	}

	if ( isset($mark_list) && !is_array($mark_list) )
	{
		$mark_list = array();
	}

	if ( !$confirm )
	{
		$page_title = '删除全部';
		page_header($page_title);

		$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';
		$s_hidden_fields .= ( isset($_POST['delete']) ) ? '<input type="hidden" name="delete" value="true" />' : '<input type="hidden" name="deleteall" value="true" />';
		$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

		for($i = 0; $i < count($mark_list); $i++)
		{
			$s_hidden_fields .= '<input type="hidden" name="mark[]" value="' . intval($mark_list[$i]) . '" />';
		}

		$template->set_filenames(array(
			'confirm_body' => 'confirm_body.tpl')
		);

		$template->assign_vars(array(
			'MESSAGE_TITLE' 	=> '确认',
			'MESSAGE_TEXT'		=> ( count($mark_list) == 1 ) ? '请确认是否删除这条信息？' : '请确认是否删除这些信息？', 

			'L_YES' 			=> '是',
			'L_NO' 				=> '否',

			'S_CONFIRM_ACTION' 	=> append_sid("privmsg.php?folder=$folder"),
			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
		);

		$template->pparse('confirm_body');

		page_footer();

	}
	else if ($confirm && $sid === $userdata['session_id'])
	{
		$delete_sql_id = '';

		if (!$delete_all)
		{
			for ($i = 0; $i < count($mark_list); $i++)
			{
				$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
			}
			$delete_sql_id = "AND privmsgs_id IN ($delete_sql_id)";
		}

		switch($folder)
		{
			case 'inbox':
				$delete_type = "privmsgs_to_userid = " . $userdata['user_id'] . " AND (
				privmsgs_type = " . PRIVMSGS_READ_MAIL . " OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
				break;

			case 'outbox':
				$delete_type = "privmsgs_from_userid = " . $userdata['user_id'] . " AND ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
				break;

			case 'sentbox':
				$delete_type = "privmsgs_from_userid = " . $userdata['user_id'] . " AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
				break;

			case 'savebox':
				$delete_type = "( ( privmsgs_from_userid = " . $userdata['user_id'] . " 
					AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " ) 
				OR ( privmsgs_to_userid = " . $userdata['user_id'] . " 
					AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) )";
				break;
		}

		$sql = "SELECT privmsgs_id
			FROM " . PRIVMSGS_TABLE . "
			WHERE $delete_type $delete_sql_id";

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain id list to delete messages', E_USER_WARNING);
		}

		$mark_list = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$mark_list[] = $row['privmsgs_id'];
		}

		unset($delete_type);

		if ( count($mark_list) )
		{
			$delete_sql_id = '';
			for ($i = 0; $i < count($mark_list); $i++)
			{
				$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
			}

			if ($folder == 'inbox' || $folder == 'outbox')
			{
				switch ($folder)
				{
					case 'inbox':
						$sql = "privmsgs_to_userid = " . $userdata['user_id'];
						break;
					case 'outbox':
						$sql = "privmsgs_from_userid = " . $userdata['user_id'];
						break;
				}

				$sql = "SELECT privmsgs_to_userid, privmsgs_type 
					FROM " . PRIVMSGS_TABLE . " 
					WHERE privmsgs_id IN ($delete_sql_id) 
						AND $sql  
						AND privmsgs_type IN (" . PRIVMSGS_NEW_MAIL . ", " . PRIVMSGS_UNREAD_MAIL . ")";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not obtain user id list for outbox messages', E_USER_WARNING);
				}

				if ( $row = $db->sql_fetchrow($result))
				{
					$update_users = $update_list = array();
				
					do
					{
						switch ($row['privmsgs_type'])
						{
							case PRIVMSGS_NEW_MAIL:
								$update_users['new'][$row['privmsgs_to_userid']]++;
								break;

							case PRIVMSGS_UNREAD_MAIL:
								$update_users['unread'][$row['privmsgs_to_userid']]++;
								break;
						}
					}
					while ($row = $db->sql_fetchrow($result));

					if (count($update_users))
					{
						foreach($update_users as $type => $users)
						{
							foreach($users as $user_id => $dec)
							{
								$update_list[$type][$dec][] = $user_id;
							}
						}
						unset($update_users);

						foreach($update_list as $type => $dec_ary)
						{
							switch ($type)
							{
								case 'new':
									$type = "user_new_privmsg";
									break;

								case 'unread':
									$type = "user_unread_privmsg";
									break;
							}

							foreach($dec_ary as $dec => $user_ary)
							{
								$user_ids = implode(', ', $user_ary);

								$sql = "UPDATE " . USERS_TABLE . " 
									SET $type = $type - $dec 
									WHERE user_id IN ($user_ids)";
								if ( !$db->sql_query($sql) )
								{
									trigger_error('Could not update user pm counters', E_USER_WARNING);
								}
							}
						}
						unset($update_list);
					}
				}
				$db->sql_freeresult($result);
			}

			$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
				WHERE privmsgs_text_id IN ($delete_sql_id)";
			$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_id IN ($delete_sql_id)
					AND ";

			switch( $folder )
			{
				case 'inbox':
					$delete_sql .= "privmsgs_to_userid = " . $userdata['user_id'] . " AND (
						privmsgs_type = " . PRIVMSGS_READ_MAIL . " OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
					break;

				case 'outbox':
					$delete_sql .= "privmsgs_from_userid = " . $userdata['user_id'] . " AND ( 
						privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
					break;

				case 'sentbox':
					$delete_sql .= "privmsgs_from_userid = " . $userdata['user_id'] . " AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
					break;

				case 'savebox':
					$delete_sql .= "( ( privmsgs_from_userid = " . $userdata['user_id'] . " 
						AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " ) 
					OR ( privmsgs_to_userid = " . $userdata['user_id'] . " 
						AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) )";
					break;
			}

			if ( !$db->sql_query($delete_sql, BEGIN_TRANSACTION) )
			{
				trigger_error('Could not delete private message info', E_USER_WARNING);
			}

			if ( !$db->sql_query($delete_text_sql, END_TRANSACTION) )
			{
				trigger_error('Could not delete private message text', E_USER_WARNING);
			}
		}
	}
}
else if ( $save && $mark_list && $folder != 'savebox' && $folder != 'outbox' )
{
	if ( !$userdata['session_logged_in'] )
	{
		login_back("privmsg.php?folder=inbox");
	}
	
	if (count($mark_list))
	{
		$sql = "SELECT COUNT(privmsgs_id) AS savebox_items, MIN(privmsgs_date) AS oldest_post_time 
			FROM " . PRIVMSGS_TABLE . " 
			WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . " 
					AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
				OR ( privmsgs_from_userid = " . $userdata['user_id'] . " 
					AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") )";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain sent message info for sendee', E_USER_WARNING);
		}

		if ( $saved_info = $db->sql_fetchrow($result) )
		{
			if ($board_config['max_savebox_privmsgs'] && $saved_info['savebox_items'] >= $board_config['max_savebox_privmsgs'] )
			{
				$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . " 
					WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . " 
								AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
							OR ( privmsgs_from_userid = " . $userdata['user_id'] . " 
								AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") ) 
						AND privmsgs_date = " . $saved_info['oldest_post_time'];
				if ( !$result = $db->sql_query($sql) )
				{
					trigger_error('Could not find oldest privmsgs (save)', E_USER_WARNING);
				}
				$old_privmsgs_id = $db->sql_fetchrow($result);
				$old_privmsgs_id = $old_privmsgs_id['privmsgs_id'];
			
				$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . ' 
					WHERE privmsgs_id = ' . $old_privmsgs_id;
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete oldest privmsgs (save)', E_USER_WARNING);
				}

				$sql = 'DELETE FROM ' . PRIVMSGS_TEXT_TABLE . ' 
					WHERE privmsgs_text_id = ' . $old_privmsgs_id;
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete oldest privmsgs text (save)', E_USER_WARNING);
				}
			}
		}
	
		$saved_sql_id = '';
		for ($i = 0; $i < count($mark_list); $i++)
		{
			$saved_sql_id .= (($saved_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
		}

		$saved_sql = "UPDATE " . PRIVMSGS_TABLE;

		if ($folder == 'inbox' || $folder == 'outbox')
		{
			switch ($folder)
			{
				case 'inbox':
					$sql = "privmsgs_to_userid = " . $userdata['user_id'];
					break;
				case 'outbox':
					$sql = "privmsgs_from_userid = " . $userdata['user_id'];
					break;
			}

			$sql = "SELECT privmsgs_to_userid, privmsgs_type 
				FROM " . PRIVMSGS_TABLE . " 
				WHERE privmsgs_id IN ($saved_sql_id) 
					AND $sql  
					AND privmsgs_type IN (" . PRIVMSGS_NEW_MAIL . ", " . PRIVMSGS_UNREAD_MAIL . ")";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain user id list for outbox messages', E_USER_WARNING);
			}

			if ( $row = $db->sql_fetchrow($result))
			{
				$update_users = $update_list = array();
			
				do
				{
					switch ($row['privmsgs_type'])
					{
						case PRIVMSGS_NEW_MAIL:
							$update_users['new'][$row['privmsgs_to_userid']]++;
							break;

						case PRIVMSGS_UNREAD_MAIL:
							$update_users['unread'][$row['privmsgs_to_userid']]++;
							break;
					}
				}
				while ($row = $db->sql_fetchrow($result));

				if (count($update_users))
				{
					foreach($update_users as $type => $users)
					{
						foreach($users as $user_id => $dec)
						{
							$update_list[$type][$dec][] = $user_id;
						}
					}
					unset($update_users);

					foreach($update_list as $type => $dec_ary)
					{
						switch ($type)
						{
							case 'new':
								$type = "user_new_privmsg";
								break;

							case 'unread':
								$type = "user_unread_privmsg";
								break;
						}

						foreach($dec_ary as $dec => $user_ary)
						{
							$user_ids = implode(', ', $user_ary);

							$sql = "UPDATE " . USERS_TABLE . " 
								SET $type = $type - $dec 
								WHERE user_id IN ($user_ids)";
							if ( !$db->sql_query($sql) )
							{
								trigger_error('Could not update user pm counters', E_USER_WARNING);
							}
						}
					}
					unset($update_list);
				}
			}
			$db->sql_freeresult($result);
		}

		switch ($folder)
		{
			case 'inbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " 
					WHERE privmsgs_to_userid = " . $userdata['user_id'] . " 
						AND ( privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
							OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
							OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . ")";
				break;

			case 'outbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " 
					WHERE privmsgs_from_userid = " . $userdata['user_id'] . " 
						AND ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
							OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) ";
				break;

			case 'sentbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " 
					WHERE privmsgs_from_userid = " . $userdata['user_id'] . " 
						AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
				break;
		}

		$saved_sql .= " AND privmsgs_id IN ($saved_sql_id)";

		if ( !$db->sql_query($saved_sql) )
		{
			trigger_error('Could not save private messages', E_USER_WARNING);
		}

		redirect(append_sid("privmsg.php?folder=savebox", true));
	}
}
else if ( $submit || $refresh || $mode != '' )
{
	if ( !$userdata['session_logged_in'] )
	{
		$user_id = ( isset($_GET[POST_USERS_URL]) ) ? '&' . POST_USERS_URL . '=' . intval($_GET[POST_USERS_URL]) : '';
		redirect(append_sid("login.php?redirect=privmsg.php?folder=$folder&mode=$mode" . $user_id, true));
	}
	
	if ( $submit )
	{

		$sql = "SELECT MAX(privmsgs_date) AS last_post_time
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_from_userid = " . $userdata['user_id'];
		if ( $result = $db->sql_query($sql) )
		{
			$db_row = $db->sql_fetchrow($result);

			$last_post_time = $db_row['last_post_time'];
			$current_time = time();

			if ( ( $current_time - $last_post_time ) < $board_config['flood_interval'])
			{
				$pm_wait_time = $board_config['flood_interval'] - ($current_time - $last_post_time);
				trigger_error("您必须等待 $pm_wait_time 秒后才能再次发送消息！", E_USER_ERROR);
			}
		}

	}

	if ( $submit )
	{
		$error_msg = '';
		if ($sid == '' || $sid != $userdata['session_id'])
		{
			$error = true;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . '错误！请重新加载页面！';
		}
		if ( !empty($_POST['username']) )
		{
			$to_username = phpbb_clean_username($_POST['username']);

			$sql = "SELECT user_id, user_notify_pm, user_email, user_active 
				FROM " . USERS_TABLE . "
				WHERE username = '" . str_replace("\'", "''", $to_username) . "'
					AND user_id <> " . ANONYMOUS;
			if ( !$result = $db->sql_query($sql) )
			{
				$error = TRUE;
				$error_msg = '对不起，您输入的用户不存在！11';
			}

			if ( !$to_userdata = $db->sql_fetchrow($result) )
			{
				$error = TRUE;
				$error_msg = '对不起，您输入的用户不存在！';
			}
		}
		else
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . '必须输入收件人才能发送信息';
		}

		$privmsg_subject = trim(htmlspecialchars($_POST['subject']));
		if ( empty($privmsg_subject) )
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . '信息的标题不能为空';
		}

		if ( strlen($privmsg_subject) < 3 )
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . '信息的标题不能小于三个字符';
		}

		if ( !empty($_POST['message']) )
		{
			if ( !$error )
			{
				$privmsg_message = trim($_POST['message']);
			}
		}
		else
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . '信息的内容不能为空';
		}
	}

	if ( $submit && !$error )
	{

		if ( !$userdata['user_allow_pm'] )
		{

			$message = '对不起，仅超级管理员才可以拒收信息';
			trigger_error($message);
		}

		$msg_time = time();

		if ( $mode != 'edit' )
		{

			$sql = "SELECT COUNT(privmsgs_id) AS inbox_items, MIN(privmsgs_date) AS oldest_post_time 
				FROM " . PRIVMSGS_TABLE . " 
				WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
						OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "  
						OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) 
					AND privmsgs_to_userid = " . $to_userdata['user_id'];
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('对不起，您输入的用户不存在', E_USER_ERROR);
			}

			if ( $inbox_info = $db->sql_fetchrow($result) )
			{
				if ($board_config['max_inbox_privmsgs'] && $inbox_info['inbox_items'] >= $board_config['max_inbox_privmsgs'])
				{
					$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . " 
						WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
								OR privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
								OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . "  ) 
							AND privmsgs_date = " . $inbox_info['oldest_post_time'] . " 
							AND privmsgs_to_userid = " . $to_userdata['user_id'];
					if ( !$result = $db->sql_query($sql) )
					{
						trigger_error('Could not find oldest privmsgs (inbox)', E_USER_WARNING);
					}
					$old_privmsgs_id = $db->sql_fetchrow($result);
					$old_privmsgs_id = $old_privmsgs_id['privmsgs_id'];
				
					$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . " 
						WHERE privmsgs_id = $old_privmsgs_id";
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not delete oldest privmsgs (inbox)'.$sql, E_USER_WARNING);
					}

					$sql = 'DELETE FROM ' . PRIVMSGS_TEXT_TABLE . " 
						WHERE privmsgs_text_id = $old_privmsgs_id";
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not delete oldest privmsgs text (inbox)', E_USER_WARNING);
					}
				}
			}

			$sql_info = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip)
				VALUES (" . PRIVMSGS_NEW_MAIL . ", '" . str_replace("\'", "''", $privmsg_subject) . "', " . $userdata['user_id'] . ", " . $to_userdata['user_id'] . ", $msg_time, '$user_ip')";
		}
		else
		{
			$sql_info = "UPDATE " . PRIVMSGS_TABLE . "
				SET privmsgs_type = " . PRIVMSGS_NEW_MAIL . ", privmsgs_subject = '" . str_replace("\'", "''", $privmsg_subject) . "', privmsgs_from_userid = " . $userdata['user_id'] . ", privmsgs_to_userid = " . $to_userdata['user_id'] . ", privmsgs_date = $msg_time, privmsgs_ip = '$user_ip', privmsgs_enable_html = $html_on 
				WHERE privmsgs_id = $privmsg_id";
		}

		if ( !($result = $db->sql_query($sql_info, BEGIN_TRANSACTION)) )
		{
			trigger_error('Could not insert/update private message sent info.', E_USER_WARNING);
		}

		if ( $mode != 'edit' )
		{
			$privmsg_sent_id = $db->sql_nextid();

			$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text)
				VALUES ($privmsg_sent_id, '" . str_replace("\'", "''", $privmsg_message) . "')";
		}
		else
		{
			$sql = "UPDATE " . PRIVMSGS_TEXT_TABLE . "
				SET privmsgs_text = '" . str_replace("\'", "''", $privmsg_message) . "'
				WHERE privmsgs_text_id = $privmsg_id";
		}

		if ( !$db->sql_query($sql, END_TRANSACTION) )
		{
			trigger_error('Could not insert/update private message sent text.', E_USER_WARNING);
		}

		if ( $mode != 'edit' )
		{
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_new_privmsg = user_new_privmsg + 1, user_last_privmsg = ' . time() . '  
				WHERE user_id = ' . $to_userdata['user_id']; 
			if ( !$status = $db->sql_query($sql) )
			{
				trigger_error('无法更新用户信息条数', E_USER_WARNING);
			}

			if ( $to_userdata['user_notify_pm'] && !empty($to_userdata['user_email']) && $to_userdata['user_active'] )
			{
				$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
				$script_name = ( $script_name != '' ) ? $script_name . '/privmsg.php' : 'privmsg.php';
				$server_name = trim($board_config['server_name']);
				$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
				$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

				include(ROOT_PATH . 'includes/class/emailer.php');
				$emailer = new emailer();
				
				$emailer->cc('');
				$emailer->bcc('');
				$emailer->from($board_config['board_email']);
				$emailer->replyto($board_config['board_email']);

				$emailer->use_template('privmsg_notify');
				$emailer->email_address($to_userdata['user_email']);
				$emailer->set_subject('您有一条新信息');
					
				$emailer->assign_vars(array(
					'USERNAME' 	=> stripslashes($to_username), 
					'SITENAME' 	=> $board_config['sitename'],
					'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 
					'U_INBOX' 	=> $server_protocol . $server_name . $server_port . $script_name . '?folder=inbox')
				);

				$emailer->send();
				$emailer->reset();
			}
		}

		trigger_error('信息发送成功！<br /><br />点击<a href="' . append_sid("privmsg.php?folder=inbox") . '">这里</a>返回收信箱', E_USER_ERROR);
	}
	else if ( $preview || $refresh || $error )
	{
		$to_username = (isset($_POST['username']) ) ? trim(htmlspecialchars(stripslashes($_POST['username']))) : '';

		$privmsg_subject = ( isset($_POST['subject']) ) ? trim(htmlspecialchars(stripslashes($_POST['subject']))) : '';
		$privmsg_message = ( isset($_POST['message']) ) ? trim($_POST['message']) : '';
		if ( !$preview )
		{
			$privmsg_message = stripslashes($privmsg_message);
		}

		if ( $mode == 'post' )
		{
			$page_title = '发信息';
		}
		else if ( $mode == 'reply' )
		{
			$page_title = '回信息';
		}
	}
	else 
	{
		if ( !$privmsg_id && ( $mode == 'reply' || $mode == 'edit' || $mode == 'quote' ) )
		{
			trigger_error('您必须指定信息的ID', E_USER_ERROR);
		}

		if ( !empty($_GET[POST_USERS_URL]) )
		{
			$user_id = intval($_GET[POST_USERS_URL]);

			$sql = "SELECT username
				FROM " . USERS_TABLE . "
				WHERE user_id = $user_id
					AND user_id <> " . ANONYMOUS;
			if ( !($result = $db->sql_query($sql)) )
			{
				$error = TRUE;
				$error_msg = '对不起，您输入的用户不存在';
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				$to_username = $row['username'];
			}
		}
		else if ( $mode == 'edit' )
		{
			$sql = "SELECT pm.*, pmt.privmsgs_text, u.username, u.user_id 
				FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u
				WHERE pm.privmsgs_id = $privmsg_id
					AND pmt.privmsgs_text_id = pm.privmsgs_id
					AND pm.privmsgs_from_userid = " . $userdata['user_id'] . "
					AND ( pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
						OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) 
					AND u.user_id = pm.privmsgs_to_userid";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain private message for editing', E_USER_WARNING);
			}

			if ( !($privmsg = $db->sql_fetchrow($result)) )
			{
				redirect(append_sid("privmsg.php?folder=$folder", true));
			}

			$privmsg_subject 		= $privmsg['privmsgs_subject'];
			$privmsg_message 		= $privmsg['privmsgs_text'];
			$privmsg_message 		= str_replace('<br />', "\n", $privmsg_message);
			$to_username 			= $privmsg['username'];
			$to_userid 				= $privmsg['user_id'];

		}
		else if ( $mode == 'reply' )
		{

			$sql = "SELECT pm.privmsgs_subject, pm.privmsgs_date, pmt.privmsgs_text, u.username, u.user_id
				FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u
				WHERE pm.privmsgs_id = $privmsg_id
					AND pmt.privmsgs_text_id = pm.privmsgs_id
					AND pm.privmsgs_to_userid = " . $userdata['user_id'] . "
					AND u.user_id = pm.privmsgs_from_userid";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain private message for editing', E_USER_WARNING);
			}

			if ( !($privmsg = $db->sql_fetchrow($result)) )
			{
				redirect(append_sid("privmsg.php?folder=$folder", true));
			}

			$orig_word = $replacement_word = array();
			obtain_word_list($orig_word, $replacement_word);

			$privmsg_subject 	= ( ( !preg_match('/^Re:/', $privmsg['privmsgs_subject']) ) ? 'Re: ' : '' ) . $privmsg['privmsgs_subject'];
			$privmsg_subject 	= str_replace($orig_word, $replacement_word, $privmsg_subject);

			$to_username 		= $privmsg['username'];
			$to_userid 			= $privmsg['user_id'];

		}
		else
		{
			$privmsg_subject = $privmsg_message = $to_username = '';
		}
	}

	if ( !$userdata['user_allow_pm'] )
	{
		$message = '对不起，仅超级管理员可以拒收信息';
		trigger_error($message);
	}

	if ($error)
	{
		$privmsg_message = htmlspecialchars($privmsg_message);
		error_box('ERROR_BOX', $error_msg);
	}

	$template->set_filenames(array(
		'body' => 'privmsg_posting.tpl')
	);

	if ( $mode == 'post' )
	{
		$post_a = '发送信息';
	}
	else if ( $mode == 'reply' )
	{
		$post_a = '回复信息';
		$mode = 'post';
	}

	$page_title = $post_a;
	page_header($page_title);
	
	$s_hidden_fields 	= '<input type="hidden" name="folder" value="' . $folder . '" />';
	$s_hidden_fields 	.= '<input type="hidden" name="mode" value="' . $mode . '" />';
	$s_hidden_fields 	.= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
	
	$privmsg_subject = isset($privmsg_subject) ? $privmsg_subject : '';
	$privmsg_message = isset($privmsg_message) ? $privmsg_message : '';
	
	$template->assign_vars(array(
		'SUBJECT' 				=> $privmsg_subject, 
		'USERNAME' 				=> $to_username,
		'MESSAGE' 				=> $privmsg_message,
		'BOX_NAME' 				=> $post_a, 
		'U_INBOX' 				=> append_sid('privmsg.php?folder=inbox'), 

		'S_HIDDEN_FORM_FIELDS' 	=> $s_hidden_fields,
		'S_POST_ACTION' 		=> append_sid("privmsg.php"))
	);

	$template->pparse('body');

	page_footer();
}

if ( !$userdata['session_logged_in'] )
{
	login_back("privmsg.php?folder=inbox");
}

$pr_id = isset($_GET['p']) ? abs(intval($_GET['p'])) : '';
if( isset($_GET['history']) )
{
	if( !(is_numeric($pr_id) && $pr_id > 0) )
	{
		trigger_error('您必须指定信息的ID', E_USER_ERROR);
	}
}

if( is_numeric($pr_id) && (isset($_GET['history'])) )
{
	if (isset($_GET['download']))
	{
		$sql = "SELECT privmsgs_from_userid
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_id = " . $pr_id;
		$result = $db->sql_query($sql);
		if (!$result)
		{
			trigger_error('Could not query private message post information', E_USER_WARNING);
		}
		$privrow  = $db->sql_fetchrow($result);		
		$user_from =  $privrow['privmsgs_from_userid'];
		$user_id = $userdata['user_id'];
	
		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);

		$sql = "SELECT *
			FROM " . PRIVMSGS_TABLE . " t, " . PRIVMSGS_TEXT_TABLE . " p
			WHERE t.privmsgs_id = p.privmsgs_text_id
			AND ((t.privmsgs_from_userid = $user_from  
			AND t.privmsgs_to_userid = $user_id)  
			OR (t.privmsgs_from_userid = $user_id  
			AND t.privmsgs_to_userid = $user_from)) 
			AND ( t.privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
			OR t.privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
			OR t.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )
		ORDER BY t.privmsgs_date ASC";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not create download', E_USER_WARNING);
		}

		$download_file = '';

		while ( $row = $db->sql_fetchrow($result) )
		{
			$poster_id 		= $row['privmsgs_from_userid'];
			$poster_fro 	= $row['privmsgs_to_userid'];

			$this_userdata 	= get_userdata($poster_id);
			$poster_from 	= $this_userdata['username'];
		
			$t_userdata 	= get_userdata($poster_fro);
			$poster 		= $t_userdata['username'];

			$post_date 		= create_date($board_config['default_dateformat'], $row['privmsgs_date'], $board_config['board_timezone']);
			$post_subject 	= '信息: ' . $row['privmsgs_subject'];

			//$bbcode_uid 	= $row['bbcode_uid'];
			$message 		= $row['privmsgs_text'];
			$message 		= strip_tags($message);
			//$message = preg_replace("/\[.*?:$bbcode_uid:?.*?\]/si", '', $message);
			$message		= preg_replace('/\[url\]|\[\/url\]/si', '', $message);
			$message 		= preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);

			$message 		= unprepare_message($message);
			$message 		= preg_replace('/&#40;/', '(', $message);
			$message 		= preg_replace('/&#41;/', ')', $message);
			$message 		= preg_replace('/&#58;/', ':', $message);
			$message 		= preg_replace('/&#91;/', '[', $message);
			$message 		= preg_replace('/&#93;/', ']', $message);
			$message 		= preg_replace('/&#123;/', '{', $message);
			$message 		= preg_replace('/&#125;/', '}', $message);

			if (count($orig_word))
			{
				$post_subject = str_replace($orig_word, $replacement_word, $post_subject);
				$message = str_replace($orig_word, $replacement_word, $message);
			}

			$break = "\n";
			$line = '---------------';
			$download_file .= $post_subject . $break . '来自: '.$poster_from . $break . '收件人: ' . $poster . $break . $post_date . $break . $message . $break . $line . $break;
		}

		$disp_folder = 'from_'.$poster_id.'_to_'.$poster_fro;
		$filename = $board_config['sitename'] . '_' . $disp_folder . '.txt';
		header('Content-Type: text/plain; name="'.$filename.'"');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Content-Transfer-Encoding: plain/text');
		header('Content-Length: '.strlen($download_file));
		print $download_file;
		exit;
	}

	$sql = "SELECT privmsgs_from_userid
		FROM " . PRIVMSGS_TABLE . "
		WHERE privmsgs_id = " . $pr_id;
	$result = $db->sql_query($sql);
	if (!$result)
	{
		trigger_error('Could not query private message post information', E_USER_WARNING);
	}
	$privrow  = $db->sql_fetchrow($result);	
	$user_from =  $privrow['privmsgs_from_userid'];
	$user_id = $userdata['user_id'];

	$page_title = '信息记录';
	page_header($page_title);
	$template->set_filenames(array(
		'body' => 'privmsgs_history_body.tpl')
	);

	$sql = "SELECT *
		FROM " . PRIVMSGS_TABLE . " t, " . PRIVMSGS_TEXT_TABLE . " p
		WHERE t.privmsgs_id = p.privmsgs_text_id
			AND ((t.privmsgs_from_userid = $user_from  
			AND t.privmsgs_to_userid = $user_id)
			OR (t.privmsgs_from_userid = $user_id  
			AND t.privmsgs_to_userid = $user_from)) 
			AND ( t.privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
			OR t.privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
			OR t.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )
		ORDER BY t.privmsgs_date DESC";
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('Could not query users', E_USER_WARNING);
	}
	$total = $db->sql_fetchrowset($result);
	
	for($i = $start; $i < count($total) & $i < $board_config['topics_per_page'] + $start; $i++)
	{	
		$privmsgs_text = $total[$i]['privmsgs_text'];
		$privmsgs_from = $total[$i]['privmsgs_from_userid'];
		$privmsgs_text = str_replace(PHP_EOL, '<br />', $privmsgs_text);
		
		$sql = "SELECT user_id, username
			FROM " . USERS_TABLE . "
			WHERE user_id = " . $privmsgs_from;
		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not query users', E_USER_WARNING);
		}
		$name = $db->sql_fetchrow($result);
		$from_id = $name['user_id'];

		$from = ( $privmsgs_from == $userdata['user_id']) ? '我' : $name['username'];
		$temp_urla = append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$from_id");
		$otvet = '<a href="' . $temp_urla . '">' .$from . '</a>';
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	
		$template->assign_block_vars('history', array(
			'ROW_CLASS' 	=> $row_class,
			'DATE' 			=> create_date('H:i', $total[$i]['privmsgs_date'], $board_config['board_timezone']),
			//'THEME' 		=> $privmsgs_subject, //取消标题显示
			'TEXT' 			=> $privmsgs_text,
			'FROM' 			=> $otvet)
		);
	}
	
	$pagination = ( count($total) > $board_config['topics_per_page']) ? generate_pagination("privmsg.php?history&amp;p=$pr_id", count($total), $board_config['topics_per_page'], $start) : '';

	$template->assign_vars(array(
		'S_BACK' 		=> append_sid("privmsg.php?folder=inbox"),
		'U_NEW_PM' 		=> append_sid("privmsg.php?mode=post&amp;" . POST_USERS_URL . "=$user_from"),
		'S_HTXT' 		=> append_sid("privmsg.php?history&amp;p=$pr_id&amp;download"),
		'PAGINATION'	=> $pagination)
	);

	$template->pparse('body');
	page_footer();
}

$sql = 'UPDATE ' . USERS_TABLE . '
	SET user_unread_privmsg = user_unread_privmsg + user_new_privmsg, user_new_privmsg = 0, user_last_privmsg = ' . $userdata['session_start'] . ' 
	WHERE user_id = ' . $userdata['user_id'];
	
if ( !$db->sql_query($sql) )
{
	trigger_error('Could not update private message new/read status for user', E_USER_WARNING);
}

$sql = 'UPDATE ' . PRIVMSGS_TABLE . '
	SET privmsgs_type = ' . PRIVMSGS_UNREAD_MAIL . ' 
	WHERE privmsgs_type = ' . PRIVMSGS_NEW_MAIL . ' 
		AND privmsgs_to_userid = ' . $userdata['user_id'];
		
if ( !$db->sql_query($sql) )
{
	trigger_error('Could not update private message new/read status (2) for user', E_USER_WARNING);
}

$userdata['user_new_privmsg'] 		= 0;
$userdata['user_unread_privmsg'] 	= ( $userdata['user_new_privmsg'] + $userdata['user_unread_privmsg'] );

$page_title = '我的信箱';
page_header($page_title);

$template->set_filenames(array(
	'body' => 'privmsgs_body.tpl')
);

$orig_word 			= array();
$replacement_word 	= array();
obtain_word_list($orig_word, $replacement_word);

$sql_tot = 'SELECT COUNT(privmsgs_id) AS total 
	FROM ' . PRIVMSGS_TABLE . ' ';
$sql = 'SELECT pm.privmsgs_type, pm.privmsgs_id, pm.privmsgs_date, pm.privmsgs_subject, u.user_id, u.username 
	FROM ' . PRIVMSGS_TABLE . ' pm, ' . USERS_TABLE . ' u ';
	
switch( $folder )
{
	case 'inbox':
		$sql_tot .= "WHERE privmsgs_to_userid = " . $userdata['user_id'] . "
			AND ( privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
				OR privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
				OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";

		$sql .= "WHERE pm.privmsgs_to_userid = " . $userdata['user_id'] . "
			AND u.user_id = pm.privmsgs_from_userid
			AND ( pm.privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
				OR pm.privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
				OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
		break;

	case 'outbox':
		$sql_tot .= "WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
			AND ( privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
				OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";

		$sql .= "WHERE pm.privmsgs_from_userid = " . $userdata['user_id'] . "
			AND u.user_id = pm.privmsgs_to_userid
			AND ( pm.privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
				OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
		break;

	case 'sentbox':
		$sql_tot .= "WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
			AND privmsgs_type =  " . PRIVMSGS_SENT_MAIL;

		$sql .= "WHERE pm.privmsgs_from_userid = " . $userdata['user_id'] . "
			AND u.user_id = pm.privmsgs_to_userid
			AND pm.privmsgs_type =  " . PRIVMSGS_SENT_MAIL;
		break;

	case 'savebox':
		$sql_tot .= "WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . " 
				AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
			OR ( privmsgs_from_userid = " . $userdata['user_id'] . " 
				AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") )";

		$sql .= "WHERE u.user_id = pm.privmsgs_from_userid 
			AND ( ( pm.privmsgs_to_userid = " . $userdata['user_id'] . " 
				AND pm.privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) 
			OR ( pm.privmsgs_from_userid = " . $userdata['user_id'] . " 
				AND pm.privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " ) )";
		break;

	default:
		trigger_error('没有这样的文件夹', E_USER_ERROR);
		break;
}
//
// Show messages over previous x days/months
//
if ( $submit_msgdays && ( !empty($HTTP_POST_VARS['msgdays']) || !empty($HTTP_GET_VARS['msgdays']) ) )
{
	$msg_days = ( !empty($HTTP_POST_VARS['msgdays']) ) ? intval($HTTP_POST_VARS['msgdays']) : intval($HTTP_GET_VARS['msgdays']);
	$min_msg_time = time() - ($msg_days * 86400);

	$limit_msg_time_total = " AND privmsgs_date > $min_msg_time";
	$limit_msg_time = " AND pm.privmsgs_date > $min_msg_time ";

	if ( !empty($HTTP_POST_VARS['msgdays']) )
	{
		$start = 0;
	}
}
else
{
	$limit_msg_time = $limit_msg_time_total = '';
	$msg_days = 0;
}

$sql .= $limit_msg_time . " ORDER BY pm.privmsgs_date DESC LIMIT $start, " . $board_config['topics_per_page'];
$sql_all_tot = $sql_tot;
$sql_tot .= $limit_msg_time_total;

if ( !($result = $db->sql_query($sql_tot)) )
{
	trigger_error('Could not query private message information', E_USER_WARNING);
}

$pm_total = ( $row = $db->sql_fetchrow($result) ) ? $row['total'] : 0;

if ( !($result = $db->sql_query($sql_all_tot)) )
{
	trigger_error('Could not query private message information', E_USER_WARNING);
}

$pm_all_total = ( $row = $db->sql_fetchrow($result) ) ? $row['total'] : 0;

$previous_days = array(0, 1, 7, 14, 30, 90, 180, 364);
$select_msg_days = '';
$previous_days_text = array('所有信息', '1天内', '7天内', '2周内', '1个月内', '3个月内', '6个月内', '一年内');

for($i = 0; $i < count($previous_days); $i++)
{
	$selected = ( $msg_days == $previous_days[$i] ) ? ' selected="selected"' : '';
	$select_msg_days .= '<option value="' . $previous_days[$i] . '"' . $selected . '>' . $previous_days_text[$i] . '</option>';
}

switch ( $folder )
{
	case 'inbox':
		$l_box_name = '收信箱';
		break;
	case 'outbox':
		$l_box_name = '发件箱';
		break;
	case 'savebox':
		$l_box_name = '草稿箱';
		break;
	case 'sentbox':
		$l_box_name = '已发送';
		break;
}

if ( $folder != 'outbox' )
{
	$inbox_limit_pct = ( $board_config['max_' . $folder . '_privmsgs'] > 0 ) ? $pm_all_total . '/' . $board_config['max_' . $folder . '_privmsgs'] : 100;
	$inbox_limit_img_length = ( $board_config['max_' . $folder . '_privmsgs'] > 0 ) ? round(( $pm_all_total / $board_config['max_' . $folder . '_privmsgs'] ) * 175) : 175;
	$inbox_limit_remain = ( $board_config['max_' . $folder . '_privmsgs'] > 0 ) ? $board_config['max_' . $folder . '_privmsgs'] - $pm_all_total : 0;

	$template->assign_block_vars('switch_box_size_notice', array());

	switch( $folder )
	{
		case 'inbox':
			$l_box_size_status = '收信箱(' . $inbox_limit_pct. ')';
			break;
		case 'sentbox':
			$l_box_size_status = '发件箱(' . $inbox_limit_pct. ')';
			break;
		case 'savebox':
			$l_box_size_status = '草稿箱(' . $inbox_limit_pct. ')';
			break;
		default:
			$l_box_size_status = '';
			break;
	}
}
else
{
	$inbox_limit_img_length = $inbox_limit_pct = $l_box_size_status = '';
	$template->assign_block_vars('switch_box_size_notice', array());
	$l_box_size_status = '发件箱';
}

$template->assign_vars(array(
	'BOX_NAME' 			=> $l_box_name, 
	'INBOX_IMG' 		=> $inbox_img, 
	'SENTBOX_IMG' 		=> $sentbox_img, 
	'OUTBOX_IMG' 		=> $outbox_img, 
	'SAVEBOX_IMG'		=> $savebox_img, 
	'INBOX' 			=> $inbox_url, 
	'SENTBOX' 			=> $sentbox_url, 
	'OUTBOX' 			=> $outbox_url, 
	'SAVEBOX' 			=> $savebox_url, 

	'IMG_POSTPM'		=> make_style_image('privmsg_create'),
	'U_POST_PM' 		=> append_sid('privmsg.php?mode=post'), 

	'INBOX_LIMIT_IMG_WIDTH' => $inbox_limit_img_length, 
	'INBOX_LIMIT_PERCENT' => $inbox_limit_pct, 

	'BOX_SIZE_STATUS' => $l_box_size_status, 

	'L_FROM_OR_TO' => ( $folder == 'inbox' || $folder == 'savebox' ) ? '来自' : '收信人', 

	'S_PRIVMSGS_ACTION' 	=> append_sid("privmsg.php?folder=$folder"),
	'S_SELECT_MSG_DAYS' 	=> $select_msg_days,
	
	'U_POST_NEW_TOPIC' 		=> append_sid("privmsg.php?mode=post"))
);
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not query private messages', E_USER_WARNING);
}

if ( $row = $db->sql_fetchrow($result) )
{
	$i = 0;
	do
	{
		$privmsg_id 			= $row['privmsgs_id'];
		$flag 					= $row['privmsgs_type'];
		$icon_flag 				= ( $flag == PRIVMSGS_NEW_MAIL || $flag == PRIVMSGS_UNREAD_MAIL ) ? make_style_image('privmsg_unread') : make_style_image('privmsg_read');
		$msg_userid 			= $row['user_id'];
		$msg_username 			= $row['username'];		
		$u_from_user_profile 	= append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $msg_userid);

		$msg_subject 			= $row['privmsgs_subject'];

		//标题
		if ( count($orig_word) )
		{
			$msg_subject = str_replace($orig_word, $replacement_word, $msg_subject);
		}
		
		//标题链接
		$u_subject 	= append_sid("privmsg.php?folder=$folder&amp;mode=read&amp;" . POST_POST_URL . "=$privmsg_id");
		
		//创建日期
		$msg_date 	= create_date($board_config['default_dateformat'], $row['privmsgs_date'], $board_config['board_timezone']);

		if ( $flag == PRIVMSGS_NEW_MAIL && $folder == 'inbox' )
		{
			$msg_subject 	= '<b>' . $msg_subject . '</b>';
			$msg_date 		= '<b>' . $msg_date . '</b>';
			$msg_username 	= '<b>' . $msg_username . '</b>';
		}

		$row_class = ( !($i % 2) ) ? 'row1 row-padding' : 'row2 row-padding';
		$template->assign_block_vars('listrow', array(
			//'NUMBER'					=> $i + $start + 1,//信息楼层数量
			'ROW_CLASS' 				=> $row_class,
			'FROM' 						=> $msg_username,
			'SUBJECT' 					=> $msg_subject,
			'DATE' 						=> $msg_date,
			'PRIVMSG_FOLDER_IMG' 		=> $icon_flag,
			'U_READ' 					=> $u_subject)
		);
		$i++;
	}
	while( $row = $db->sql_fetchrow($result) );

	$template->assign_vars(array(
		'PAGINATION' 	=> generate_pagination("privmsg.php?folder=$folder", $pm_total, $board_config['topics_per_page'], $start))
	);

}
else
{
	$template->assign_block_vars('switch_no_messages', array());
}

$template->pparse('body');
page_footer();
?>