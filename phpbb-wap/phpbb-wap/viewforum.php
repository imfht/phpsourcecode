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

if ( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
	$forum_id = ( isset($_GET[POST_FORUM_URL]) ) ? intval($_GET[POST_FORUM_URL]) : intval($_POST[POST_FORUM_URL]);
}
else if ( isset($_GET['forum']))
{
	$forum_id = intval($_GET['forum']);
}
else
{
	$forum_id = '';
}

if ( !empty($forum_id) )
{
	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = $forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain forums information', E_USER_WARNING);
	}
}
else
{
	trigger_error('论坛不存在', E_USER_ERROR);
}

if ( !($forum_row = $db->sql_fetchrow($result)) )
{
	trigger_error('论坛不存在', E_USER_ERROR);
}

$userdata = $session->start($user_ip, $forum_id);
init_userprefs($userdata);

$start = get_pagination_start($board_config['topics_per_page']);

$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_row);

if ( !$is_auth['auth_read'] || !$is_auth['auth_view'] )
{
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = POST_FORUM_URL . "=$forum_id" . ( ( isset($start) ) ? "&start=$start" : '' );
		login_back("viewforum.php?$redirect");
	}
	$message = ( !$is_auth['auth_view'] ) ? '论坛不存在' : '对不起，仅 ' . $is_auth['auth_read_type'] . ' 可以阅读主题';

	trigger_error($message, E_USER_ERROR);;
}

if ( $is_auth['auth_mod'] && $board_config['prune_enable'] )
{
	if ( $forum_row['prune_next'] < time() && $forum_row['prune_enable'] )
	{
		include(ROOT_PATH . 'includes/functions/prune.php');
		require(ROOT_PATH . 'includes/functions/admin.php');
		auto_prune($forum_id);
	}
}


$topics_count = ( $forum_row['forum_topics'] ) ? $forum_row['forum_topics'] : 1;

if (isset($_GET['marrow']))
{
	$sql = "SELECT SQL_CALC_FOUND_ROWS t.*, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_username, p2.post_username AS post_username2, p2.post_time 
		FROM " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2, " . USERS_TABLE . " u2
		WHERE t.forum_id = $forum_id
			AND t.topic_poster = u.user_id
			AND p.post_id = t.topic_first_post_id
			AND p2.post_id = t.topic_last_post_id
			AND u2.user_id = p2.poster_id 
			AND t.topic_marrow = " . POST_MARROW . " 
		ORDER BY t.topic_type DESC, t.topic_last_post_id DESC 
		LIMIT $start, ".$board_config['topics_per_page'];

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain topic information', E_USER_WARNING);
	}

	// 统计精华帖子
	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);
	
	page_header($forum_row['forum_name'] . '的精华帖子');
	
	if ($total_topics = $db->sql_numrows($result))
	{
		$i = 0;
		while( $topic_marrow = $db->sql_fetchrow($result) )
		{
			// 主题标题
			$topic_title 		= ( count($orig_word) ) ? str_replace($orig_word, $replacement_word, $topic_marrow['topic_title']) : $topic_marrow['topic_title'];
			$topic_type 		= $topic_marrow['topic_type'];
			$views				= $topic_marrow['topic_views'];
			$replies 			= $topic_marrow['topic_replies'];
			// 公告帖子
			if( $topic_type == POST_ANNOUNCE )
			{
				$topic_type = make_style_image('topic_announcement', '公告帖子', '【告】');
			}
			// 置顶帖子
			else if( $topic_type == POST_STICKY )
			{
				$topic_type = make_style_image('topic_sticky', '置顶帖子', '【顶】');
			}
			// 普通贴子
			else
			{
				$topic_type = '';		
			}

			// 投票
			if( $topic_marrow['topic_vote'] )
			{
				$topic_type = make_style_image('topic_poll', '投票帖子', '【投】');
			}
			
			// 从其他论坛移动过来的帖子
			if( $topic_marrow['topic_status'] == TOPIC_MOVED )
			{
				$topic_type = make_style_image('topic_move', '移动过来的帖子', '【移】');
				$topic_id = $topic_marrow['topic_moved_id'];
			}
			
			// 附件图标
			if (intval($topic_marrow['topic_attachment']) == 0 || (!($is_auth['auth_download'] && $is_auth['auth_view'])) || intval($board_config['disable_mod']) || $board_config['topic_icon'] == '')
			{
				$attachment_image = '';
			}
			else
			{

				$attachment_image = '<img src="' . $board_config['topic_icon'] . '" alt="附" title="带有附件的帖子"/>';
			}
			$topic_id 			= $topic_marrow['topic_id'];
			$row_class 			= (($i % 2) == 0) ? 'row1' : 'row2';
			$view_topic_url 	= append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id");
			$last_post_author 	= ( $topic_marrow['id2'] == ANONYMOUS ) ? ( ($topic_marrow['post_username2'] != '' ) ? $topic_marrow['post_username2'] . ' ' : '匿名用户' . ' ' ) :  $topic_marrow['user2']  ;
			$s_last_post 		= '<a href="' . append_sid("viewtopic.php?"  . POST_POST_URL . '=' . $topic_marrow['topic_last_post_id']) . '#' . $topic_marrow['topic_last_post_id'] . '">»</a>';
			$nomer_posta 		= $i + $start + 1;

			$template->assign_block_vars('topicrow', array(
				'ROW_CLASS'				=> $row_class,
				'TOPIC_TITLE' 			=> $topic_title,
				'TOPIC_TYPE' 			=> $topic_type,
				'REPLIES' 				=> $replies,
				'VIEWS'					=> $views,
				'TOPIC_ATTACHMENT_IMG' 	=> $attachment_image,
				'NOMER_POSTA' 			=> $nomer_posta,
				'LAST_POST_AUTHOR' 		=> $last_post_author, 
				'S_LAST_POST' 			=> $s_last_post, 
				'U_VIEW_TOPIC' 			=> $view_topic_url)
			);

			$i++;
		}
	}
	else
	{
		$template->assign_block_vars('switch_no_topics', array() );
	}
	$db->sql_freeresult($result);

	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);

	page_jump();
	
	$template->set_filenames(array(
		'body' => 'viewforum_marrow.tpl')
	);
	// 计算出查询的记录总数
	$sql = 'SELECT found_rows() AS rowcount'; 

	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法统计在线用户！', E_USER_WARNING);
	}

	$total_marrow 		= $db->sql_fetchrow($result);
	$total_all_marrow 	= $total_marrow['rowcount'];

	$template->assign_vars(array(
		'FORUM_NAME' 		=> $forum_row['forum_name'],
		'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL ."=$forum_id"),
		'U_FORUM'			=> append_sid('forum.php'),
		'PAGINATION' 		=> generate_pagination("viewforum.php?" . POST_FORUM_URL . "=$forum_id&amp;marrow&amp;", $total_all_marrow, $board_config['topics_per_page'], $start))
	);

	$template->pparse('body');

	page_footer();

}

// 获取公告帖子
$sql = "SELECT t.*, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_time, p.post_username
	FROM " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . USERS_TABLE . " u2
	WHERE t.forum_id = $forum_id 
		AND t.topic_poster = u.user_id
		AND p.post_id = t.topic_last_post_id
		AND p.poster_id = u2.user_id
		AND t.topic_type = " . POST_ANNOUNCE . " 
	ORDER BY t.topic_last_post_id DESC ";
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain topic information', E_USER_WARNING);
}

$topic_rowset = array();
$total_announcements = 0;
while( $row = $db->sql_fetchrow($result) )
{
	$topic_rowset[] = $row;
	$total_announcements++;
}

$db->sql_freeresult($result);

// 获取帖子
$sql = "SELECT t.*, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_username, p2.post_username AS post_username2, p2.post_time 
	FROM " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2, " . USERS_TABLE . " u2
	WHERE t.forum_id = $forum_id
		AND t.topic_poster = u.user_id
		AND p.post_id = t.topic_first_post_id
		AND p2.post_id = t.topic_last_post_id
		AND u2.user_id = p2.poster_id 
		AND t.topic_type <> " . POST_ANNOUNCE . " 
	ORDER BY t.topic_type DESC, t.topic_last_post_id DESC 
	LIMIT $start, ".$board_config['topics_per_page'];
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain topic information', E_USER_WARNING);
}

$total_topics = 0;
while( $row = $db->sql_fetchrow($result) )
{
	$topic_rowset[] = $row;
	$total_topics++;
}

$db->sql_freeresult($result);

$total_topics += $total_announcements;

$orig_word = array();
$replacement_word = array();
obtain_word_list($orig_word, $replacement_word);

$u_modcp = '';
if ($is_auth['auth_mod'])
{
	$u_modcp = append_sid('modcp.php?' . POST_FORUM_URL . '=' . $forum_id . '&amp;start=' . $start . '&amp;sid=' . $userdata['session_id']);
	$template->assign_block_vars('modcp', array('U_MODCP' => $u_modcp,) );
}

$template->assign_vars(array(
	'U_FORUMCP'			=> append_sid('forumcp.php?' . POST_FORUM_URL . '=' . $forum_id),
	'FORUM_ID' 			=> $forum_id,
	'FORUM_NAME' 		=> $forum_row['forum_name'],
	'U_POST_NEW_TOPIC' 	=> append_sid("posting.php?mode=newtopic&amp;" . POST_FORUM_URL . "=$forum_id"),
	'U_CLASS'			=> append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id),
	'U_MARROW'			=> append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id . '&marrow'),
	'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL ."=$forum_id"),
	'S_SEARCH_ACTION' 	=> append_sid('bbs/search_topic.php?' . POST_FORUM_URL . '=' . $forum_id))
);

define('SHOW_ONLINE', true);
$page_title = $forum_row['forum_name'];

page_header($page_title);

page_jump();

// 使用论坛的模块
require_once ROOT_PATH . 'includes/class/forum_module.php';

$forum_module = new Forum_module($forum_id);

// 显示论坛的顶部
$forum_module->display_top();

$template->set_filenames(array(
	'body' => 'viewforum_body.tpl')
);

if( $total_topics )
{
	for($i = 0; $i < $total_topics; $i++)
	{
		$topic_id 		= $topic_rowset[$i]['topic_id'];
		$topic_title 	= ( count($orig_word) ) ? str_replace($orig_word, $replacement_word, $topic_rowset[$i]['topic_title']) : $topic_rowset[$i]['topic_title'];		
		$views			= $topic_rowset[$i]['topic_views'];
		$replies 		= $topic_rowset[$i]['topic_replies'];
		$topic_type 	= $topic_rowset[$i]['topic_type'];
		//$topic_time		= $topic_rowset[$i]['topic_time'];

		// 公告帖子
		if( $topic_type == POST_ANNOUNCE )
		{
			$topic_type = make_style_image('topic_announcement', '公告帖子', '【告】');
			$row_class = 'bold';
		}
		// 置顶帖子
		else if( $topic_type == POST_STICKY )
		{
			$topic_type = make_style_image('topic_sticky', '置顶帖子', '【顶】');
			$row_class = 'bold';
		}
		// 普通贴子
		else
		{
			$topic_type = '';
			$row_class = 'medium';
		}

		// 投票
		if( $topic_rowset[$i]['topic_vote'] )
		{
			$topic_type = make_style_image('topic_poll', '投票帖子', '【投】');
		}
		
		// 从其他论坛移动过来的帖子
		if( $topic_rowset[$i]['topic_status'] == TOPIC_MOVED )
		{
			$topic_type = make_style_image('topic_move', '移动过来的帖子', '【移】');
			$topic_id = $topic_rowset[$i]['topic_moved_id'];
		}
		
		// 附件图标
		if (intval($topic_rowset[$i]['topic_attachment']) == 0 || (!($is_auth['auth_download'] && $is_auth['auth_view'])) || intval($board_config['disable_mod']) || $board_config['topic_icon'] == '')
		{
			$attachment_image = '';
		}
		else
		{

			$attachment_image = '<img src="' . $board_config['topic_icon'] . '" alt="附" title="带有附件的帖子"/>';
		}

		if ($topic_rowset[$i]['topic_marrow'] == POST_MARROW)
		{
			$topic_marrow = make_style_image('topic_marrow', '精华帖子', '【精】');
		}
		else
		{
			$topic_marrow = '';
		}

		$view_topic_url 	= append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id");
		$last_post_author 	= ( $topic_rowset[$i]['id2'] == ANONYMOUS ) ? ( ($topic_rowset[$i]['post_username2'] != '' ) ? $topic_rowset[$i]['post_username2'] . ' ' : '匿名用户' . ' ' ) :  $topic_rowset[$i]['user2']  ;
		$s_last_post 		= '<a href="' . append_sid("viewtopic.php?"  . POST_POST_URL . '=' . $topic_rowset[$i]['topic_last_post_id']) . '#' . $topic_rowset[$i]['topic_last_post_id'] . '">»</a>';
		$nomer_posta 		= $i + $start + 1;
		$row_color = ( !($i % 2) ) ? 'row1' : 'row2';

		$template->assign_block_vars('topicrow', array(
			'ROW_CLASS'				=> $row_class,
			'ROW_COLOR'				=> $row_color,
			'REPLIES' 				=> $replies,
			'VIEWS'					=> $views,
			'TOPIC_ATTACHMENT_IMG' 	=> $attachment_image,
			'TOPIC_TITLE' 			=> $topic_title,
			'TOPIC_TYPE' 			=> $topic_type,
			'TOPIC_MARROW'			=> $topic_marrow,
			'NOMER_POSTA' 			=> $nomer_posta,
			'LAST_POST_AUTHOR' 		=> $last_post_author, 
			'S_LAST_POST' 			=> $s_last_post, 
			'U_VIEW_TOPIC' 			=> $view_topic_url)
		);
	}

	$template->assign_vars(array(
		'PAGINATION' 		=> generate_pagination("viewforum.php?" . POST_FORUM_URL . "=$forum_id", $topics_count, $board_config['topics_per_page'], $start))
	);
}
else
{
	$no_topics_msg = ( $forum_row['forum_status'] == FORUM_LOCKED ) ? '该论坛已经锁定，不能发表新主题、帖子、回复主题和编辑发贴' : '论坛中还没有贴子，点击 发表新贴 链接发表贴子';
	$template->assign_vars(array(
		'L_NO_TOPICS' => $no_topics_msg)
	);

	$template->assign_block_vars('switch_no_topics', array() );

}

// 显示论坛的底部
$forum_module->display_bottom();

$template->pparse('body');

page_footer();

?>