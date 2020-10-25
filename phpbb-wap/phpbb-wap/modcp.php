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
include(ROOT_PATH . 'includes/functions/bbcode.php');
include(ROOT_PATH . 'includes/functions/admin.php');

if ( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
	$forum_id = (isset($_POST[POST_FORUM_URL])) ? intval($_POST[POST_FORUM_URL]) : intval($_GET[POST_FORUM_URL]);
}
else
{
	$forum_id = '';
}

if ( isset($_GET[POST_POST_URL]) || isset($_POST[POST_POST_URL]) )
{
	$post_id = (isset($_POST[POST_POST_URL])) ? intval($_POST[POST_POST_URL]) : intval($_GET[POST_POST_URL]);
}
else
{
	$post_id = '';
}

if ( isset($_GET[POST_TOPIC_URL]) || isset($_POST[POST_TOPIC_URL]) )
{
	$topic_id = (isset($_POST[POST_TOPIC_URL])) ? intval($_POST[POST_TOPIC_URL]) : intval($_GET[POST_TOPIC_URL]);
}
else
{
	$topic_id = '';
}

$confirm 	= ( isset($_POST['confirm']) ) ? TRUE : FALSE;
$start  	= get_pagination_start($board_config['posts_per_page']);

$delete	 	= ( isset($_POST['delete']) ) ? TRUE : FALSE;
$move 		= ( isset($_POST['move']) ) ? TRUE : FALSE;
$lock 		= ( isset($_POST['lock']) ) ? TRUE : FALSE;
$unlock 	= ( isset($_POST['unlock']) ) ? TRUE : FALSE;

if ( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	if ( $delete )
	{
		$mode = 'delete';
	}
	else if ( $move )
	{
		$mode = 'move';
	}
	else if ( $lock )
	{
		$mode = 'lock';
	}
	else if ( $unlock )
	{
		$mode = 'unlock';
	}
	else
	{
		$mode = '';
	}
}

if (!empty($_POST['sid']) || !empty($_GET['sid']))
{
	$sid = (!empty($_POST['sid'])) ? $_POST['sid'] : $_GET['sid'];
}
else
{
	$sid = '';
}

if ( !empty($topic_id) )
{
	$sql = "SELECT f.forum_id, f.forum_name, f.forum_topics, f.forum_money, f.forum_postcount, t.topic_poster
		FROM " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f
		WHERE t.topic_id = " . $topic_id . "
			AND f.forum_id = t.forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法查询主题数据', E_USER_WARNING);
	}

	if (!$topic_row = $db->sql_fetchrow($result))
	{
		trigger_error('主题不存在' . back_link(append_sid('index.php')), E_USER_ERROR);
	}

	$forum_topics = ( $topic_row['forum_topics'] == 0 ) ? 1 : $topic_row['forum_topics'];
	$forum_id = $topic_row['forum_id'];
	$forum_name = $topic_row['forum_name'];
	$topic_poster = $topic_row['topic_poster'];
}
else if ( !empty($forum_id) )
{
	$sql = "SELECT forum_name, forum_topics, forum_money, forum_postcount
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = " . $forum_id;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法取得论坛信息', E_USER_WARNING);
	}

	if (!$topic_row = $db->sql_fetchrow($result))
	{
		trigger_error('论坛不存在' . back_link(append_sid('index.php')), E_USER_ERROR);
	}

	$forum_topics = ( $topic_row['forum_topics'] == 0 ) ? 1 : $topic_row['forum_topics'];
	$forum_name = $topic_row['forum_name'];
}
else
{
	trigger_error('请指定帖子' . back_link(append_sid('index.php')), E_USER_ERROR);
}

$userdata = $session->start($user_ip, $forum_id);
init_userprefs($userdata);
	
if ($sid == '' || $sid != $userdata['session_id'])
{
	trigger_error('Session错误！！请重新打开页面', E_USER_ERROR);
}

if ( isset($_POST['cancel']) )
{
	if ( $topic_id )
	{
		$redirect = "viewtopic.php?" . POST_TOPIC_URL . "=$topic_id";
	}
	else if ( $forum_id )
	{
		$redirect = "viewforum.php?" . POST_FORUM_URL . "=$forum_id";
	}
	else
	{
		$redirect = "index.php";
	}

	redirect(append_sid($redirect, true));
}

$is_auth = auth(AUTH_ALL, $forum_id, $userdata);

if ( !$is_auth['auth_mod'] && !( $mode == 'lock' && $topic_poster == $userdata['user_id'] && $userdata['user_id'] != ANONYMOUS ) )
{
	trigger_error('您不是该论坛版主', E_USER_ERROR);
}

switch( $mode )
{
	case 'delete':
		if (!$is_auth['auth_delete'])
		{
			trigger_error('对不起，仅 ' . $is_auth['auth_delete_type'] . ' 可以删除贴子');
		}

		$page_title = '删除主题_版主管理面板';
		page_header($page_title);

		if ( $confirm )
		{
  			if ( empty($_POST['topic_id_list']) && empty($topic_id) )
			{
				trigger_error('您没有选中任何主题');
			}

			include(ROOT_PATH . 'includes/functions/search.php');

			$topics = ( isset($_POST['topic_id_list']) ) ? $_POST['topic_id_list'] : array($topic_id);

			$topic_id_sql = '';
			for($i = 0; $i < count($topics); $i++)
			{
				$topic_id_sql .= ( ( $topic_id_sql != '' ) ? ', ' : '' ) . intval($topics[$i]);
			}

			$sql = "SELECT topic_id 
				FROM " . TOPICS_TABLE . "
				WHERE topic_id IN ($topic_id_sql)
					AND forum_id = $forum_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get topic id information', E_USER_WARNING);
			}
			
			$topic_id_sql = '';
			while ($row = $db->sql_fetchrow($result))
			{
				$topic_id_sql .= (($topic_id_sql != '') ? ', ' : '') . intval($row['topic_id']);
			}
			$db->sql_freeresult($result);

			if ( $topic_id_sql == '')
			{
				trigger_error('您没有选择任何主题', E_USER_ERROR);
			}

			if ($topic_row['forum_postcount'] == 1)
			{
				$sql = "SELECT poster_id, COUNT(post_id) AS posts 
					FROM " . POSTS_TABLE . " 
					WHERE topic_id IN ($topic_id_sql) 
					GROUP BY poster_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not get poster id information', E_USER_ERROR);
				}

				while ( $row = $db->sql_fetchrow($result) )
				{
					$count_sql = "UPDATE " . USERS_TABLE . " 
						SET user_posts = user_posts - " . $row['posts'] . " 
						WHERE user_id = " . $row['poster_id'] . " AND user_posts >= " . $row['posts'];
					if ( !$db->sql_query($count_sql) )
					{
						trigger_error('Could not update user post count information', E_USER_WARNING);
					}
					$money = $topic_row['forum_money'] * $row['posts'];
					$count_sql_money = "UPDATE " . USERS_TABLE . "
						SET user_points = user_points - " . $money . " 
						WHERE user_id = " . $row['poster_id'] . " AND user_points >= " . $money;
					if ( !$db->sql_query($count_sql_money) )
					{
						trigger_error('Could not update user money count information', E_USER_WARNING);
					}
				}
				$db->sql_freeresult($result);
			}
			
			$sql = "SELECT post_id 
				FROM " . POSTS_TABLE . " 
				WHERE topic_id IN ($topic_id_sql)";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get post id information', E_USER_WARNING);
			}

			$post_id_sql = '';
			while ( $row = $db->sql_fetchrow($result) )
			{
				$post_id_sql .= ( ( $post_id_sql != '' ) ? ', ' : '' ) . intval($row['post_id']);
			}
			$db->sql_freeresult($result);

			$sql = "SELECT vote_id 
				FROM " . VOTE_DESC_TABLE . " 
				WHERE topic_id IN ($topic_id_sql)";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get vote id information', E_USER_WARNING);
			}

			$vote_id_sql = '';
			while ( $row = $db->sql_fetchrow($result) )
			{
				$vote_id_sql .= ( ( $vote_id_sql != '' ) ? ', ' : '' ) . $row['vote_id'];
			}
			$db->sql_freeresult($result);

			$sql = "DELETE 
				FROM " . TOPICS_TABLE . " 
				WHERE topic_id IN ($topic_id_sql) 
					OR topic_moved_id IN ($topic_id_sql)";
			if ( !$db->sql_query($sql, BEGIN_TRANSACTION) )
			{
				trigger_error('Could not delete topics', E_USER_WARNING);
			}


			if ( $post_id_sql != '' )
			{
				$sql = "DELETE 
					FROM " . POSTS_TABLE . " 
					WHERE post_id IN ($post_id_sql)";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete posts', E_USER_WARNING);
				}

				$sql = "DELETE 
					FROM " . POSTS_TEXT_TABLE . " 
					WHERE post_id IN ($post_id_sql)";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete posts text', E_USER_WARNING);
				}

				remove_search_post($post_id_sql);
			}

			if ( $vote_id_sql != '' )
			{
				$sql = "DELETE 
					FROM " . VOTE_DESC_TABLE . " 
					WHERE vote_id IN ($vote_id_sql)";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete vote descriptions', E_USER_WARNING);
				}

				$sql = "DELETE 
					FROM " . VOTE_RESULTS_TABLE . " 
					WHERE vote_id IN ($vote_id_sql)";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete vote results', E_USER_WARNING);
				}

				$sql = "DELETE 
					FROM " . VOTE_USERS_TABLE . " 
					WHERE vote_id IN ($vote_id_sql)";
				if ( !$db->sql_query($sql) )
				{
					trigger_error('Could not delete vote users', E_USER_WARNING);
				}
			}

			$sql = "DELETE 
				FROM " . TOPICS_WATCH_TABLE . " 
				WHERE topic_id IN ($topic_id_sql)";
			if ( !$db->sql_query($sql, END_TRANSACTION) )
			{
				trigger_error('Could not delete watched post list', E_USER_WARNING);
			}

			sync('forum', $forum_id);
			
			if ( !empty($topic_id) )
			{
				$redirect_page = "viewforum.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'];
				$l_redirect = '点击 <a href="' . $redirect_page . '">这里</a> 返回论坛页面';
			}
			else
			{
				$redirect_page = "modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'];
				$l_redirect = '点击 <a href="' . $redirect_page . '">这里</a> 返回版主管理面板';
			}
			
			trigger_error('主题已删除！<br />' . $l_redirect);
		}
		else
		{
			if ( empty($_POST['topic_id_list']) && empty($topic_id) )
			{
				trigger_error('您没有选中任何主题', E_USER_ERROR);
			}

			$hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

			if ( isset($_POST['topic_id_list']) )
			{
				$topics = $_POST['topic_id_list'];
				for($i = 0; $i < count($topics); $i++)
				{
					$hidden_fields .= '<input type="hidden" name="topic_id_list[]" value="' . intval($topics[$i]) . '" />';
				}
			}
			else
			{
				$hidden_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';
			}

			$template->set_filenames(array(
				'confirm' => 'confirm_body.tpl')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '删除确认',
				'MESSAGE_TEXT' 		=> '请确认是否删除主题',

				'L_YES' 			=> '是',
				'L_NO' 				=> '否',

				'S_CONFIRM_ACTION' 	=> 'modcp.php?sid=' . $userdata['session_id'],
				'S_HIDDEN_FIELDS' 	=> $hidden_fields)
			);

			$template->pparse('confirm');

			page_footer();
		}
		break;

	case 'move':
		$page_title = '版主管理面板';
		page_header($page_title);

		if ( $confirm )
		{
			if ( empty($_POST['topic_id_list']) && empty($topic_id) )
			{
				trigger_error('您没有选中任何主题', E_USER_ERROR);
			}

			$new_forum_id = intval($_POST['new_forum']);
			$old_forum_id = $forum_id;

			$sql = 'SELECT forum_id FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . $new_forum_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not select from forums table', E_USER_WARNING);
			}
			
			if (!$db->sql_fetchrow($result))
			{
				trigger_error('New forum does not exist', E_USER_WARNING);
			}

			$db->sql_freeresult($result);

			if ( $new_forum_id != $old_forum_id )
			{
				$topics = ( isset($_POST['topic_id_list']) ) ?  $_POST['topic_id_list'] : array($topic_id);

				$topic_list = '';
				for($i = 0; $i < count($topics); $i++)
				{
					$topic_list .= ( ( $topic_list != '' ) ? ', ' : '' ) . intval($topics[$i]);
				}

				$sql = "SELECT * 
					FROM " . TOPICS_TABLE . " 
					WHERE topic_id IN ($topic_list)
						AND forum_id = $old_forum_id
						AND topic_status <> " . TOPIC_MOVED;
				if ( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
				{
					trigger_error('Could not select from topic table', E_USER_WARNING);
				}

				$row = $db->sql_fetchrowset($result);
				$db->sql_freeresult($result);

				for($i = 0; $i < count($row); $i++)
				{
					$topic_id = $row[$i]['topic_id'];
					
					if ( isset($_POST['move_leave_shadow']) )
					{
						$sql = "INSERT INTO " . TOPICS_TABLE . " (forum_id, topic_title, topic_poster, topic_time, topic_status, topic_type, topic_vote, topic_views, topic_replies, topic_first_post_id, topic_last_post_id, topic_moved_id)
							VALUES ($old_forum_id, '" . addslashes(str_replace("\'", "''", $row[$i]['topic_title'])) . "', '" . str_replace("\'", "''", $row[$i]['topic_poster']) . "', " . $row[$i]['topic_time'] . ", " . TOPIC_MOVED . ", " . POST_NORMAL . ", " . $row[$i]['topic_vote'] . ", " . $row[$i]['topic_views'] . ", " . $row[$i]['topic_replies'] . ", " . $row[$i]['topic_first_post_id'] . ", " . $row[$i]['topic_last_post_id'] . ", $topic_id)";
						if ( !$db->sql_query($sql) )
						{
							trigger_error('Could not insert shadow topic', E_USER_WARNING);
						}
					}

					$sql = "UPDATE " . TOPICS_TABLE . " 
						SET forum_id = $new_forum_id  
						WHERE topic_id = $topic_id";
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update old topic', E_USER_WARNING);
					}

					$sql = "UPDATE " . POSTS_TABLE . " 
						SET forum_id = $new_forum_id 
						WHERE topic_id = $topic_id";
					if ( !$db->sql_query($sql) )
					{
						trigger_error('Could not update post topic ids', E_USER_WARNING);
					}
				}

				sync('forum', $new_forum_id);
				sync('forum', $old_forum_id);

				$message = '选中的主题已经移动<br />';

			}
			else
			{
				$message = '您没有选择任何主题<br />';
			}
			
			if ( !empty($topic_id) )
			{
				$redirect_page = "viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;sid=" . $userdata['session_id'];
				$message .= '点击 <a href="' . $redirect_page . '">这里</a> 返回主题页面';
			}
			else
			{
				$redirect_page = "modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'];
				$message .= '点击 <a href="' . $redirect_page . '">这里</a> 返回版主管理面板';
			}

			$message = $message . '<br />点击 <a href="' . "viewforum.php?" . POST_FORUM_URL . "=$old_forum_id&amp;sid=" . $userdata['session_id'] . '">这里</a> 返回旧的论坛页面';

			trigger_error($message);
		}
		else
		{
			if ( empty($_POST['topic_id_list']) && empty($topic_id) )
			{
				trigger_error('您没有选择任何主题或主题', E_USER_ERROR);
			}

			$hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

			if ( isset($_POST['topic_id_list']) )
			{
				$topics = $_POST['topic_id_list'];

				for($i = 0; $i < count($topics); $i++)
				{
					$hidden_fields .= '<input type="hidden" name="topic_id_list[]" value="' . intval($topics[$i]) . '" />';
				}
			}
			else
			{
				$hidden_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';
			}

			$template->set_filenames(array(
				'movetopic' => 'modcp/modcp_move.tpl')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '移动主题',
				'MESSAGE_TEXT' 		=> '是否移动主题？',
				
				'L_YES' 			=> '是',
				'L_NO' 				=> '否',

				'U_FORUM'			=> append_sid('forum.php'),
				'S_FORUM_SELECT' 	=> make_forum_select('new_forum', $forum_id), 
				'S_MODCP_ACTION' 	=> 'modcp.php?sid=' . $userdata['session_id'],
				'S_HIDDEN_FIELDS' 	=> $hidden_fields)
			);

			$template->pparse('movetopic');

			page_footer();
		}
		break;

	case 'lock':
		if ( empty($_POST['topic_id_list']) && empty($topic_id) )
		{
			trigger_error('您没有选中任何主题', E_USER_ERROR);
		}

		$topics = ( isset($_POST['topic_id_list']) ) ?  $_POST['topic_id_list'] : array($topic_id);

		$topic_id_sql = '';
		for($i = 0; $i < count($topics); $i++)
		{
			$topic_id_sql .= ( ( $topic_id_sql != '' ) ? ', ' : '' ) . intval($topics[$i]);
		}

		if ( $confirm )
		{
			$sql = "UPDATE " . TOPICS_TABLE . " 
				SET topic_status = " . TOPIC_LOCKED . ", topic_closed = " . intval($userdata['user_id']) . " 
				WHERE topic_id IN ($topic_id_sql) 
					AND forum_id = $forum_id
					AND topic_moved_id = 0";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not update topics table', E_USER_WARNING);
			}

			if ( !empty($topic_id) )
			{
				$redirect_page = "viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;sid=" . $userdata['session_id'];
				$message = '点击 <a href="' . $redirect_page . '">这里</a> 返回主题页面';
			}
			else
			{
				$redirect_page = "modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'];
				$message = '点击 <a href="' . $redirect_page . '">这里</a> 返回版主管理面板';
			}

			$message = $message . '<br />点击 <a href="' . "viewforum.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'] . '">这里</a> 返回论坛页面';
			trigger_error('主题已锁定<br />' . $message);
		}
		else
		{
			$page_title = '锁定主题_版主管理面板';
			page_header($page_title);

			if ( empty($_POST['topic_id_list']) && empty($topic_id) )
			{
				trigger_error('您没有选中任何主题', E_USER_ERROR);
			}

			$hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

			if ( isset($_POST['topic_id_list']) )
			{
				$topics = $_POST['topic_id_list'];
				for($i = 0; $i < count($topics); $i++)
				{
					$hidden_fields .= '<input type="hidden" name="topic_id_list[]" value="' . intval($topics[$i]) . '" />';
				}
			}
			else
			{
				$hidden_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';
			}

			$template->set_filenames(array(
				'confirm' => 'confirm_body.tpl')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '确认',
				'MESSAGE_TEXT' 		=> '请确认是否锁定主题',

				'L_YES' 			=> '是',
				'L_NO' 				=> '否',

				'S_CONFIRM_ACTION' 	=> 'modcp.php?sid=' . $userdata['session_id'],
				'S_HIDDEN_FIELDS' 	=> $hidden_fields)
			);

			$template->pparse('confirm');

			page_footer();
		}
		break;

	case 'unlock':
		if ( empty($_POST['topic_id_list']) && empty($topic_id) )
		{
			trigger_error('您没有选中任何主题', E_USER_ERROR);
		}

		$topics = ( isset($_POST['topic_id_list']) ) ?  $_POST['topic_id_list'] : array($topic_id);

		$topic_id_sql = '';
		for($i = 0; $i < count($topics); $i++)
		{
			$topic_id_sql .= ( ( $topic_id_sql != "") ? ', ' : '' ) . intval($topics[$i]);
		}

		$sql = "UPDATE " . TOPICS_TABLE . " 
			SET topic_status = " . TOPIC_UNLOCKED . " 
			WHERE topic_id IN ($topic_id_sql) 
				AND forum_id = $forum_id
				AND topic_moved_id = 0";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not update topics table', E_USER_WARNING);
		}

		if ( !empty($topic_id) )
		{
			$redirect_page = "viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;sid=" . $userdata['session_id'];
			$message = '点击 <a href="' . $redirect_page . '">这里</a> 返回主题页面';
		}
		else
		{
			$redirect_page = "modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'];
			$message = '点击 <a href="' . $redirect_page . '">这里</a> 返回版主管理面板';
		}

		$message = $message . '<br />点击 <a href="' . "viewforum.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'] . '">这里</a> 返回论坛页面';

		trigger_error('主题已解锁<br />' . $message);

		break;

	case 'split':
		
		$page_title = '分割主题_版主控制面板';
		
		page_header($page_title);

		$post_id_sql = '';

		if (isset($_POST['split_type_all']) || isset($_POST['split_type_beyond']))
		{
			$posts = $_POST['post_id_list'];

			for ($i = 0; $i < count($posts); $i++)
			{
				$post_id_sql .= (($post_id_sql != '') ? ', ' : '') . intval($posts[$i]);
			}
		}

		if ($post_id_sql != '')
		{
			$sql = "SELECT post_id 
				FROM " . POSTS_TABLE . "
				WHERE post_id IN ($post_id_sql)
					AND forum_id = $forum_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get post id information', E_USER_WARNING);
			}
			
			$post_id_sql = '';
			while ($row = $db->sql_fetchrow($result))
			{
				$post_id_sql .= (($post_id_sql != '') ? ', ' : '') . intval($row['post_id']);
			}
			$db->sql_freeresult($result);

			if ($post_id_sql == '')
			{
				trigger_error('您没有选中任何主题', E_USER_ERROR);
			}

			$sql = "SELECT post_id, poster_id, topic_id, post_time
				FROM " . POSTS_TABLE . "
				WHERE post_id IN ($post_id_sql) 
				ORDER BY post_time ASC";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not get post information', E_USER_ERROR);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				$first_poster = $row['poster_id'];
				$topic_id = $row['topic_id'];
				$post_time = $row['post_time'];

				$user_id_sql = '';
				$post_id_sql = '';
				do
				{
					$user_id_sql .= (($user_id_sql != '') ? ', ' : '') . intval($row['poster_id']);
					$post_id_sql .= (($post_id_sql != '') ? ', ' : '') . intval($row['post_id']);;
				}
				while ($row = $db->sql_fetchrow($result));

				$post_subject = trim(htmlspecialchars($_POST['subject']));
				if (empty($post_subject))
				{
					trigger_error('标题不能为空', E_USER_ERROR);
				}

				$new_forum_id = intval($_POST['new_forum_id']);
				$topic_time = time();
				
				$sql = 'SELECT forum_id FROM ' . FORUMS_TABLE . '
					WHERE forum_id = ' . $new_forum_id;
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not select from forums table', E_USER_WARNING);
				}
			
				if (!$db->sql_fetchrow($result))
				{
					trigger_error('New forum does not exist', E_USER_ERROR);
				}

				$db->sql_freeresult($result);

				$sql  = "INSERT INTO " . TOPICS_TABLE . " (topic_title, topic_poster, topic_time, forum_id, topic_status, topic_type)
					VALUES ('" . str_replace("\'", "''", $post_subject) . "', $first_poster, " . $topic_time . ", $new_forum_id, " . TOPIC_UNLOCKED . ", " . POST_NORMAL . ")";
				if (!($db->sql_query($sql, BEGIN_TRANSACTION)))
				{
					trigger_error('Could not insert new topic', E_USER_ERROR);
				}

				$new_topic_id = $db->sql_nextid();

				$sql = "UPDATE " . TOPICS_WATCH_TABLE . " 
					SET topic_id = $new_topic_id 
					WHERE topic_id = $topic_id 
						AND user_id IN ($user_id_sql)";
				if (!$db->sql_query($sql))
				{
					trigger_error('Could not update topics watch table', E_USER_WARNING);
				}

				$sql_where = (!empty($_POST['split_type_beyond'])) ? " post_time >= $post_time AND topic_id = $topic_id" : "post_id IN ($post_id_sql)";

				$sql = 	"UPDATE " . POSTS_TABLE . "
					SET topic_id = $new_topic_id, forum_id = $new_forum_id 
					WHERE $sql_where";
				if (!$db->sql_query($sql, END_TRANSACTION))
				{
					trigger_error('Could not update posts table', E_USER_WARNING);
				}

				sync('topic', $new_topic_id);
				sync('topic', $topic_id);
				sync('forum', $new_forum_id);
				sync('forum', $forum_id);

				$message = '帖子已分割<br />点击 <a href="' . "viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;sid=" . $userdata['session_id'] . '">这里</a> 返回帖子页面';
				trigger_error($message);
			}
		}
		else
		{

			$template->set_filenames(array(
				'split_body' => 'modcp/modcp_split.tpl')
			);

			$sql = "SELECT u.username, u.user_posts, p.*, pt.post_text, pt.bbcode_uid, pt.post_subject, p.post_username
				FROM " . POSTS_TABLE . " p, " . USERS_TABLE . " u, " . POSTS_TEXT_TABLE . " pt
				WHERE p.topic_id = $topic_id
					AND p.poster_id = u.user_id
					AND p.post_id = pt.post_id
				ORDER BY p.post_time ASC";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get topic/post information', E_USER_WARNING);
			}

			$s_hidden_fields = '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" /><input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" /><input type="hidden" name="mode" value="split" />';

			if( ( $total_posts = $db->sql_numrows($result) ) > 0 )
			{
				$postrow = $db->sql_fetchrowset($result);

				$template->assign_vars(array(
					'FORUM_NAME' 		=> $forum_name, 
					'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL . "=$forum_id"), 
					'U_MODCP'			=> "modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;start=$start&amp;sid=" . $userdata['session_id'],
					'U_FORUM'			=> append_sid('forum.php'),
					'S_SPLIT_ACTION' 	=> 'modcp.php?sid=' . $userdata['session_id'],
					'S_HIDDEN_FIELDS' 	=> $s_hidden_fields,
					'S_FORUM_SELECT' 	=> make_forum_select("new_forum_id", false, $forum_id))
				);

				$orig_word = array();
				$replacement_word = array();
				obtain_word_list($orig_word, $replacement_word);

				for($i = 0; $i < $total_posts; $i++)
				{
					$post_id 		= $postrow[$i]['post_id'];
					$poster_id 		= $postrow[$i]['poster_id'];
					$poster 		= '<a href="' . append_sid('ucp.php'.'?mode=viewprofile&amp;u=' . $poster_id) . '">' . $postrow[$i]['username'] . '</a>';
					$poster_posts 	= $postrow[$i]['user_posts'];

					$post_date 		= create_date($board_config['default_dateformat'], $postrow[$i]['post_time'], $board_config['board_timezone']);

					$bbcode_uid 	= $postrow[$i]['bbcode_uid'];
					$message 		= $postrow[$i]['post_text'];
					$post_subject 	= ( $postrow[$i]['post_subject'] != '' ) ? $postrow[$i]['post_subject'] : '';

					if ( !$board_config['allow_html'] )
					{
						if ( $postrow[$i]['enable_html'] )
						{
							$message = preg_replace('#(<)([\/]?.*?)(>)#is', '&lt;\\2&gt;', $message);
						}
					}

					if ( $bbcode_uid != '' )
					{
						$message = ( $board_config['allow_bbcode'] ) ? bbencode_second_pass($message, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $message);
					}

					if ( count($orig_word) )
					{
						$post_subject = str_replace($orig_word, $replacement_word, $post_subject);
						$message = str_replace($orig_word, $replacement_word, $message);
					}

					$message = make_clickable($message);

					if ( $board_config['allow_smilies'] && $postrow[$i]['enable_smilies'] )
					{
						$message = smilies_pass($message);
					}

					$message = str_replace("\n", '<br />', $message);

					$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

					$checkbox = ( $i > 0 ) ? '<input type="checkbox" name="post_id_list[]" value="' . $post_id . '" />' : '';
					
					$template->assign_block_vars('postrow', array(
						'ROW_CLASS' 		=> $row_class,
						'POSTER_NAME' 		=> $poster,
						'POSTER_POSTS' 		=> $poster_posts,
						'POST_DATE' 		=> $post_date,
						'POST_SUBJECT' 		=> $post_subject,
						'MESSAGE' 			=> $message,
						'POST_ID' 			=> $post_id,
						
						'S_SPLIT_CHECKBOX' 	=> $checkbox)
					);
				}

				$template->pparse('split_body');
			}
		}
		break;

	case 'ip':
		
		$page_title = 'IP信息_版主控制面板';
		
		page_header($page_title);

		$rdns_ip_num = ( isset($_GET['rdns']) ) ? $_GET['rdns'] : '';

		if ( !$post_id )
		{
			trigger_error('帖子不存在，请返回重试', E_USER_ERROR);
		}

		$template->set_filenames(array(
			'viewip' => 'modcp/modcp_viewip.tpl')
		);

		$sql = "SELECT poster_ip, poster_id 
			FROM " . POSTS_TABLE . " 
			WHERE post_id = $post_id
				AND forum_id = $forum_id";
				
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not get poster IP information', E_USER_WARNING);
		}
		
		if ( !($post_row = $db->sql_fetchrow($result)) )
		{
			trigger_error('帖子不存在，请返回重试', E_USER_ERROR);
		}

		$ip_this_post = decode_ip($post_row['poster_ip']);
		$ip_this_post = ( $rdns_ip_num == $ip_this_post ) ? htmlspecialchars(gethostbyaddr($ip_this_post)) : $ip_this_post;

		$poster_id = $post_row['poster_id'];

		$template->assign_vars(array(
			'IP'			=> $ip_this_post,
			'FORUM_NAME'	=> $forum_name,
			//'TOPIC_TITLE'	=> $topic_title,
			'U_FORUM'		=> append_sid('forum.php'),
			'U_VIEWFORUM'	=> append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id),
			'U_MODCP'		=> 'modcp.php?' . POST_FORUM_URL . '=' . $forum_id . '?sid=' . $userdata['session_id'],
			'U_VIEWTOPIC'	=> append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id),
			'U_LOOKUP_IP' 	=> "modcp.php?mode=ip&amp;" . POST_POST_URL . "=$post_id&amp;" . POST_TOPIC_URL . "=$topic_id&amp;rdns=$ip_this_post&amp;sid=" . $userdata['session_id'])
		);

		$sql = 'SELECT poster_ip, COUNT(*) AS postings 
			FROM ' . POSTS_TABLE . " 
			WHERE poster_id = $poster_id 
			GROUP BY poster_ip 
			ORDER BY postings DESC";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not get IP information for this user', E_USER_WARNING);
		}

		if ( $db->sql_numrows($result) )
		{
			$i = 0;
			while ( $row = $db->sql_fetchrow($result) )
			{
				if ( $row['poster_ip'] == $post_row['poster_ip'] )
				{
					$template->assign_vars(array(
						'POSTS' => $row['postings'])
					);
					continue;
				}

				$ip = decode_ip($row['poster_ip']);
				$ip = ( $rdns_ip_num == $row['poster_ip'] || $rdns_ip_num == 'all') ? htmlspecialchars(gethostbyaddr($ip)) : $ip;

				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('iprow', array( 
					'ROW_CLASS' 	=> $row_class, 
					'IP' 			=> $ip,
					'POSTS' 		=> $row['postings'],

					'U_LOOKUP_IP' 	=> "modcp.php?mode=ip&amp;" . POST_POST_URL . "=$post_id&amp;" . POST_TOPIC_URL . "=$topic_id&amp;rdns=" . $row['poster_ip'] . "&amp;sid=" . $userdata['session_id'])
				);

				$i++; 
			}
			
			if ($i == 0)
			{
				$template->assign_block_vars('not_iprow', array());
			}
		}
		else
		{
			$template->assign_block_vars('not_iprow', array());
		}

		$sql = "SELECT u.user_id, u.username, COUNT(*) as postings 
			FROM " . USERS_TABLE ." u, " . POSTS_TABLE . " p 
			WHERE p.poster_id = u.user_id 
				AND p.poster_ip = '" . $post_row['poster_ip'] . "'
			GROUP BY u.user_id, u.username
			ORDER BY postings DESC";
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not get posters information based on IP', E_USER_WARNING);
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
			$i = 0;
			while ( $row = $db->sql_fetchrow($result) )
			{
				$id = $row['user_id'];
				$username = ( $id == ANONYMOUS ) ? '匿名用户' : $row['username'];

				$row_color = '';
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('userrow', array(
					'ROW_CLASS' 		=> $row_class, 
					'USERNAME'	 		=> $username,
					'POSTS' 			=> $row['postings'],
					'U_PROFILE' 		=> ($id == ANONYMOUS) ? "modcp.php?mode=ip&amp;" . POST_POST_URL . "=" . $post_id . "&amp;" . POST_TOPIC_URL . "=" . $topic_id . "&amp;sid=" . $userdata['session_id'] : append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$id"),
					'U_SEARCHPOSTS' 	=> append_sid("search.php?search_author=" . (($id == ANONYMOUS) ? 'Anonymous' : urlencode($username)) . "&amp;showresults=topics"))
				);

				$i++; 
			}
		}

		$template->pparse('viewip');

		break;

	default:
		$page_title = '版主控制面板';
		page_header($page_title);

		page_jump();

		$template->assign_vars(array(
			'FORUM_NAME' 		=> $forum_name,
			'U_FORUM'			=> append_sid('forum.php'),
			'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL . "=$forum_id"), 
			'S_HIDDEN_FIELDS' 	=> '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" /><input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />',
			'S_MODCP_ACTION' 	=> 'modcp.php?sid=' . $userdata['session_id'])
		);

		$template->set_filenames(array(
			'body' => 'modcp/modcp_body.tpl')
		);

		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);

		$sql = 'SELECT t.*, u.username, u.user_id, p.post_time
			FROM ' . TOPICS_TABLE . ' t, ' . USERS_TABLE . ' u, ' . POSTS_TABLE . ' p
			WHERE t.forum_id = ' . $forum_id . '
				AND t.topic_poster = u.user_id
				AND p.post_id = t.topic_last_post_id
			ORDER BY t.topic_type DESC, p.post_time DESC 
			LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('Could not obtain topic information', E_USER_WARNING);
		}

		if (!$row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('not_topic', array());
		}

		while ( $row = $db->sql_fetchrow($result) )
		{
			$topic_title = '';

			if ( $row['topic_status'] == TOPIC_LOCKED )
			{
				$folder_alt = '【锁】';
			}
			else
			{
				if ( $row['topic_type'] == POST_ANNOUNCE )
				{
					$folder_alt = '【告】';
				}
				else if ( $row['topic_type'] == POST_STICKY )
				{
					$folder_alt = '【顶】';
				}
				else 
				{
					$folder_alt = '';
				}
			}

			$topic_id 		= $row['topic_id'];
			$topic_type 	= $row['topic_type'];
			$topic_status 	= $row['topic_status'];
			
			if ( $topic_type == POST_ANNOUNCE )
			{
				$topic_type = '【告】';
			}
			else if ( $topic_type == POST_STICKY )
			{
				$topic_type = '【顶】';
			}
			else if ( $topic_status == TOPIC_MOVED )
			{
				$topic_type = '【移】';
			}
			else
			{
				if ( $row['topic_status'] == TOPIC_LOCKED )
				{
					$topic_type = '锁';
				}
				else
				{
					$topic_type = '';
				}
			}
	
			if ( $row['topic_vote'] )
			{
				$topic_type .= '【投】';
			}
	
			$topic_title = $row['topic_title'];
			if ( count($orig_word) )
			{
				$topic_title = str_replace($orig_word, $replacement_word, $topic_title);
			}

			$u_view_topic = "modcp.php?mode=split&amp;" . POST_TOPIC_URL . "=$topic_id&amp;sid=" . $userdata['session_id'];
			$topic_replies = $row['topic_replies'];

			$last_post_time = create_date($board_config['default_dateformat'], $row['post_time'], $board_config['board_timezone']);
			
			$template->assign_block_vars('topicrow', array(
				'U_VIEW_TOPIC' 			=> $u_view_topic,
				'TOPIC_TYPE' 			=> $topic_type, 
				'TOPIC_TITLE' 			=> $topic_title,
				'REPLIES' 				=> $topic_replies,
				'LAST_POST_TIME' 		=> $last_post_time,
				'TOPIC_ID' 				=> $topic_id,
				'L_TOPIC_FOLDER_ALT' 	=> $folder_alt)
			);
		}

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination("modcp.php?" . POST_FORUM_URL . "=$forum_id&amp;sid=" . $userdata['session_id'], $forum_topics, $board_config['topics_per_page'], $start))
		);

		$template->pparse('body');

		break;
}

page_footer();

?>