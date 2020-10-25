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
require(ROOT_PATH . 'includes/attach/attachment_mod.php');
include(ROOT_PATH . 'includes/functions/bbcode.php');// BBCode 函数
include(ROOT_PATH . 'includes/functions/post.php');// 表单处理函数

$params = array('submit' => 'post', 'preview' => 'preview', 'delete' => 'delete', 'translit' => 'translit', 'poll_delete' => 'poll_delete', 'poll_add' => 'add_poll_option', 'poll_edit' => 'edit_poll_option', 'mode' => 'mode', 'lock_subject' => 'lock_subject');

foreach($params as $var => $param)
{
	if ( !empty($_POST[$param]) || !empty($_GET[$param]) )
	{
		$$var = ( !empty($_POST[$param]) ) ? htmlspecialchars($_POST[$param]) : htmlspecialchars($_GET[$param]);
	}
	else
	{
		$$var = '';
	}
}

$confirm = isset($_POST['confirm']) ? true : false;
$sid = (isset($_POST['sid'])) ? $_POST['sid'] : 0;

$params = array('forum_id' => POST_FORUM_URL, 'topic_id' => POST_TOPIC_URL, 'post_id' => POST_POST_URL, 'user_otv_id' => POST_USERS_URL);

foreach($params as $var => $param)
{
	if ( !empty($_POST[$param]) || !empty($_GET[$param]) )
	{
		$$var = ( !empty($_POST[$param]) ) ? intval($_POST[$param]) : intval($_GET[$param]);
	}
	else
	{
		$$var = '';
	}
}

$refresh = $preview || $poll_add || $poll_edit || $poll_delete;
$orig_word = $replacement_word = array();

$topic_type = ( !empty($_POST['topictype']) ) ? intval($_POST['topictype']) : POST_NORMAL;
$topic_type = ( in_array($topic_type, array(POST_NORMAL, POST_STICKY, POST_ANNOUNCE)) ) ? $topic_type : POST_NORMAL;

$userdata = $session->start($user_ip, PAGE_POSTING);
init_userprefs($userdata);

if ( isset($_POST['cancel']) )
{
	if ( $post_id )
	{
		$redirect = 'viewtopic.php?' . POST_POST_URL . '=' . $post_id;
		$post_append = '#$post_id';
	}
	else if ( $topic_id )
	{
		$redirect = 'viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id;
		$post_append = '';
	}
	else if ( $forum_id )
	{
		$redirect = 'viewforum.php?' . POST_FORUM_URL . '=' . $forum_id;
		$post_append = '';
	}
	else
	{
		$redirect = 'index.php';
		$post_append = '';
	}

	redirect(append_sid($redirect, true) . $post_append);
}

$is_auth = array();
switch( $mode )
{
	case 'newtopic':
		if ( $topic_type == POST_ANNOUNCE )
		{
			$is_auth_type = 'auth_announce';
		}
		else if ( $topic_type == POST_STICKY )
		{
			$is_auth_type = 'auth_sticky';
		}
		else
		{
			$is_auth_type = 'auth_post';
		}
		break;
	case 'reply':
	case 'otv':
	case 'quote':
		$is_auth_type = 'auth_reply';
		break;
	case 'editpost':
		$is_auth_type = 'auth_edit';
		break;
	case 'delete':
	case 'poll_delete':
		$is_auth_type = 'auth_delete';
		break;
	case 'vote':
		$is_auth_type = 'auth_vote';
		break;
	case 'topicreview':
		$is_auth_type = 'auth_read';
		break;
	default:
		trigger_error('没有指定发帖模式', E_USER_ERROR);
		break;
}

$error_msg = '';
$post_data = array();
switch ( $mode )
{
	case 'newtopic':
		if ( empty($forum_id) )
		{
			trigger_error('论坛不存在', E_USER_ERROR);
		}

		$sql = 'SELECT * 
			FROM ' . FORUMS_TABLE . ' 
			WHERE forum_id = ' . $forum_id;

		break;

	case 'reply':
	case 'vote':
		if ( empty( $topic_id) )
		{
			trigger_error('您必须选择要回复的主题', E_USER_ERROR);
		}

		$sql = 'SELECT f.*, t.topic_status, t.topic_title, t.topic_type 
			FROM ' . FORUMS_TABLE . ' f, ' . TOPICS_TABLE . " t
			WHERE t.topic_id = $topic_id
				AND f.forum_id = t.forum_id";
		break;

	case 'quote':
	case 'otv':
	case 'editpost':
	case 'delete':
	case 'poll_delete':
		if ( empty($post_id) )
		{
			trigger_error('您必须指定贴子', E_USER_ERROR);
		}

		$select_sql = (!$submit) ? ', t.topic_title, p.enable_bbcode, p.enable_html, p.enable_smilies, p.enable_sig, p.post_username, pt.post_subject, pt.post_text, pt.bbcode_uid, u.username, u.user_id, u.user_sig, u.user_sig_bbcode_uid' : '';
		$from_sql = ( !$submit ) ? ', ' . POSTS_TEXT_TABLE . ' pt, ' . USERS_TABLE . ' u' : '';
		$where_sql = (!$submit) ? 'AND pt.post_id = p.post_id AND u.user_id = p.poster_id' : '';
 
		$sql = 'SELECT f.*, t.topic_id, t.topic_status, t.topic_type, t.topic_marrow, t.topic_first_post_id, t.topic_last_post_id, t.topic_vote, p.post_id, p.poster_id, p.post_time' . $select_sql . ' 
			FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t, ' . FORUMS_TABLE . ' f' . $from_sql . " 
			WHERE p.post_id = $post_id 
				AND t.topic_id = p.topic_id 
				AND f.forum_id = p.forum_id
				$where_sql";
		break;

	default:
		trigger_error('您只能发表新帖子、回复主题或者引用回复，请返回重试', E_USER_ERROR);
}

if ( ($result = $db->sql_query($sql)) && ($post_info = $db->sql_fetchrow($result)) )
{

	$db->sql_freeresult($result);

	$forum_id 		= $post_info['forum_id'];
	$forum_name 	= $post_info['forum_name'];
	$topic_title 	= (isset($post_info['topic_title'])) ? $post_info['topic_title'] : '';
	$is_auth 		= auth(AUTH_ALL, $forum_id, $userdata, $post_info);
	
	if ( $post_info['forum_status'] == FORUM_LOCKED && !$is_auth['auth_mod'] ) 
	{ 
		trigger_error('该论坛已经锁定，不能发表新主题、帖子、回复主题和编辑发贴', E_USER_ERROR);
	} 
	else if ( $mode != 'newtopic' && $post_info['topic_status'] == TOPIC_LOCKED && !$is_auth['auth_mod']) 
	{ 
		trigger_error('该主题已经锁定，不能编辑贴子和回复主题', E_USER_ERROR);
	} 

	if ( $mode == 'editpost' || $mode == 'delete' || $mode == 'poll_delete' )
	{
		$topic_id = $post_info['topic_id'];

		$post_data['poster_post'] = ( $post_info['poster_id'] == $userdata['user_id'] ) ? true : false;
		$post_data['first_post'] = ( $post_info['topic_first_post_id'] == $post_id ) ? true : false;
		$post_data['last_post'] = ( $post_info['topic_last_post_id'] == $post_id ) ? true : false;
		$post_data['last_topic'] = ( $post_info['forum_last_post_id'] == $post_id ) ? true : false;
		$post_data['has_poll'] = ( $post_info['topic_vote'] ) ? true : false; 
		$post_data['topic_type'] = $post_info['topic_type'];
		$post_data['poster_id'] = $post_info['poster_id'];

		if ( $post_data['first_post'] && $post_data['has_poll'] )
		{
			$sql = 'SELECT * 
				FROM ' . VOTE_DESC_TABLE . ' vd, ' . VOTE_RESULTS_TABLE . " vr 
				WHERE vd.topic_id = $topic_id 
					AND vr.vote_id = vd.vote_id 
				ORDER BY vr.vote_option_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain vote data for this topic', E_USER_WARNING);
			}

			$poll_options = array();
			$poll_results_sum = 0;
			if ( $row = $db->sql_fetchrow($result) )
			{
				$poll_title = $row['vote_text'];
				$poll_id = $row['vote_id'];
				$poll_length = $row['vote_length'] / 86400;

				do
				{
					$poll_options[$row['vote_option_id']] = $row['vote_option_text']; 
					$poll_results_sum += $row['vote_result'];
				}
				while ( $row = $db->sql_fetchrow($result) );
			}
			$db->sql_freeresult($result);

			$post_data['edit_poll'] = ( ( !$poll_results_sum || $is_auth['auth_mod'] ) && $post_data['first_post'] ) ? true : 0;
		}
		else 
		{
			$post_data['edit_poll'] = ($post_data['first_post'] && $is_auth['auth_pollcreate']) ? true : false;
		}

		if ( $post_info['poster_id'] != $userdata['user_id'] && !$is_auth['auth_mod'] )
		{
			$message = ( $delete || $mode == 'delete' ) ? '对不起，您没有权限删除别人的帖子或主题' : '对不起，您没有权限编辑别人的帖子或主题';
			$message .= '<br />点击 <a href="' . append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id) . '">这里</a> 返回主题页面';
			trigger_error($message);
		}
		else if ( !$post_data['last_post'] && !$is_auth['auth_mod'] && ( $mode == 'delete' || $delete ) )
		{
			trigger_error('对不起，您不能删除您发表的主题，因为别人已经回复了您的主题', E_USER_ERROR);
		}
		else if ( !$post_data['edit_poll'] && !$is_auth['auth_mod'] && ( $mode == 'poll_delete' || $poll_delete ) )
		{
			trigger_error('对不起，您不能删除一个已经有投票的贴子', E_USER_ERROR);
		}
	}
	else
	{
		if ( $mode == 'quote' || $mode == 'otv' )
		{
			$topic_id = $post_info['topic_id'];
		}
		if ( $mode == 'newtopic' )
		{
			$post_data['topic_type'] = POST_NORMAL;
		}

		$post_data['first_post'] = ( $mode == 'newtopic' ) ? true : 0;
		$post_data['last_post'] = false;
		$post_data['has_poll'] = false;
		$post_data['edit_poll'] = false;
	}
	if ( $mode == 'poll_delete' && !isset($poll_id) )
	{
		trigger_error('帖子不存在，请返回重试', E_USER_ERROR);
	}
}
else
{
	trigger_error('帖子不存在，请返回重试', E_USER_ERROR);
}

$is_auth_type = ( $is_auth['auth_mod'] ) ? $is_auth['auth_mod'] : $is_auth[$is_auth_type];
if ( !$is_auth_type )
{
	$lang = array(
		'Sorry_auth_announce' => '对不起，仅 %s 可以发表公告',
		'Sorry_auth_sticky' => '对不起，仅 %s 可以置顶贴子', 
		'Sorry_auth_read' => '对不起，仅 %s 可以阅读主题', 
		'Sorry_auth_post' => '对不起，仅 %s 可以发表新贴', 
		'Sorry_auth_reply' => '对不起，仅 %s 可以回复发贴', 
		'Sorry_auth_edit' => '对不起，仅 %s 可以编辑发贴', 
		'Sorry_auth_delete' => '对不起，仅 %s 可以删除贴子', 
		'Sorry_auth_vote' => '对不起，仅 %s 可以参与投票', 
	);
	
	if ( $userdata['session_logged_in'] )
	{
		trigger_error(sprintf($lang['Sorry_' . $is_auth_type], $is_auth[$is_auth_type . '_type']), E_USER_ERROR);
	}

	switch( $mode )
	{
		case 'newtopic':
			$redirect = 'mode=newtopic&' . POST_FORUM_URL . '=' . $forum_id;
			break;
		case 'reply':
		case 'topicreview':
			$redirect = 'mode=reply&' . POST_TOPIC_URL . '=' . $topic_id;
			break;
		case 'otv':
			$redirect = 'mode=otv&' . POST_POST_URL . '=' . $post_id;
			break;
		case 'quote':
		case 'editpost':
			$redirect = 'mode=quote&' . POST_POST_URL . '=' . $post_id;
			break;
	}

	login_back('posting.php?' . $redirect);
}

if ( !$board_config['allow_html'] )
{
	$html_on = 0;
}
else
{
	$html_on = ( $submit || $refresh ) ? ( ( !empty($_POST['disable_html']) ) ? 0 : TRUE ) : ( ( $userdata['user_id'] == ANONYMOUS ) ? $board_config['allow_html'] : $userdata['user_allowhtml'] );
}

if ( !$board_config['allow_bbcode'] )
{
	$bbcode_on = 0;
}
else
{
	$bbcode_on = ( $submit || $refresh ) ? ( ( !empty($_POST['disable_bbcode']) ) ? 0 : TRUE ) : ( ( $userdata['user_id'] == ANONYMOUS ) ? $board_config['allow_bbcode'] : $userdata['user_allowbbcode'] );
}

if ( !$board_config['allow_smilies'] )
{
	$smilies_on = 0;
}
else
{
	$smilies_on = ( $submit || $refresh ) ? ( ( !empty($_POST['disable_smilies']) ) ? 0 : TRUE ) : ( ( $userdata['user_id'] == ANONYMOUS ) ? $board_config['allow_smilies'] : $userdata['user_allowsmile'] );
}

if ( ($submit || $refresh) && $is_auth['auth_read'])
{
	$notify_user = ( !empty($_POST['notify']) ) ? TRUE : 0;
}
else
{
	if ( $mode != 'newtopic' && $userdata['session_logged_in'] && $is_auth['auth_read'] )
	{
		$sql = 'SELECT topic_id 
			FROM ' . TOPICS_WATCH_TABLE . "
			WHERE topic_id = $topic_id 
				AND user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain topic watch information', E_USER_WARNING);
		}
		if ( $db->sql_fetchrow($result) )
		{
			$notify_user = TRUE;
		}
		else
		{
			$notify_user = FALSE;
		}
		$db->sql_freeresult($result);
	}
	else
	{
		$notify_user = FALSE;
	}
}

$attach_sig = ( $submit || $refresh ) ? ( ( !empty($_POST['attach_sig']) ) ? TRUE : 0 ) : ( ( $userdata['user_id'] == ANONYMOUS ) ? 0 : $userdata['user_attachsig'] );


execute_posting_attachment_handling();

$username 		= '';
$subject		= '';
$poll_title 	= '';
$poll_length 	= 0;

if ( ( $delete || $poll_delete || $mode == 'delete' ) && !$confirm )
{
	$page_title	 		= '删除操作';
	$s_hidden_fields 	= '<input type="hidden" name="' . POST_POST_URL . '" value="' . $post_id . '" />';
	$s_hidden_fields 	.= ( $delete || $mode == "delete" ) ? '<input type="hidden" name="mode" value="delete" />' : '<input type="hidden" name="mode" value="poll_delete" />';
	$s_hidden_fields 	.= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

	$l_confirm = ( $delete || $mode == 'delete' ) ? '请确认是否删除？' : '请确认是否删除投票';

	page_header($page_title);

	$template->set_filenames(array(
		'confirm_body' 	=> 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE'		=> '删除',
		'MESSAGE_TEXT' 		=> $l_confirm,

		'L_YES'				=> '是',
		'L_NO' 				=> '否',

		'S_CONFIRM_ACTION' 	=> append_sid('posting.php'),
		'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
	);

	$template->pparse('confirm_body');

	page_footer();
}
// 投票
else if ( $mode == 'vote' )
{

	if ( !empty($_POST['vote_id']) )
	{
		$vote_option_id = intval($_POST['vote_id']);

		$sql = 'SELECT vd.vote_id    
			FROM ' . VOTE_DESC_TABLE . ' vd, ' . VOTE_RESULTS_TABLE . " vr
			WHERE vd.topic_id = $topic_id 
				AND vr.vote_id = vd.vote_id 
				AND vr.vote_option_id = $vote_option_id
			GROUP BY vd.vote_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain vote data for this topic', E_USER_WARNING);
		}

		if ( $vote_info = $db->sql_fetchrow($result) )
		{
			$vote_id = $vote_info['vote_id'];

			$sql = 'SELECT * 
				FROM ' . VOTE_USERS_TABLE . "  
				WHERE vote_id = $vote_id 
					AND vote_user_id = " . $userdata['user_id'];
			if ( !($result2 = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain user vote data for this topic', E_USER_WARNING);
			}

			if ( !($row = $db->sql_fetchrow($result2)) )
			{
				$sql = 'UPDATE ' . VOTE_RESULTS_TABLE . " 
					SET vote_result = vote_result + 1 
					WHERE vote_id = $vote_id 
						AND vote_option_id = $vote_option_id";
				if ( !$db->sql_query($sql, BEGIN_TRANSACTION) )
				{
					trigger_error('Could not update poll result', E_USER_WARNING);
				}

				$sql = 'INSERT INTO ' . VOTE_USERS_TABLE . " (vote_id, vote_user_id, vote_user_ip) 
					VALUES ($vote_id, " . $userdata['user_id'] . ", '$user_ip')";
				if ( !$db->sql_query($sql, END_TRANSACTION) )
				{
					trigger_error('Could not insert user_id for poll', E_USER_WARNING);
				}

				$message = '您已经完成投票';
			}
			else
			{
				$message = '您已经投票';
			}
			$db->sql_freeresult($result2);
		}
		else
		{
			$message = '你必须指定投票选项';
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="0;url=' . append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id) . '">')
		);
		$message .=  '<br />点击 <a href="' . append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id) . '">这里</a> 返回查看主题';
		trigger_error($message);
	}
	else
	{
		redirect(append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id, true));
	}
}

// 提交中 ....
else if ( $submit || $confirm )
{
	$return_message = '';
	$return_meta = '';

	if ($sid == '' || $sid != $userdata['session_id'])
	{
		$error_msg .= '<p>错误！请重新加载页面！</p>';
	}

	if ( $mode == 'newtopic' && $board_config['captcha_in_topic'] )
	{
		$confirm_code = trim(htmlspecialchars($_POST['confirm_code']));

		if (empty($_POST['confirm_id']))
		{
			$error = TRUE;
			$error_msg .= '<p>确认代码输入无效</p>';
		}
		else
		{
			$confirm_id = htmlspecialchars($_POST['confirm_id']);
			if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
			{
				$confirm_id = '';
			}

			$sql = 'SELECT code 
				FROM ' . CONFIRM_TABLE . " 
				WHERE confirm_id = '$confirm_id' 
					AND session_id = '" . $userdata['session_id'] . "'";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not obtain confirmation code', E_USER_WARNING);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				if ($row['code'] != $confirm_code)
				{
					$error = TRUE;
					$error_msg .= '<p>确认代码输入无效</p>';
				}
				else
				{
					$sql = 'DELETE FROM ' . CONFIRM_TABLE . " 
						WHERE confirm_id = '$confirm_id' 
							AND session_id = '" . $userdata['session_id'] . "'";
					if (!$db->sql_query($sql))
					{
						trigger_error('Could not delete confirmation code', E_USER_WARNING);
					}
				}
			}
			else
			{		
				$error = TRUE;
				$error_msg .= '<p>确认代码输入无效</p>';
			}
			$db->sql_freeresult($result);
		}
	}

	$translit = ( isset($_POST['translit']) ) ? TRUE : FALSE; 
	switch ( $mode )
	{
		case 'editpost':
		case 'newtopic':
		case 'reply':
			$username = ( !empty($_POST['username']) ) ? $_POST['username'] : '';
			$subject = ( !empty($_POST['subject']) ) ? trim($_POST['subject']) : '';
			if ( $translit )
			{
				$message = ( !empty($_POST['message']) ) ?  '[rus]' . $_POST['message'] . '[/rus]' : '';
				$message .= ( !empty($_POST['smile_code']) ) ? $_POST['smile_code'] : ''; 
			}
			else
			{
				$message = ( !empty($_POST['message']) ) ?  $_POST['message'] : '';
				$message .= ( !empty($_POST['smile_code']) ) ? $_POST['smile_code'] : ''; 
			}
			$poll_title = ( isset($_POST['poll_title']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_title'] : '';
			$poll_options = ( isset($_POST['poll_option_text']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_option_text'] : '';
			$poll_length = ( isset($_POST['poll_length']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_length'] : '';
			$bbcode_uid = '';
			
			$topic_marrow = (isset($_POST['marrow']) && $is_auth['auth_marrow']) ? POST_MARROW : POST_UNMARROW;

			prepare_post($mode, $post_data, $bbcode_on, $html_on, $smilies_on, $error_msg, $username, $bbcode_uid, $subject, $message, $poll_title, $poll_options, $poll_length);

			if ( $error_msg == '' )
			{
				$topic_type = ( $topic_type != $post_data['topic_type'] && (!$is_auth['auth_sticky'] && !$is_auth['auth_announce'])) ? $post_data['topic_type'] : $topic_type;
				$message = phpbb_message_at($message);
				submit_post($mode, $post_data, $return_message, $return_meta, $forum_id, $topic_id, $post_id, $poll_id, $topic_type, $topic_marrow, $bbcode_on, $html_on, $smilies_on, $attach_sig, $bbcode_uid, str_replace("\'", "''", $username), str_replace("\'", "''", $subject), str_replace("\'", "''", $message), str_replace("\'", "''", $poll_title), $poll_options, $poll_length);
			}
			break;

		case 'delete':
		case 'poll_delete':

			if ($error_msg != '')
			{
				trigger_error($error_msg);
			}

			delete_post($mode, $post_data, $return_message, $return_meta, $forum_id, $topic_id, $post_id, $poll_id);
			break;
	}

	if ( $error_msg == '' )
	{
		if ( $mode != 'editpost' )
		{
			$user_id = ( $mode == 'reply' || $mode == 'newtopic' ) ? $userdata['user_id'] : $post_data['poster_id'];
			update_post_stats($mode, $post_data, $forum_id, $topic_id, $post_id, $user_id);
		}
		$attachment_mod['posting']->insert_attachment($post_id);

		if ($error_msg == '' && $mode != 'poll_delete')
		{
			user_notification($mode, $post_data, $post_info['topic_title'], $forum_id, $topic_id, $post_id, $notify_user);
		}

		if ( $mode == 'newtopic' || $mode == 'reply' )
		{
			$tracking_topics = ( !empty($_COOKIE[$board_config['cookie_name'] . '_t']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_t']) : array();
			$tracking_forums = ( !empty($_COOKIE[$board_config['cookie_name'] . '_f']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_f']) : array();

			if ( count($tracking_topics) + count($tracking_forums) == 100 && empty($tracking_topics[$topic_id]) )
			{
				asort($tracking_topics);
				unset($tracking_topics[key($tracking_topics)]);
			}

			$tracking_topics[$topic_id] = time();

			setcookie($board_config['cookie_name'] . '_t', serialize($tracking_topics), 0, $board_config['cookie_path'], $board_config['cookie_domain'], $board_config['cookie_secure']);
		}
		redirect($return_meta);
	}
}

if( $refresh || isset($_POST['del_poll_option']) || $error_msg != '' )
{
	$username = ( !empty($_POST['username']) ) ? htmlspecialchars(trim(stripslashes($_POST['username']))) : '';
	$subject = ( !empty($_POST['subject']) ) ? htmlspecialchars(trim(stripslashes($_POST['subject']))) : '';
	$message = ( !empty($_POST['message']) ) ? htmlspecialchars(trim(stripslashes($_POST['message']))) : '';

	$poll_title = ( !empty($_POST['poll_title']) ) ? htmlspecialchars(trim(stripslashes($_POST['poll_title']))) : '';
	$poll_length = ( isset($_POST['poll_length']) ) ? max(0, intval($_POST['poll_length'])) : 0;

	$poll_options = array();
	if ( !empty($_POST['poll_option_text']) )
	{
		foreach($_POST['poll_option_text'] as $option_id => $option_text)
		{
			if( isset($_POST['del_poll_option'][$option_id]) )
			{
				unset($poll_options[$option_id]);
			}
			else if ( !empty($option_text) ) 
			{
				$poll_options[intval($option_id)] = htmlspecialchars(trim(stripslashes($option_text)));
			}
		}
	}

	if ( isset($poll_add) && !empty($_POST['add_poll_option_text']) )
	{
		$poll_options[] = htmlspecialchars(trim(stripslashes($_POST['add_poll_option_text'])));
	}

	if ( $mode == 'newtopic' || $mode == 'reply')
	{
		$user_sig = ( $userdata['user_sig'] != '' && $board_config['allow_sig'] ) ? $userdata['user_sig'] : '';
	}
	else if ( $mode == 'editpost' )
	{
		$user_sig = ( $post_info['user_sig'] != '' && $board_config['allow_sig'] ) ? $post_info['user_sig'] : '';
		$userdata['user_sig_bbcode_uid'] = $post_info['user_sig_bbcode_uid'];
	}
	
	if( $error_msg != '' )
	{
		error_box('ERROR_BOX', $error_msg);
	}
}
else
{
	if ( $mode == 'newtopic' )
	{
		$user_sig = ( $userdata['user_sig'] != '' ) ? $userdata['user_sig'] : '';

		$message = '';
	}
	else if ( $mode == 'reply' )
	{
		$user_sig = ( $userdata['user_sig'] != '' ) ? $userdata['user_sig'] : '';

		$username = ( $userdata['session_logged_in'] ) ? $userdata['username'] : '';
		$subject = '';
		$message = '';

	}
	else if ( $mode == 'quote' || $mode == 'editpost' )
	{
		$subject = ( $post_data['first_post'] ) ? $post_info['topic_title'] : $post_info['post_subject'];
		$message = $post_info['post_text'];

		if ( $mode == 'editpost' )
		{
			$attach_sig = ( $post_info['enable_sig'] && $post_info['user_sig'] != '' ) ? TRUE : 0; 
			$user_sig = $post_info['user_sig'];

			$html_on = ( $post_info['enable_html'] ) ? true : false;
			$bbcode_on = ( $post_info['enable_bbcode'] ) ? true : false;
			$smilies_on = ( $post_info['enable_smilies'] ) ? true : false;
		}
		else
		{
			$attach_sig = ( $userdata['user_attachsig'] ) ? TRUE : 0;
			$user_sig = $userdata['user_sig'];
		}

		if ( $post_info['bbcode_uid'] != '' )
		{
			$message = preg_replace('/\:(([a-z0-9]:)?)' . $post_info['bbcode_uid'] . '/s', '', $message);
		}

		$message = str_replace('<', '&lt;', $message);
		$message = str_replace('>', '&gt;', $message);
		$message = str_replace('<br />', "\n", $message);

		if ( $mode == 'quote' )
		{
			$orig_word = array();
			$replacement_word = array();
			obtain_word_list($orig_word, $replace_word);
					
			$quote_username = ( trim($post_info['post_username']) != '' ) ? $post_info['post_username'] : $post_info['username'];
			$message = '[quote="' . $quote_username . '"]' . $message . '[/quote]';

			if ( !empty($orig_word) )
			{
				$subject = ( !empty($subject) ) ? str_replace($orig_word, $replace_word, $subject) : '';
				$message = ( !empty($message) ) ? str_replace($orig_word, $replace_word, $message) : '';
			}

			if ( !preg_match('/^Re:/', $subject) && strlen($subject) > 0 )
			{
				$subject = 'Re: ' . $subject;
			}

			$mode = 'reply';
		}
		else
		{
			$username = ( $post_info['user_id'] == ANONYMOUS && !empty($post_info['post_username']) ) ? $post_info['post_username'] : '';
		}
	}
	else if ( $mode == 'otv' )
	{
		$subject = ( $post_data['first_post'] ) ? $post_info['topic_title'] : $post_info['post_subject'];

		$otv_username = ( trim($post_info['post_username']) != '' ) ? $post_info['post_username'] : $post_info['username'];
		$message = '@' . $otv_username . ', ';

		if ( !preg_match('/^Re:/', $subject) && strlen($subject) > 0 )
		{
			$subject = 'Re: ' . $subject;
		}

		$mode = 'reply';
	}
}

if( !$userdata['session_logged_in'] || ( $mode == 'editpost' && $post_info['poster_id'] == ANONYMOUS ) )
{
	$template->assign_block_vars('switch_username_select', array());
}

if ( $userdata['session_logged_in'] && $is_auth['auth_read'] && ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm']) )
{
	if ( $mode != 'editpost' || ( $mode == 'editpost' && $post_info['poster_id'] != ANONYMOUS ) )
	{
		$template->assign_block_vars('switch_notify_checkbox', array());
	}
}

if ( $mode == 'editpost' && ( ( $is_auth['auth_delete'] && $post_data['last_post'] && ( !$post_data['has_poll'] || $post_data['edit_poll'] ) ) || $is_auth['auth_mod'] ) )
{
	$template->assign_block_vars('switch_delete_checkbox', array());
}

$topic_marrow 		= false;
$topic_type_toggle 	= '';

if ( $mode == 'newtopic' || ( $mode == 'editpost' && $post_data['first_post'] ) )
{
	$template->assign_block_vars('switch_allow_subject_on', array()); 

	if( $is_auth['auth_sticky'] )
	{
		$topic_type_toggle .= '<input type="radio" name="topictype" value="' . POST_STICKY . '"';
		if ( $post_data['topic_type'] == POST_STICKY || $topic_type == POST_STICKY )
		{
			$topic_type_toggle .= ' checked="checked"';
		}
		$topic_type_toggle .= ' /> 置顶';
	}

	if( $is_auth['auth_announce'] )
	{
		$topic_type_toggle .= '<input type="radio" name="topictype" value="' . POST_ANNOUNCE . '"';
		if ( $post_data['topic_type'] == POST_ANNOUNCE || $topic_type == POST_ANNOUNCE )
		{
			$topic_type_toggle .= ' checked="checked"';
		}
		$topic_type_toggle .= ' /> 公告';
	}

	if ($is_auth['auth_marrow'])
	{
		$template->assign_block_vars('switch_allow_marrow_on', array()); 
		$topic_marrow = (isset($post_info['topic_marrow'])) ? (($post_info['topic_marrow'] == POST_MARROW) ? true : false) : false; 
	}
}

if ( $topic_type_toggle != '' )
{
	$template->assign_block_vars('switch_type_toggle', array());
	$topic_type_toggle = '主题的类型：<br /><input type="radio" name="topictype" value="' . POST_NORMAL .'"' . ( ( $post_data['topic_type'] == POST_NORMAL || $topic_type == POST_NORMAL ) ? ' checked="checked"' : '' ) . ' />普通' . $topic_type_toggle;
}

$hidden_form_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';
$hidden_form_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

switch( $mode )
{
	case 'newtopic':
		$page_title = '发表主题';
		$hidden_form_fields .= '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';
	break;

	case 'reply':
		$page_title = '发表回复';
		$hidden_form_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';
		$template->assign_block_vars('show_topic_title', array());
	break;

	case 'editpost':
		$page_title = '编辑帖子';
		$hidden_form_fields .= '<input type="hidden" name="' . POST_POST_URL . '" value="' . $post_id . '" />';
		$template->assign_block_vars('show_topic_title', array());
	break;
}

page_header($page_title);

$template->set_filenames(array(
	'body' 		=> 'posting_body.tpl', 
	'pollbody' 	=> 'posting_poll_body.tpl')
);

if (isset($_GET[POST_TOPIC_URL]))
{
	$back_url = append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id);
}
elseif (isset($_GET[POST_POST_URL]))
{
	$back_url = append_sid('viewtopic.php?' . POST_POST_URL . '=' . $post_id . '#' . $post_id);
}
else
{
	$back_url = append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id);
}


$template->assign_vars(array(
	'FORUM_NAME' 		=> $forum_name,
	'TOPIC_TITLE' 		=> $topic_title,
	'L_POST_A' 			=> $page_title,
	'U_FORUM'			=> append_sid('forum.php'),
	'U_VIEW_TOPIC' 		=> append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id),
	'U_VIEW_FORUM' 		=> append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id),
	'U_BACK'			=> $back_url)
);

if ( $mode == 'newtopic' && $board_config['captcha_in_topic'])
{
	$sql = 'SELECT session_id 
		FROM ' . SESSIONS_TABLE; 
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not select session data', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		$confirm_sql = '';
		do
		{
			$confirm_sql .= (($confirm_sql != '') ? ', ' : '') . "'" . $row['session_id'] . "'";
		}
		while ($row = $db->sql_fetchrow($result));

		$sql = 'DELETE FROM ' .  CONFIRM_TABLE . " 
			WHERE session_id NOT IN ($confirm_sql)";
		if (!$db->sql_query($sql))
		{
			trigger_error('Could not delete stale confirm data', E_USER_WARNING);
		}
	}
	$db->sql_freeresult($result);

	$allowed_symbols = '0123456789';
	$length = 5;
		while(true){
			$code='';
			for($i=0;$i<$length;$i++){
				$code.=$allowed_symbols{mt_rand(0,strlen($allowed_symbols)-1)};
			}
			if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $code)) break;
		}

	$confirm_id = md5(uniqid($user_ip));

	$sql = 'INSERT INTO ' . CONFIRM_TABLE . " (confirm_id, session_id, code) 
		VALUES ('$confirm_id', '". $userdata['session_id'] . "', '$code')";
	if (!$db->sql_query($sql))
	{
		trigger_error('Could not insert new confirm code information', E_USER_WARNING);
	}

	unset($code);
		
	$confirm_image = '<img src="' . append_sid('ucp.php?mode=confirm&amp;id=' . $confirm_id) . '" alt="." title="验证码" />';
	$hidden_form_fields .= '<input type="hidden" name="confirm_id" value="' . $confirm_id . '" />';

	$template->assign_block_vars('switch_confirm', array());
	$template->assign_vars(array('CONFIRM_IMG' 		=> $confirm_image));
}

if ( $userdata['session_logged_in'] && $userdata['user_bb_panel'] )
{
	$template->assign_block_vars('bb_panel', array());
}

$template->assign_vars(array(
	'USERNAME' 			=> $username,
	'SUBJECT' 			=> $subject,
	'MESSAGE' 			=> $message,

	'U_VIEWTOPIC' 		=> ( $mode == 'reply' ) ? append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id . '&amp;postorder=desc') : '', 
	'U_REVIEW_TOPIC' 	=> ( $mode == 'reply' ) ? append_sid('posting.php?mode=topicreview&amp;' . POST_TOPIC_URL . '=' . $topic_id) : '', 

	'SMILES_TABLE' 		=> append_sid('rules.php?mode=faq&amp;act=smiles'),
	'BBCODE_TABLE' 		=> append_sid('rules.php?mode=faq&amp;act=bbcode'),
	'ATTACH_TABLE'		=> append_sid('rules.php?mode=faq&amp;act=attach&amp;' . POST_FORUM_URL . '=' . $forum_id),
	
	'S_MARROW_CHECKED'		=> ( $topic_marrow ) ? 'checked="checked"' : '',
	'S_HTML_CHECKED' 		=> ( !$html_on ) ? 'checked="checked"' : '', 
	'S_BBCODE_CHECKED' 		=> ( !$bbcode_on ) ? 'checked="checked"' : '', 
	'S_SMILIES_CHECKED' 	=> ( !$smilies_on ) ? 'checked="checked"' : '', 
	'S_SIGNATURE_CHECKED' 	=> ( $attach_sig ) ? 'checked="checked"' : '', 
 	'S_NOTIFY_CHECKED' 		=> ( $notify_user ) ? 'checked="checked"' : '', 
	'S_TYPE_TOGGLE' 		=> $topic_type_toggle, 
	'S_TOPIC_ID' 			=> $topic_id, 
	'S_POST_ACTION' 		=> append_sid('posting.php'),
	'S_HIDDEN_FORM_FIELDS' 	=> $hidden_form_fields)
);

if( ( $mode == 'newtopic' || ( $mode == 'editpost' && $post_data['edit_poll']) ) && $is_auth['auth_pollcreate'] )
{
	$template->assign_vars(array(
		
		'POLL_TITLE' 	=> $poll_title,
		'POLL_LENGTH' 	=> $poll_length)
	);

	if( $mode == 'editpost' && $post_data['edit_poll'] && $post_data['has_poll'])
	{
		$template->assign_block_vars('switch_poll_delete_toggle', array());
	}

	if( !empty($poll_options) )
	{
		foreach($poll_options as $option_id => $option_text)
		{
			$template->assign_block_vars('poll_option_rows', array(
				'POLL_OPTION' 		=> str_replace('"', '&quot;', $option_text), 
				'S_POLL_OPTION_NUM'	=> $option_id)
			);
		}
	}

	$template->assign_var_from_handle('POLLBOX', 'pollbody');
}

$template->pparse('body');

page_footer();
?>