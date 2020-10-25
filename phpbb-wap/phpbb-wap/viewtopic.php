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
require(ROOT_PATH . 'includes/attach/attachment_mod.php');
require(ROOT_PATH . 'includes/functions/bbcode.php');// Bbcode 处理
require(ROOT_PATH . 'includes/functions/post.php');// 表单处理
require(ROOT_PATH . 'includes/functions/validate.php');// 表单处理
require(ROOT_PATH . 'includes/functions/selects.php');
require(ROOT_PATH . 'includes/functions/topic_class.php');

$topic_id = $post_id = 0;

if ( isset($_GET[POST_TOPIC_URL]) )
{
	$topic_id = abs(intval($_GET[POST_TOPIC_URL]));
}
else if ( isset($_GET['topic']) )
{
	$topic_id = abs(intval($_GET['topic']));
}
if ( isset($_GET[POST_POST_URL]))
{
	$post_id = abs(intval($_GET[POST_POST_URL]));
}

$download = ( isset($_GET['download']) ) ? $_GET['download'] : '';

if ( !$topic_id && !$post_id )
{
	trigger_error('主题或帖子不存在！', E_USER_ERROR);
}

$join_sql_table	= (!$post_id) ? '' : ", " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2 ";
$join_sql 		= (!$post_id) ? "t.topic_id = $topic_id" : "p.post_id = $post_id AND t.topic_id = p.topic_id AND p2.topic_id = p.topic_id AND p2.post_id <= $post_id";
$count_sql 		= (!$post_id) ? '' : ", COUNT(p2.post_id) AS prev_posts";

$order_sql = (!$post_id) ? '' : "GROUP BY p.post_id, t.topic_id, t.topic_title, t.topic_status, t.topic_replies, t.topic_time, t.topic_type, t.topic_vote, t.topic_last_post_id, f.forum_name, f.forum_status, f.forum_id, f.auth_view, f.auth_read, f.auth_post, f.auth_reply, f.auth_edit, f.auth_delete, f.auth_sticky, f.auth_announce, f.auth_pollcreate, f.auth_vote, f.auth_attachments, f.auth_download, t.topic_attachment ORDER BY p.post_id ASC";

$sql = 'SELECT t.topic_id, t.topic_title, t.topic_poster, t.topic_status, t.topic_replies, t.topic_views, t.topic_time, t.topic_type, t.topic_marrow, t.topic_vote, t.topic_last_post_id, t.topic_closed, f.forum_name, f.forum_status, f.forum_id, f.auth_view, f.auth_read, f.auth_post, f.auth_reply, f.auth_edit, f.auth_delete, f.auth_sticky, f.auth_announce, f.auth_marrow, f.auth_pollcreate, f.auth_vote, f.auth_attachments, f.auth_download, t.topic_attachment' . $count_sql . '
	FROM ' . TOPICS_TABLE . ' t, ' . FORUMS_TABLE . ' f' . $join_sql_table . '
	WHERE ' . $join_sql . '
		AND f.forum_id = t.forum_id 
		' . $order_sql;

if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain topic information', E_USER_WARNING);
}

if ( !($forum_topic_data = $db->sql_fetchrow($result)) )
{
	trigger_error('主题不存在', E_USER_ERROR);
}

$forum_id = abs(intval($forum_topic_data['forum_id']));

// 设置phpBB-WAP的session
$userdata = $session->start($user_ip, $forum_id);
init_userprefs($userdata);

// 得到分页的$start值
$start = get_pagination_start($board_config['posts_per_page']);

// 权限
$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_topic_data);

// 下载帖子内容
if ( $download )
{
	$sql_download = ( $download != -1 ) ? ' AND p.post_id = ' . intval($download) . ' ' : '';
	
	$orig_word 			= array();
	$replacement_word 	= array();
	
	obtain_word_list($orig_word, $replacement_word);

	$sql = 'SELECT u.*, p.*,  pt.post_text, pt.post_subject, pt.bbcode_uid
		FROM ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u, ' . POSTS_TEXT_TABLE . ' pt
		WHERE p.topic_id = ' . $topic_id . $sql_download . '
			AND pt.post_id = p.post_id
			AND u.user_id = p.poster_id
			ORDER BY p.post_time ASC, p.post_id ASC';
			
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法获取创建下载文件的内容', E_USER_WARNING);
	}

	$download_file = '';

	$is_auth_read = array();

	while ( $row = $db->sql_fetchrow($result) )
	{
		$is_auth_read = auth(AUTH_ALL, $row['forum_id'], $userdata);

		$poster_id = $row['user_id'];
		$poster = ( $poster_id == ANONYMOUS ) ? '匿名用户' : $row['username'];

		$post_date = create_date($board_config['default_dateformat'], $row['post_time'], $board_config['board_timezone']);
		$post_subject = ( $row['post_subject'] != '' ) ? $row['post_subject'] : '';

		$bbcode_uid = $row['bbcode_uid'];
		$message = $row['post_text'];
		$message = strip_tags($message);
		$message = preg_replace("/\[.*?:$bbcode_uid:?.*?\]/si", '', $message);
		$message = preg_replace('/\[url\]|\[\/url\]/si', '', $message);
		$message = preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);

		$message = unprepare_message($message);
		$message = preg_replace('/&#40;/', '(', $message);
		$message = preg_replace('/&#41;/', ')', $message);
		$message = preg_replace('/&#58;/', ':', $message);
		$message = preg_replace('/&#91;/', '[', $message);
		$message = preg_replace('/&#93;/', ']', $message);
		$message = preg_replace('/&#123;/', '{', $message);
		$message = preg_replace('/&#125;/', '}', $message);

		if (count($orig_word))
		{
			$post_subject = str_replace($orig_word, $replacement_word, $post_subject);

			$message = str_replace($orig_word, $replacement_word, $message);
		}

		$break = "\n";
		$line = '---------------';
		$download_file .= $post_subject . $break.$poster . $break.$post_date . $break . $message . $break . $line;
	}

	$disp_folder = ( $download == -1 ) ? 'topic_'.$topic_id : 'post_'.$download;

	if (!$is_auth_read['auth_read'])
	{
		$download_file = '对不起，仅 ' . $is_auth_read['auth_read_type'] . ' 可阅读主题';
		$disp_folder = 'Download';
	}

	$filename = $board_config['sitename'] . '_' . $disp_folder . '_' . date('Ymd',time()) . '.txt';
	header('Content-Type: text/plain; name="'.$filename.'"');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Content-Transfer-Encoding: plain/text');
	header('Content-Length: '.strlen($download_file));
	
	print $download_file;

	exit;
}

// 检测主题的浏览和阅读权限
if( !$is_auth['auth_view'] || !$is_auth['auth_read'] )
{
	// 阅读权限开启，如果用户没有登录则跳转到登录页面
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = ($post_id) ? POST_POST_URL . '=' . $post_id : POST_TOPIC_URL . '=' . $topic_id;
		$redirect .= ($start) ? '&amp;start=' . $start : '';
		login_back("viewtopic.php?$redirect");
	}

	$message = ( !$is_auth['auth_view'] ) ? '主题不存在' : '对不起，仅 ' . $is_auth['auth_read_type'] . ' 可以阅读主题';

	trigger_error($message, E_USER_ERROR);
}

$forum_name 	= $forum_topic_data['forum_name'];// 所属论坛
$topic_title 	= $forum_topic_data['topic_title'];//主题标题
$topic_marrow	= ($forum_topic_data['topic_marrow'] == POST_MARROW) ? '【精】' : '';//
$topic_id 		= abs(intval($forum_topic_data['topic_id']));//主题ID
$topic_time 	= $forum_topic_data['topic_time'];//主题创建时间

// 关闭帖子
if ( $forum_topic_data['topic_status'] == TOPIC_LOCKED)
{
	// 由作者关闭的帖子
	if ( $forum_topic_data['topic_closed'] == $forum_topic_data['topic_poster'] )
	{
		$user_closed = get_userdata($forum_topic_data['topic_poster']);
		$topic_closed = '<p class="red">帖子已被 <a href="' . append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '='  . $forum_topic_data['topic_closed']) . '">作者（' . $user_closed['username'] . '）</a>锁定，这个主题不可以进行回复</p>';
	}
	// 其他管理人员和版主关闭的帖子
	else
	{
		$user_closed = get_userdata($forum_topic_data['topic_closed']);
		$topic_closed = '<p class="red">帖子已被 <a href="' . append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '='  . $forum_topic_data['topic_closed']) . '">管理人员（' . $user_closed['username'] . '）锁定</a>，这个主题不可以进行回复</p>';
	}
}
else
{
	$topic_closed = '';
}

if ($post_id)
{
	$start = floor(($forum_topic_data['prev_posts'] - 1) / intval($board_config['posts_per_page'])) * intval($board_config['posts_per_page']);
}

if( $userdata['session_logged_in'] )
{

	$can_watch_topic = TRUE;

	$sql = 'SELECT notify_status
		FROM ' . TOPICS_WATCH_TABLE . '
		WHERE topic_id = ' . $topic_id . '
			AND user_id = ' . $userdata['user_id'];
			
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain topic watch information', E_USER_WARNING);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		if ( isset($_GET['unwatch']) )
		{
			if ( $_GET['unwatch'] == 'topic' )
			{
				$is_watching_topic = 0;

				$sql = 'DELETE FROM ' . TOPICS_WATCH_TABLE . "
					WHERE topic_id = $topic_id
						AND user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not delete topic watch information', E_USER_WARNING);
				}
			}

			$message = '主题已停止跟踪<br />点击 <a href="' . append_sid('viewtopic.php?' . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">这里</a> 返回主题页面';
			trigger_error($message, E_USER_ERROR);
		}
		else
		{
			$is_watching_topic = TRUE;

			if ( $row['notify_status'] )
			{
				$sql = 'UPDATE ' . TOPICS_WATCH_TABLE . "
					SET notify_status = 0
					WHERE topic_id = $topic_id
						AND user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not update topic watch information', E_USER_WARNING);
				}
			}
		}
	}
	else
	{
		if ( isset($_GET['watch']) )
		{
			if ( $_GET['watch'] == 'topic' )
			{
				$is_watching_topic = TRUE;
				$sql = 'INSERT INTO ' . TOPICS_WATCH_TABLE . ' (user_id, topic_id, notify_status)
					VALUES (' . $userdata['user_id'] . ", $topic_id, 0)";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not insert topic watch information', E_USER_WARNING);
				}
			}

			$message = '跟踪主题成功<br />点击 <a href="' . append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start") . '">这里</a> 返回主题页面';
			trigger_error($message, E_USER_ERROR);
		}
		else
		{
			$is_watching_topic = 0;
		}
	}
}
else
{
	if ( isset($_GET['unwatch']) )
	{
		if ( $_GET['unwatch'] == 'topic' )
		{
			login_back("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&unwatch=topic");
		}
	}
	else
	{
		$can_watch_topic = 0;
		$is_watching_topic = 0;
	}
}

$sql = "SELECT COUNT(p.post_id) AS num_posts
	FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
	WHERE t.topic_id = $topic_id
		AND p.topic_id = t.topic_id";
		
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain limited topics count information', E_USER_WARNING);
}

$total_replies = ( $row = $db->sql_fetchrow($result) ) ? intval($row['num_posts']) : 0;

$sql = 'SELECT rank_id, rank_title, rank_min, rank_special, rank_image
	FROM ' . RANKS_TABLE;
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('获取用户头街等级信息失败！', E_USER_WARNING);
}

$ranksrow = array();
while ( $row = $db->sql_fetchrow($result) )
{
	$ranksrow[] = $row;
}
$db->sql_freeresult($result);


$sql = "SELECT u.username, u.user_id, u.user_level, u.user_posts, u.user_gender, u.user_post_leng, u.user_nic_color, u.user_allowsmile, u.user_allow_viewonline, u.user_session_time, p.*,  pt.post_text, pt.post_subject, pt.bbcode_uid, u.user_warnings ,u.user_avatar_type, u.user_allowavatar, u.user_avatar, u.user_zvanie, u.user_rank, u.user_sig 
	FROM " . POSTS_TABLE . " p, " . USERS_TABLE . " u, " . POSTS_TEXT_TABLE . " pt
	WHERE p.topic_id = $topic_id
		AND pt.post_id = p.post_id
		AND u.user_id = p.poster_id
	ORDER BY p.post_time ASC
	LIMIT $start, ".$board_config['posts_per_page'];

if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain post/user information.', E_USER_WARNING);
}

$postrow = array();
if ($row = $db->sql_fetchrow($result))
{
	$post_ids = $row['post_id'];
	do
	{
		$postrow[] = $row;
		$post_ids .= ',' . $row['post_id'];
	}
	while ($row = $db->sql_fetchrow($result));
	$db->sql_freeresult($result);

	$total_posts = count($postrow);
}
else 
{ 
	include(ROOT_PATH . 'includes/functions/admin.php'); 
	sync('topic', $topic_id); 

	trigger_error('主题没有这样的贴子', E_USER_ERROR);
}

$resync = FALSE; 
if (($forum_topic_data['topic_replies'] + 1) < ($start + count($postrow))) 
{ 
	$resync = TRUE; 
} 
elseif (($start + $board_config['posts_per_page']) > $forum_topic_data['topic_replies']) 
{ 
	$row_id = intval($forum_topic_data['topic_replies']) % intval($board_config['posts_per_page']); 

	if (($postrow[$row_id]['post_id'] != $forum_topic_data['topic_last_post_id']) || ($start + count($postrow) < $forum_topic_data['topic_replies'])) 
	{ 
		$resync = TRUE; 
	} 
} 
elseif (count($postrow) < $board_config['posts_per_page']) 
{ 
	$resync = TRUE; 
} 

if ($resync) 
{ 
	include(ROOT_PATH . 'includes/functions/admin.php'); 
	sync('topic', $topic_id); 

	$result = $db->sql_query('SELECT COUNT(post_id) AS total FROM ' . POSTS_TABLE . ' WHERE topic_id = ' . $topic_id); 
	$row = $db->sql_fetchrow($result); 
	$total_replies = $row['total'];
}

$orig_word = array();
$replacement_word = array();
obtain_word_list($orig_word, $replacement_word);

if ( count($orig_word) )
{
	$topic_title = str_replace($orig_word, $replacement_word, $topic_title);
}

$highlight_match = $highlight = '';
if (isset($_GET['highlight']))
{
	$words = explode(' ', trim(htmlspecialchars($_GET['highlight'])));

	for($i = 0; $i < count($words); $i++)
	{
		if (trim($words[$i]) != '')
		{
			$highlight_match .= (($highlight_match != '') ? '|' : '') . str_replace('*', '\w*', preg_quote($words[$i], '#'));
		}
	}
	unset($words);

	$highlight = urlencode($_GET['highlight']);
	$highlight_match = phpbb_rtrim($highlight_match, "\\");
}

$reply_topic_url = append_sid("posting.php?mode=reply&amp;" . POST_TOPIC_URL . "=$topic_id");
$view_forum_url = append_sid("viewforum.php?" . POST_FORUM_URL . "=$forum_id");

$reply_alt = ( $forum_topic_data['forum_status'] == FORUM_LOCKED || $forum_topic_data['topic_status'] == TOPIC_LOCKED ) ? '该主题已经锁定，不能编辑贴子和回复主题' : '回复';
$post_alt = ( $forum_topic_data['forum_status'] == FORUM_LOCKED ) ? '该论坛已经锁定，不能发表新主题、帖子、回复主题和编辑发贴' : '发表新帖';

if ( $userdata['session_logged_in'] )
{
	$tracking_topics = ( isset($_COOKIE[$board_config['cookie_name'] . '_t']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_t']) : array();
	$tracking_forums = ( isset($_COOKIE[$board_config['cookie_name'] . '_f']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_f']) : array();

	if ( !empty($tracking_topics[$topic_id]) && !empty($tracking_forums[$forum_id]) )
	{
		$topic_last_read = ( $tracking_topics[$topic_id] > $tracking_forums[$forum_id] ) ? $tracking_topics[$topic_id] : $tracking_forums[$forum_id];
	}
	else if ( !empty($tracking_topics[$topic_id]) || !empty($tracking_forums[$forum_id]) )
	{
		$topic_last_read = ( !empty($tracking_topics[$topic_id]) ) ? $tracking_topics[$topic_id] : $tracking_forums[$forum_id];
	}
	else
	{
		$topic_last_read = $userdata['user_lastvisit'];
	}

	if ( count($tracking_topics) >= 150 && empty($tracking_topics[$topic_id]) )
	{
		asort($tracking_topics);
		unset($tracking_topics[key($tracking_topics)]);
	}

	$tracking_topics[$topic_id] = time();

	setcookie($board_config['cookie_name'] . '_t', serialize($tracking_topics), 0, $board_config['cookie_path'], $board_config['cookie_domain'], $board_config['cookie_secure']);
}

$hidden_form_fields = '<input type="hidden" name="mode" value="reply" />';
$hidden_form_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
$hidden_form_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';

page_header($topic_title);

$template->set_filenames(array(
	'body' => 'viewtopic_body.tpl',
	'posttopic' => 'viewtopic_post.tpl')
);

$topic_mod = '';

if ( !$is_auth['auth_mod'] && ($forum_topic_data['topic_poster'] == $userdata['user_id'] && $userdata['user_id'] != ANONYMOUS) && $forum_topic_data['topic_status'] == TOPIC_UNLOCKED )
{
	$topic_mod .= "[操作]：<a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=lock&amp;sid=" . $userdata['session_id'] . '">锁定</a><br />';
}


if ( $is_auth['auth_mod'] )
{
	$topic_mod .= "[操作]：<a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=delete&amp;sid=" . $userdata['session_id'] . '">删除</a>';
	$topic_mod .= " . <a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=move&amp;sid=" . $userdata['session_id'] . '">移动</a>';
	$topic_mod .= ( $forum_topic_data['topic_status'] == TOPIC_UNLOCKED ) ? " . <a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=lock&amp;sid=" . $userdata['session_id'] . '">锁定</a>' : " . <a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=unlock&amp;sid=" . $userdata['session_id'] . '">解锁</a>';
	$topic_mod .= " . <a href=\"modcp.php?" . POST_TOPIC_URL . "=$topic_id&amp;mode=split&amp;sid=" . $userdata['session_id'] . '">分隔</a><br />';
}

$s_watching_topic = '';

$notify_user = false;
if ( $can_watch_topic && ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm']) )
{
	if ( $is_watching_topic )
	{
		$s_watching_topic = '<a href="' . append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;unwatch=topic&amp;start=$start") . '" class="buttom">停止跟踪</a><br/>';
		$notify_user = true;
	}
	else
	{
		$s_watching_topic = '<a href="' . append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;watch=topic&amp;start=$start") . '" class="buttom">跟踪主题</a><br/>';
		$notify_user = false;
	}
}

//表情选择
$smiles_select = smiles_select();

// 分页处理
$pagination = ( $highlight != '' ) ? generate_pagination("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;highlight=$highlight", $total_replies, $board_config['posts_per_page'], $start) : generate_pagination("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id", $total_replies, $board_config['posts_per_page'], $start);

$template->assign_vars(array(
	'FORUM_ID' 			=> $forum_id,
	'FORUM_NAME' 		=> $forum_name,
	'TOPIC_ID' 			=> $topic_id,
	'TOPIC_TITLE' 		=> $topic_title,
	'TOPIC_MARROW'		=> $topic_marrow,
	
	'TOPIC_REPLIES' 	=> $forum_topic_data['topic_replies'],
	'TOPIC_VIEWS' 		=> $forum_topic_data['topic_views'] + 1,

	'PAGINATION' 			=> $pagination,
	'L_POST_REPLY_TOPIC' 	=> $reply_alt,
	'SMILES_SELECT' 		=> $smiles_select,

	'S_TOPIC_LINK' 			=> POST_TOPIC_URL,
	'S_POST_DAYS_ACTION' 	=> append_sid("viewtopic.php?" . POST_TOPIC_URL . '=' . $topic_id . "&amp;start=$start"),
	'S_WATCH_TOPIC' 		=> $s_watching_topic,
 	'S_NOTIFY_CHECKED' 		=> ( $notify_user ) ? 'checked="checked"' : '',
	'S_POST_ACTION' 		=> append_sid('posting.php'),
	'S_HIDDEN_FORM_FIELDS' 	=> $hidden_form_fields,

	'U_VIEW_TOPIC' 			=> append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;start=$start&amp;highlight=$highlight"),
	'U_VIEW_FORUM' 			=> $view_forum_url,
	'U_FORUM'				=> append_sid('forum.php'),
	'U_POST_REPLY_TOPIC' 	=> $reply_topic_url)
);

$viewtopic_posting_true = false;
if ( $is_auth['auth_reply'] )
{
//	if ( ($userdata['session_logged_in'] && !$forum_topic_data['topic_status'] == TOPIC_LOCKED) || !$userdata['session_logged_in'] )
//	{
		// 展开
		if($board_config['quick_answer'] == QUICK_ANSWER_ON)
		{
			if (!$userdata['session_logged_in'])
			{
				$template->assign_block_vars('switch_username_select', array());
			}
			if ($userdata['session_logged_in'] && $userdata['user_bb_panel'])
			{
				$template->assign_block_vars('bb_panel', array());
			}
			if ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm'])
			{
				$template->assign_block_vars('switch_notify_checkbox', array());
			}
			$template->assign_var_from_handle('POSTTOPIC', 'posttopic');
			if ($userdata['user_java_otv'])
			{
				$viewtopic_posting_true = true;
				$template->assign_block_vars('user_otv', array());
			}	
		}
		else if ($board_config['quick_answer'] == QUICK_ANSWER_USER)
		{
			if ($userdata['user_quick_answer'])
			{
				if (!$userdata['session_logged_in'])
				{
					$template->assign_block_vars('switch_username_select', array());
				}
				if ($userdata['session_logged_in'] && $userdata['user_bb_panel'])
				{
					$template->assign_block_vars('bb_panel', array());
				}
				if ($userdata['user_notify_to_email'] || $userdata['user_notify_to_pm'])
				{
					$template->assign_block_vars('switch_notify_checkbox', array());
				}
				$template->assign_var_from_handle('POSTTOPIC', 'posttopic');
				if ($userdata['user_java_otv'])
				{
					$viewtopic_posting_true = true;
					$template->assign_block_vars('user_otv', array());
				}		
			}
		}
	//}

	$template->assign_block_vars('is_auth_reply', array());
}
else
{

	if ($userdata['session_logged_in'])
	{
		$template->assign_block_vars('not_auth_reply', array());
	}
	else
	{
		$template->assign_block_vars('auth_reply_login', array(
			'U_LOGIN' => append_sid('login.php'),
			'U_REGISTAR' => append_sid('ucp.php?mode=register'))
		);
	}
}

if ( !empty($forum_topic_data['topic_vote']) )
{
	$s_hidden_fields = '';

	$sql = "SELECT vd.vote_id, vd.vote_text, vd.vote_start, vd.vote_length, vr.vote_option_id, vr.vote_option_text, vr.vote_result
		FROM " . VOTE_DESC_TABLE . " vd, " . VOTE_RESULTS_TABLE . " vr
		WHERE vd.topic_id = $topic_id
			AND vr.vote_id = vd.vote_id
		ORDER BY vr.vote_option_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain vote data for this topic', E_USER_WARNING);
	}

	if ( $vote_info = $db->sql_fetchrowset($result) )
	{
		$db->sql_freeresult($result);
		$vote_options = count($vote_info);

		$vote_id = $vote_info[0]['vote_id'];
		$vote_title = $vote_info[0]['vote_text'];

		$sql = "SELECT vote_id
			FROM " . VOTE_USERS_TABLE . "
			WHERE vote_id = $vote_id
				AND vote_user_id = " . intval($userdata['user_id']);
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain user vote data for this topic', E_USER_WARNING);
		}

		$user_voted = ( $row = $db->sql_fetchrow($result) ) ? TRUE : 0;
		$db->sql_freeresult($result);

		if ( isset($_GET['vote']) || isset($_POST['vote']) )
		{
			$view_result = ( ( ( isset($_GET['vote']) ) ? $_GET['vote'] : $_POST['vote'] ) == 'viewresult' ) ? TRUE : 0;
		}
		else
		{
			$view_result = 0;
		}

		$poll_expired = ( $vote_info[0]['vote_length'] ) ? ( ( $vote_info[0]['vote_start'] + $vote_info[0]['vote_length'] < time() ) ? TRUE : 0 ) : 0;

		if ( $user_voted || $view_result || $poll_expired || !$is_auth['auth_vote'] || $forum_topic_data['topic_status'] == TOPIC_LOCKED )
		{
			$template->set_filenames(array(
				'pollbox' => 'viewtopic_poll_result.tpl')
			);

			$vote_results_sum = 0;

			for($i = 0; $i < $vote_options; $i++)
			{
				$vote_results_sum += $vote_info[$i]['vote_result'];
			}

			$vote_graphic = 0;
			$vote_graphic_max = count($images['voting_graphic']);

			for($i = 0; $i < $vote_options; $i++)
			{
				$vote_percent = ( $vote_results_sum > 0 ) ? $vote_info[$i]['vote_result'] / $vote_results_sum : 0;
				$vote_graphic_length = round($vote_percent * $board_config['vote_graphic_length']);

				$vote_graphic_img = $images['voting_graphic'][$vote_graphic];
				$vote_graphic = ($vote_graphic < $vote_graphic_max - 1) ? $vote_graphic + 1 : 0;

				if ( count($orig_word) )
				{
					$vote_info[$i]['vote_option_text'] = str_replace($orig_word, $replacement_word, $vote_info[$i]['vote_option_text']);
				}

				$template->assign_block_vars("poll_option", array(
					'POLL_OPTION_CAPTION' 	=> $vote_info[$i]['vote_option_text'],
					'POLL_OPTION_RESULT' 	=> $vote_info[$i]['vote_result'],
					'POLL_OPTION_PERCENT' 	=> sprintf("%.1d%%", ($vote_percent * 100)),

					'POLL_OPTION_IMG' 		=> $vote_graphic_img,
					'POLL_OPTION_IMG_WIDTH' => $vote_graphic_length)
				);
			}

			$template->assign_vars(array(
				'L_TOTAL_VOTES' => $lang['Total_votes'],
				'TOTAL_VOTES' => $vote_results_sum)
			);

		}
		else
		{
			$template->set_filenames(array(
				'pollbox' => 'viewtopic_poll_ballot.tpl')
			);

			for($i = 0; $i < $vote_options; $i++)
			{
				if ( count($orig_word) )
				{
					$vote_info[$i]['vote_option_text'] = str_replace($orig_word, $replacement_word, $vote_info[$i]['vote_option_text']);
				}

				$template->assign_block_vars("poll_option", array(
					'POLL_OPTION_ID' 		=> $vote_info[$i]['vote_option_id'],
					'POLL_OPTION_CAPTION' 	=> $vote_info[$i]['vote_option_text'])
				);
			}

			$template->assign_vars(array(
				'L_SUBMIT_VOTE' 	=> $lang['Submit_vote'],
				'L_VIEW_RESULTS' 	=> $lang['View_results'],

				'U_VIEW_RESULTS' 	=> append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;vote=viewresult"))
			);

			$s_hidden_fields = '<input type="hidden" name="topic_id" value="' . $topic_id . '" /><input type="hidden" name="mode" value="vote" />';
		}

		if ( count($orig_word) )
		{
			$vote_title = str_replace($orig_word, $replacement_word, $vote_title);
		}

		$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

		$template->assign_vars(array(
			'POLL_QUESTION' 	=> $vote_title,

			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields,
			'S_POLL_ACTION' 	=> append_sid("posting.php?mode=vote&amp;" . POST_TOPIC_URL . "=$topic_id"))
		);

		$template->assign_var_from_handle('POLL_DISPLAY', 'pollbox');
	}
}

init_display_post_attachments($forum_topic_data['topic_attachment']);

$sql = "UPDATE " . TOPICS_TABLE . "
	SET topic_views = topic_views + 1
	WHERE topic_id = $topic_id";
if ( !$db->sql_query($sql) )
{
	trigger_error('Could not update topic views.', E_USER_WARNING);
}

// 获取本帖子默认的专题
$default_class = default_topic_class($topic_id);

// 获取专题链接
$class_link = main_topic_class($forum_id, $default_class);

for($i = 0; $i < $total_posts; $i++)
{
	
	// 作者ID
	$poster_id = $postrow[$i]['user_id'];
	
	// 楼层
	$nomer_posta = $i + $start + 1;
	
	// 用户名颜色
	if ( $postrow[$i]['user_warnings'] == 0 )
	{
		if ( !empty($postrow[$i]['user_nic_color']) )
		{
			$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : '匿名用户' ) : '<a href="' . append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '" style="color: '.$postrow[$i]['user_nic_color'].'">' . $postrow[$i]['username'] . '</a>';
		}
		else
		{
			$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : '匿名用户' ) : '<a href="' . append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '">' . $postrow[$i]['username'] . '</a>';
		}
	} 
	else 
	{
		$poster = ( $poster_id == ANONYMOUS ) ? ( ($postrow[$i]['post_username'] != '' ) ? $postrow[$i]['post_username'] : '匿名用户' ) : '<a href="' . append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $postrow[$i]['user_id']) . '" style="color:#000000">' . $postrow[$i]['username'] . '</a>';
	}
	
	// 头街等级
	$poster_rank = '';
	$rank_image = '';
	if ( !empty($postrow[$i]['user_zvanie']) )
	{
		$poster_rank = $postrow[$i]['user_zvanie'];// 显示商店购买的个人等级
	}
	else
	{
		if ( $postrow[$i]['user_rank'] )
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_rank'] == $ranksrow[$j]['rank_id'] && $ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] ) 
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
			else
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_posts'] >= $ranksrow[$j]['rank_min'] && !$ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] )
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
	}
	
	// 发表日期
	$post_date = create_date($board_config['default_dateformat'], $postrow[$i]['post_time'], $board_config['board_timezone']);

	// 用户的帖子数
	$poster_posts = ( $postrow[$i]['user_id'] != ANONYMOUS ) ? '[' . $postrow[$i]['user_posts'] . ']' : ' ';

	// 性别
	if ( $postrow[$i]['user_gender'] == 1 )
	{
		$poster_gender = make_style_image('gender_male', '男', '男');
	} 
	elseif ( $postrow[$i]['user_gender'] == 2) 
	{
		$poster_gender = make_style_image('gender_girl', '女', '女');
	}
	else
	{
		$poster_gender = make_style_image('gender_unknown', '保密', '保密');
	}

	// 头街等级
	$poster_rank = '';
	$rank_image = '';
	if ( !empty($postrow[$i]['user_zvanie']) )
	{
		$poster_rank = $postrow[$i]['user_zvanie'];// 显示商店购买的个人等级
	}
	else
	{
		if ( $postrow[$i]['user_rank'] )
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_rank'] == $ranksrow[$j]['rank_id'] && $ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] ) 
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
			else
			{
				for($j = 0; $j < count($ranksrow); $j++)
				{
					if ( $postrow[$i]['user_posts'] >= $ranksrow[$j]['rank_min'] && !$ranksrow[$j]['rank_special'] )
					{
						$poster_rank = $ranksrow[$j]['rank_title'];
						if ( $ranksrow[$j]['rank_image'] )
						{
							$rank_image = '<img src="' . $ranksrow[$j]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0" /><br />';
							$poster_rank = '';
						}
					}
				}
			}
	}

	// 权限
	if ( $postrow[$i]['user_level'] == ADMIN )
	{
		$user_level = make_style_image('level_admin', '超级管理员');
	}
	else if ( $postrow[$i]['user_level'] == MOD ) 
	{
		$user_level = make_style_image('level_mod', '版主');
	}
	else if ( $postrow[$i]['user_level'] == USER ) 
	{
		$user_level = '';
	}
	else
	{
		$user_level = '';
	}
	
	if ( $poster_id != ANONYMOUS )
	{
		// 在线、隐身、离线
		if( $userdata['user_on_off'] == 1)
		{
			if ($postrow[$i]['user_session_time'] >= (time()-$board_config['online_time']))
			{
				if ($postrow[$i]['user_allow_viewonline'])
				{
					$online_status = make_style_image('online_online', '在线') . '<span style="color: #0fff0f">(在线)</span>';
					$online_class = '';
				}
				else if ( $is_auth['auth_mod'] || $userdata['user_id'] == $poster_id )
				{
					$online_status = make_style_image('online_hidden', '隐身'). '<span style="color: #888888">(隐身)</span>';
					$online_class = 'class = "avatar"';
				}
				else
				{
					$online_class = 'class = "avatar"';
					$online_status = make_style_image('online_offline', '离线'). '<span style="color: #b40000">(离线)</span>';
				}
			}
			else
			{
				$online_class = 'class = "avatar"';
				$online_status = make_style_image('online_offline', '离线') . '<span style="color: #b40000">(离线)</span>';
			}
		}
		else
		{
			$online_class = 'class = "avatar"';
			$online_status = '';
		}
	}
	else
	{
		$online_class = 'class = "avatar"';
		$online_status = '';
	}

	//引用
	$reply_url = append_sid("posting.php?mode=otv&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
	$reply = ( $viewtopic_posting_true ) ? '<a href="#text" onclick="otv(\'@' . $postrow[$i]['username'] . '\');">回复</a>' : '<a href="' . $reply_url . '">回复</a>';
	
	if ( ($userdata['session_logged_in'] && $userdata['user_message_quote'] && $board_config['message_quote']) || (!$userdata['session_logged_in'] && $board_config['message_quote']) )
	{
		$quote_url = append_sid("posting.php?mode=quote&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
		$quote = '|<a href="' . $quote_url . '">引用</a>';
	}

	// 编辑
	if ( ( $userdata['user_id'] == $poster_id && $is_auth['auth_edit'] ) || $is_auth['auth_mod'] )
	{
		$edit_url = append_sid("posting.php?mode=editpost&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id']);
		$edit = '|<a href="' . $edit_url . '">编辑</a>';
	}
	else
	{
		$edit = '';
	}

	// IP、删除
	if ( $is_auth['auth_mod'] )
	{

		$ip_url = "modcp.php?mode=ip&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;" . POST_TOPIC_URL . "=" . $topic_id . "&amp;sid=" . $userdata['session_id'];
		$ip = '|<a href="' . $ip_url . '">IP</a>';

		$delete_url = "posting.php?mode=delete&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
		$delpost = '|<a href="' . $delete_url . '">删除</a>';
	}
	else
	{
		$ip = '';

		if ( $userdata['user_id'] == $poster_id && $is_auth['auth_delete'] && $forum_topic_data['topic_last_post_id'] == $postrow[$i]['post_id'] )
		{
			$delete_url = "posting.php?mode=delete&amp;" . POST_POST_URL . "=" . $postrow[$i]['post_id'] . "&amp;sid=" . $userdata['session_id'];
			$delpost = '|<a href="' . $delete_url . '">删除</a>';
		}
		else
		{
			$delpost = '';
		}
	}

	// ？？？phpBB-WAP 何来 post_subject
	$post_subject = ( $postrow[$i]['post_subject'] != '' ) ? $postrow[$i]['post_subject'] : '';

	// 内容
	$message = $postrow[$i]['post_text'];
	// bbcode 的 id
	$bbcode_uid = $postrow[$i]['bbcode_uid'];

	// 过于长的文件显示 -->
	if ( (!isset($_GET[POST_POST_URL]) || (($_GET[POST_POST_URL] != $postrow[$i]['post_id']) && isset($_GET[POST_POST_URL]))) && ($userdata['user_post_leng'] > 0) && $userdata['session_logged_in'] )
	{
		$message = u2w($message);
		if ( strlen($message) > $userdata['user_post_leng'] )
		{
			$obrez = strpos($message, '', $userdata['user_post_leng']);
			$message = substr($message, 0, $obrez);
			$message .= '...<a href="' . append_sid("viewtopic.php?" .POST_POST_URL . "=" . $postrow[$i]['post_id']) . '">--&gt</a>';
		}
		$message = w2u($message);
	}

	// 判断是否允许 HTML 标签
	if ( !$board_config['allow_html'] || !$userdata['user_allowhtml'])
	{
		if ( $postrow[$i]['enable_html'] )
		{
			$message = preg_replace('#(<)([\/]?.*?)(>)#is', "&lt;\\2&gt;", $message);
		}
	}

	// bbcode id
	if ($bbcode_uid != '')
	{
		$message = ($board_config['allow_bbcode']) ? bbencode_second_pass($message, $bbcode_uid) : preg_replace("/\:$bbcode_uid/si", '', $message);
	}

	$message = make_clickable($message);

	// 表情
	if ( $board_config['allow_smilies'] )
	{
		if ( $postrow[$i]['enable_smilies'] )
		{
			$message = smilies_pass($message);
		}
	}

	// 搜索匹配突出显示
	if ($highlight_match)
	{
		$message = preg_replace('#(?!<.*)(?<!\w)(' . $highlight_match . ')(?!\w|[^<>]*>)#i', '<b style="color: red">\1</b>', $message);
	}

	// 敏感词
	if (count($orig_word))
	{
		$post_subject = str_replace($orig_word, $replacement_word, $post_subject);
		$message = str_replace($orig_word, $replacement_word, $message);
	}

	$message = str_replace("\n", "\n<br />\n", $message);
	
	$message = phpbb_message_at_link($message);
	
	//$message = preg_replace("!(@|＠)([\\x{4e00}-\\x{9fa5}A-Za-z0-9_\\-]{1,12})(\x20|&nbsp;|<|\xC2\xA0|\r|\n|\x03|\t|,|\\?|\\!|:|;|，|。|？|！|：|；|、|…|$)!ue","'\\1<a href=\"/profile.php?mode=viewprofile&amp;u=2&user='.urlencode('\\2').'\">\\2</a>'",$message);

	// 最后编辑时间
	if ( $postrow[$i]['post_edit_count'] && (($userdata['session_logged_in'] && $userdata['user_posl_red']) || (!$userdata['session_logged_in'] && $board_config['posl_red'])) )
	{
		$l_edited_by = '<p style="font-size: 10px; color: red;" class="module">由 ' . $poster . ' 最后修改于 '. create_date($board_config['default_dateformat'], $postrow[$i]['post_edit_time'], $board_config['board_timezone']) . ' 共修改了 ' . $postrow[$i]['post_edit_count'] . ' 次！</p>';
	}
	else
	{
		$l_edited_by = '';
	}
	
	// 用于风格 
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	// 签名
	$signture = ( empty($postrow[$i]['user_sig']) ) ? '这家伙很懒，什么也没留下！' : $postrow[$i]['user_sig'];

	if ($poster_id == ANONYMOUS)
	{
		$poster_info = '';
	}
	else
	{
		$poster_info = '[楼主]：<a href="' . append_sid('search.php?search_author=' . $postrow[$i]['username'] . '&mode=all_topics&ucp') . '">主题</a>';
		$poster_info .= ' <a href="' . append_sid('album.php?action=personal&user_id=' . $poster_id) . '">相册</a>';
		$poster_info .= ' <a href="' . append_sid('ucp.php?mode=viewfiles&u=' . $poster_id) . '">附件</a>';
		$poster_info .= ' <a href="' . append_sid('article.php') . '">文章</a><br />';
	}

	// 楼层名称
	switch ($nomer_posta)
	{
		case 1:
			$nomer_posta 	= '楼主';
			$class_select 	= ( $is_auth['auth_mod'] ) ? class_select($forum_id, $topic_id, $default_class) : ''; 
			$class_view		= $class_link;
			$poster_posts 	= $poster_posts;
			$poster_info	= $poster_info;
			$download_topic = ($userdata['session_logged_in']) ? '<br />您可以 <a href="' . append_sid("viewtopic.php?download=-1&amp;".POST_TOPIC_URL."=".$topic_id) . '">下载</a>  . <a href="' . append_sid('ucp.php?mode=topic_collect&action=add&' . POST_USERS_URL . '=' . $userdata['user_id'] . '&tc=' . $topic_id) . '">收藏</a> 这个主题哦' : '';
			$topic_mod		= $topic_mod;
			$topic_closed 	= $topic_closed;
		break;
		case 2:
			$nomer_posta 	= '沙发';
			$poster_posts	= $class_select = $class_view = $download_topic = '';
			$topic_mod		= '';
			$topic_closed 	= '';
			$poster_info	= '';
		break;
		case 3:
			$nomer_posta 	= '椅子';
			$poster_posts	= $class_select = $class_view = $download_topic = '';
			$topic_mod		= '';
			$topic_closed 	= '';
			$poster_info	= '';
		break;
		case 4:
			$nomer_posta 	= '板凳';
			$poster_posts	= $class_select = $class_view = $download_topic = '';
			$topic_mod		= '';
			$topic_closed 	= '';
			$poster_info	= '';
		break;
		case 5:
			$nomer_posta 	= '地板';
			$poster_posts	= $class_select = $class_view = $download_topic = '';
			$topic_mod		= '';
			$topic_closed 	= '';
			$poster_info	= '';
		break;
		default:
			$nomer_posta 	= $nomer_posta . '楼';
			$poster_posts	= $class_select = $class_view = $download_topic = '';
			$topic_mod		= '';
			$topic_closed 	= '';
			$poster_info	= '';
	}

	// 帖子中的头像
	$avatar_img = ''; 
	if ( $postrow[$i]['user_avatar_type'] && $postrow[$i]['user_allowavatar'] ) 
	{ 
		switch( $postrow[$i]['user_avatar_type'] ) 
		{ 
			case USER_AVATAR_UPLOAD: 
				$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $postrow[$i]['user_avatar'] . '" ' . $online_class . ' style="float:left;" title="' . $nomer_posta . '" alt="." width="48" hight="48" />' : make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;"'); 
			break; 
			case USER_AVATAR_REMOTE: 
				$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $postrow[$i]['user_avatar'] . '" ' . $online_class . ' style="float:left;" alt="." title="' . $nomer_posta . '" width="48" hight="48" />' : make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class .' style="float:left;"'); 
			break; 
			default:
				$avatar_img = make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;z-index:1"');
		} 
	}
	else
	{
		$avatar_img = make_style_image('topic_avatar', $nomer_posta, $nomer_posta, $online_class . ' style="float:left;z-index:1"');
	}



	$template->assign_block_vars('postrow', array(
		'DOWNLOAD_TOPIC' 	=> $download_topic,
		'S_TOPIC_ADMIN' 	=> $topic_mod,
		'TOPIC_CLOSED' 		=> $topic_closed,

		'POSTER_ID'		=> $poster_id,
		'AVATAR_IMG' 	=> $avatar_img,
		'ROW_CLASS' 	=> $row_class,
		'POSTER_NAME' 	=> $poster,
		'POSTER_POSTS' 	=> $poster_posts,

		'POSTER_INFO' 	=> $poster_info,
		
		'POSTER_ONLINE_STATUS' 	=> $online_status,
		
		'POST_DATE' 		=> $post_date,
		'POST_SUBJECT' 		=> $post_subject,
		'MESSAGE' 			=> $message,
		'EDITED_MESSAGE' 	=> $l_edited_by,
		'NOMER_POSTA' 		=> $nomer_posta,
		'POSTER_RANK' 		=> ( empty($rank_image) ) ? $poster_rank : $rank_image,
		'USER_LEVEL'		=> $user_level,
		'SIGNATURE'			=> $signture,
		'POSTER_GENDER'		=> $poster_gender,
		
		'CLASS_SELECT'		=> $class_select,
		'CLASS_VIEW'		=> $class_view,

		'EDIT' 		=> $edit,
		'REPLY' 	=> $reply,
		'QUOTE' 	=> $quote,
		'IP' 		=> $ip,
		'DELETE' 	=> $delpost,

		'U_POST_ID' 		=> $postrow[$i]['post_id'])
	);
	display_post_attachments($postrow[$i]['post_id'], $postrow[$i]['post_attachment']);
}

$template->pparse('body');

page_footer();

?>