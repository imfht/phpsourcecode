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

if (!defined('IN_PHPBB'))
{
	exit;
}

$html_entities_match = array('#&(?!(\#[0-9]+;))#', '#<#', '#>#', '#"#');
$html_entities_replace = array('&amp;', '&lt;', '&gt;', '&quot;');

$unhtml_specialchars_match = array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace = array('>', '<', '"', '&');

function prepare_message($message, $html_on, $bbcode_on, $smile_on, $bbcode_uid = 0)
{
	global $board_config, $html_entities_match, $html_entities_replace;

	$message = trim($message);

	if ($html_on)
	{
		$message = stripslashes($message);
		$html_match = '#<[^\w<]*(\w+)((?:"[^"]*"|\'[^\']*\'|[^<>\'"])+)?>#';
		$matches = array();

		$message_split = preg_split($html_match, $message);
		preg_match_all($html_match, $message, $matches);

		$message = '';

		foreach ($message_split as $part)
		{
			$tag = array(array_shift($matches[0]), array_shift($matches[1]), array_shift($matches[2]));
			$message .= preg_replace($html_entities_match, $html_entities_replace, $part) . clean_html($tag);
		}

		$message = addslashes($message);
		$message = str_replace('&quot;', '\&quot;', $message);
	}
	else
	{
		$message = preg_replace($html_entities_match, $html_entities_replace, $message);
	}

	if($bbcode_on && $bbcode_uid != '')
	{
		$message = bbencode_first_pass($message, $bbcode_uid);
	}

	return $message;
}

function unprepare_message($message)
{
	global $unhtml_specialchars_match, $unhtml_specialchars_replace;

	return preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, $message);
}

function prepare_post(&$mode, &$post_data, &$bbcode_on, &$html_on, &$smilies_on, &$error_msg, &$username, &$bbcode_uid, &$subject, &$message, &$poll_title, &$poll_options, &$poll_length)
{
	global $board_config, $userdata;

	if (!empty($username))
	{
		$username = phpbb_clean_username($username);

		if (!$userdata['session_logged_in'] || ($userdata['session_logged_in'] && $username != $userdata['username']))
		{
			include(ROOT_PATH . 'includes/functions_validate.php');

			$result = validate_username($username);
			if ($result['error'])
			{
				$error_msg .= (!empty($error_msg)) ? '<br />' . $result['error_msg'] : $result['error_msg'];
			}
		}
		else
		{
			$username = '';
		}
	}
	if (!empty($subject))
	{
		$subject = htmlspecialchars(trim($subject));
	}
	else if ($mode == 'newtopic' || ($mode == 'editpost' && $post_data['first_post']))
	{
		$error_msg .= '<p>标题不能为空</p>';
	}

	if (!empty($message))
	{
		$bbcode_uid = ($bbcode_on) ? make_bbcode_uid() : '';
		$message = prepare_message(trim($message), $html_on, $bbcode_on, $smilies_on, $bbcode_uid);

		$message_validate = bbencode_second_pass($message, $bbcode_uid);
		$message_validate = strip_tags($message_validate);
		$message_validate = trim($message_validate);
		if (empty($message_validate))
		{
			$error_msg .= '<p>内容不能为空</p>';
		}
	}
	else if ($mode != 'delete' && $mode != 'poll_delete') 
	{
		$error_msg .= '<p>内容不能为空</p>';
	}

	if ($mode == 'newtopic' || ($mode == 'editpost' && $post_data['first_post']))
	{
		$poll_length = (isset($poll_length)) ? max(0, intval($poll_length)) : 0;

		if (!empty($poll_title))
		{
			$poll_title = htmlspecialchars(trim($poll_title));
		}

		if(!empty($poll_options))
		{
			$temp_option_text = array();
			foreach($poll_options as $option_id => $option_text)
			{
				$option_text = trim($option_text);
				if (!empty($option_text))
				{
					$temp_option_text[intval($option_id)] = htmlspecialchars($option_text);
				}
			}
			$option_text = $temp_option_text;

			if (count($poll_options) < 2)
			{
				$error_msg .= '<p>您必须输入两个或更多的投票选项</p>';
			}
			else if (count($poll_options) > $board_config['max_poll_options']) 
			{
				$error_msg .= '<p>您必须输入两个或更多的投票选项</p>';
			}
			else if ($poll_title == '')
			{
				$error_msg .= '<p>您必须输入投票标题</p>';
			}
		}
	}

	return;
}

function submit_post($mode, &$post_data, &$message, &$meta, &$forum_id, &$topic_id, &$post_id, &$poll_id, &$topic_type, &$topic_marrow, &$bbcode_on, &$html_on, &$smilies_on, &$attach_sig, &$bbcode_uid, $post_username, $post_subject, $post_message, $poll_title, &$poll_options, &$poll_length)
{
	global $board_config, $db;
	global $userdata, $user_ip;

	include(ROOT_PATH . 'includes/functions/search.php');

	$current_time = time();

	if ($mode == 'newtopic' || $mode == 'reply' || $mode == 'editpost') 
	{
		$where_sql = ($userdata['user_id'] == ANONYMOUS) ? "poster_ip = '$user_ip'" : 'poster_id = ' . $userdata['user_id'];
		$sql = "SELECT MAX(post_time) AS last_post_time
			FROM " . POSTS_TABLE . "
			WHERE $where_sql";
		if ($result = $db->sql_query($sql))
		{
			if ($row = $db->sql_fetchrow($result))
			{
				if (intval($row['last_post_time']) > 0 && ($current_time - intval($row['last_post_time'])) < intval($board_config['flood_interval']))
				{
					trigger_error('您不能马上发表第二条信息，因为小于发表两条信息所必须最小间隔时间，请稍候重试', E_USER_ERROR);
				}
			}
		}
		$where_sql = ($userdata['user_id'] == ANONYMOUS) ? "poster_ip = '$user_ip'" : 'poster_id = ' . $userdata['user_id'];
		$sql = "SELECT MAX(post_edit_time) AS last_post_time
			FROM " . POSTS_TABLE . "
			WHERE $where_sql";
		if ($result = $db->sql_query($sql))
		{
			if ($row = $db->sql_fetchrow($result))
			{
				if (intval($row['last_post_time']) > 0 && ($current_time - intval($row['last_post_time'])) < intval($board_config['flood_interval']))
				{
					trigger_error('您不能马上发表第二条信息，因为小于发表两条信息所必须最小间隔时间，请稍候重试', E_USER_ERROR);
				}
			}
		}
	}

	if ($mode == 'editpost')
	{
		remove_search_post($post_id);
	}

	if ($mode == 'newtopic' || ($mode == 'editpost' && $post_data['first_post']))
	{
		$topic_vote = (!empty($poll_title) && count($poll_options) >= 2) ? 1 : 0;

		$sql  = ($mode != "editpost") ? "INSERT INTO " . TOPICS_TABLE . " (topic_title, topic_poster, topic_time, forum_id, topic_status, topic_type, topic_marrow, topic_vote) VALUES ('$post_subject', " . $userdata['user_id'] . ", $current_time, $forum_id, " . TOPIC_UNLOCKED . ", $topic_type, $topic_marrow, $topic_vote)" : "UPDATE " . TOPICS_TABLE . " SET topic_title = '$post_subject', topic_type = $topic_type, topic_marrow = $topic_marrow " . (($post_data['edit_vote'] || !empty($poll_title)) ? ", topic_vote = " . $topic_vote : "") . " WHERE topic_id = $topic_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in posting', E_USER_WARNING);
		}

		if ($mode == 'newtopic')
		{
			$topic_id = $db->sql_nextid();
		}
	}

	$edited_sql = ($mode == 'editpost' && !$post_data['last_post'] && $post_data['poster_post']) ? ", post_edit_time = $current_time, post_edit_count = post_edit_count + 1 " : "";
	$sql = ($mode != "editpost") ? "INSERT INTO " . POSTS_TABLE . " (topic_id, forum_id, poster_id, post_username, post_time, poster_ip, enable_bbcode, enable_html, enable_smilies, enable_sig) VALUES ($topic_id, $forum_id, " . $userdata['user_id'] . ", '$post_username', $current_time, '$user_ip', $bbcode_on, $html_on, $smilies_on, $attach_sig)" : "UPDATE " . POSTS_TABLE . " SET post_username = '$post_username', enable_bbcode = $bbcode_on, enable_html = $html_on, enable_smilies = $smilies_on, enable_sig = $attach_sig" . $edited_sql . " WHERE post_id = $post_id";
	if (!$db->sql_query($sql, BEGIN_TRANSACTION))
	{
		trigger_error('Error in posting', E_USER_WARNING);
	}

	if ($mode != 'editpost')
	{
		$post_id = $db->sql_nextid();
	}

	$sql = ($mode != 'editpost') ? "INSERT INTO " . POSTS_TEXT_TABLE . " (post_id, post_subject, bbcode_uid, post_text) VALUES ($post_id, '" . $db->sql_escape($post_subject) . "', '$bbcode_uid', '" . $db->sql_escape($post_message) . "')" : "UPDATE " . POSTS_TEXT_TABLE . " SET post_text = '" . $db->sql_escape($post_message) . "',  bbcode_uid = '$bbcode_uid', post_subject = '" . $db->sql_escape($post_subject) . "' WHERE post_id = $post_id";
	if (!$db->sql_query($sql))
	{
		trigger_error('Error in posting', E_USER_WARNING);
	}
	if ($mode == 'editpost' && $post_data['poster_id'] != $userdata['user_id'])
	{
		$sql = 'UPDATE ' . POSTS_TABLE . ' 
			SET post_locked = ' . $post_data['post_locked'] . ' 
			WHERE post_id = ' . $post_id;
		if ($db->sql_query($sql))
		{
			trigger_error('无法更新论坛状态', E_USER_WARNING);
		}
	}
	add_search_words('single', $post_id, stripslashes($post_message), stripslashes($post_subject));

	if (($mode == 'newtopic' || ($mode == 'editpost' && $post_data['edit_poll'])) && !empty($poll_title) && count($poll_options) >= 2)
	{
		$sql = (!$post_data['has_poll']) ? "INSERT INTO " . VOTE_DESC_TABLE . " (topic_id, vote_text, vote_start, vote_length) VALUES ($topic_id, '$poll_title', $current_time, " . ($poll_length * 86400) . ")" : "UPDATE " . VOTE_DESC_TABLE . " SET vote_text = '$poll_title', vote_length = " . ($poll_length * 86400) . " WHERE topic_id = $topic_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in posting', E_USER_WARNING);
		}

		$delete_option_sql = '';
		$old_poll_result = array();
		if ($mode == 'editpost' && $post_data['has_poll'])
		{
			$sql = "SELECT vote_option_id, vote_result  
				FROM " . VOTE_RESULTS_TABLE . " 
				WHERE vote_id = $poll_id 
				ORDER BY vote_option_id ASC";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not obtain vote data results for this topic', E_USER_WARNING);
			}

			while ($row = $db->sql_fetchrow($result))
			{
				$old_poll_result[$row['vote_option_id']] = $row['vote_result'];

				if (!isset($poll_options[$row['vote_option_id']]))
				{
					$delete_option_sql .= ($delete_option_sql != '') ? ', ' . $row['vote_option_id'] : $row['vote_option_id'];
				}
			}
		}
		else
		{
			$poll_id = $db->sql_nextid();
		}

		@reset($poll_options);

		$poll_option_id = 1;
		foreach ($poll_options as $option_id => $option_text)
		{
			if (!empty($option_text))
			{
				$option_text = str_replace("\'", "''", htmlspecialchars($option_text));
				$poll_result = ($mode == "editpost" && isset($old_poll_result[$option_id])) ? $old_poll_result[$option_id] : 0;

				$sql = ($mode != "editpost" || !isset($old_poll_result[$option_id])) ? "INSERT INTO " . VOTE_RESULTS_TABLE . " (vote_id, vote_option_id, vote_option_text, vote_result) VALUES ($poll_id, $poll_option_id, '$option_text', $poll_result)" : "UPDATE " . VOTE_RESULTS_TABLE . " SET vote_option_text = '$option_text', vote_result = $poll_result WHERE vote_option_id = $option_id AND vote_id = $poll_id";
				if (!$db->sql_query($sql))
				{
					trigger_error('Error in posting', E_USER_WARNING);
				}
				$poll_option_id++;
			}
		}

		if ($delete_option_sql != '')
		{
			$sql = "DELETE FROM " . VOTE_RESULTS_TABLE . " 
				WHERE vote_option_id IN ($delete_option_sql) 
					AND vote_id = $poll_id";
			if (!$db->sql_query($sql))
			{
				trigger_error('Error deleting pruned poll options', E_USER_WARNING);
			}
		}
	}
	$meta = append_sid('viewtopic.php?' . POST_POST_URL . '=' . $post_id, true); 
	$message = '您的评论已经成功发表<br /><a href="' . append_sid('viewtopic.php?' . POST_POST_URL . '=' . $post_id) . '#' . $post_id . '">&lt;--</a>';

	return false;
}

function update_post_stats(&$mode, &$post_data, &$forum_id, &$topic_id, &$post_id, &$user_id)
{
	global $board_config, $db;

	$sql = "SELECT * FROM " . FORUMS_TABLE . "
		WHERE forum_id = $forum_id";
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error in deleting post', E_USER_WARNING);
	}
	$row = $db->sql_fetchrow($result);
	$money = $row['forum_money'];
	$forum_postcount = $row['forum_postcount'];

	$sign = ($mode == 'delete') ? '- 1' : '+ 1';
	$sign_money = ($mode == 'delete') ? '- ' . $money : '+ ' . $money;
	$forum_update_sql = "forum_posts = forum_posts $sign";
	$topic_update_sql = '';

	if ($mode == 'delete')
	{
		if ($post_data['last_post'])
		{
			if ($post_data['first_post'])
			{
				$forum_update_sql .= ', forum_topics = forum_topics - 1';
			}
			else
			{

				$topic_update_sql .= 'topic_replies = topic_replies - 1';

				$sql = "SELECT MAX(post_id) AS last_post_id
					FROM " . POSTS_TABLE . " 
					WHERE topic_id = $topic_id";
				if (!($result = $db->sql_query($sql)))
				{
					trigger_error('Error in deleting post', E_USER_WARNING);
				}

				if ($row = $db->sql_fetchrow($result))
				{
					$topic_update_sql .= ', topic_last_post_id = ' . $row['last_post_id'];
				}
			}

			if ($post_data['last_topic'])
			{
				$sql = "SELECT MAX(post_id) AS last_post_id
					FROM " . POSTS_TABLE . " 
					WHERE forum_id = $forum_id"; 
				if (!($result = $db->sql_query($sql)))
				{
					trigger_error('Error in deleting post', E_USER_WARNING);
				}

				if ($row = $db->sql_fetchrow($result))
				{
					$forum_update_sql .= ($row['last_post_id']) ? ', forum_last_post_id = ' . $row['last_post_id'] : ', forum_last_post_id = 0';
				}
			}
		}
		else if ($post_data['first_post']) 
		{
			$sql = "SELECT MIN(post_id) AS first_post_id
				FROM " . POSTS_TABLE . " 
				WHERE topic_id = $topic_id";
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Error in deleting post', E_USER_WARNING);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				$topic_update_sql .= 'topic_replies = topic_replies - 1, topic_first_post_id = ' . $row['first_post_id'];
			}
		}
		else
		{
			$topic_update_sql .= 'topic_replies = topic_replies - 1';
		}
	}
	else if ($mode != 'poll_delete')
	{
		$forum_update_sql .= ", forum_last_post_id = $post_id" . (($mode == 'newtopic') ? ", forum_topics = forum_topics $sign" : ""); 
		$topic_update_sql = "topic_last_post_id = $post_id" . (($mode == 'reply') ? ", topic_replies = topic_replies $sign" : ", topic_first_post_id = $post_id");
	}
	else 
	{
		$topic_update_sql .= 'topic_vote = 0';
	}

	if ($mode != 'poll_delete')
	{
		$sql = "UPDATE " . FORUMS_TABLE . " SET 
			$forum_update_sql 
			WHERE forum_id = $forum_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in posting', E_USER_WARNING);
		}
	}

	if ($topic_update_sql != '')
	{
		$sql = "UPDATE " . TOPICS_TABLE . " SET 
			$topic_update_sql 
			WHERE topic_id = $topic_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in posting', E_USER_WARNING);
		}
	}

	if ($mode != 'poll_delete')
	{
		if ($forum_postcount == 1)
		{
			$sql = "SELECT user_posts, user_points FROM " . USERS_TABLE . " WHERE user_id = $user_id";
			$result = $db->sql_query($sql); 
			$bug = $db->sql_fetchrow($result); 
			if( ($bug['user_posts'] == 0) && ($sign == '- 1') ) $sign = '';
			if( ($bug['user_points'] < $money) && ($sign_money == '- '.$money) ) $sign_money = '';
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_posts = user_posts " . $sign . ", user_points = user_points " . $sign_money . " 
				WHERE user_id = $user_id";
			if (!$db->sql_query($sql, END_TRANSACTION))
			{
				trigger_error('Error in posting', E_USER_WARNING);
			}
		}
	}

	return;
}

function delete_post($mode, &$post_data, &$message, &$meta, &$forum_id, &$topic_id, &$post_id, &$poll_id)
{
	global $board_config, $db;
	global $userdata, $user_ip;

	if ($mode != 'poll_delete')
	{
		include(ROOT_PATH . 'includes/functions/search.php');

		$sql = "DELETE FROM " . POSTS_TABLE . " 
			WHERE post_id = $post_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in deleting post', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . POSTS_TEXT_TABLE . " 
			WHERE post_id = $post_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in deleting post', E_USER_WARNING);
		}

		if ($post_data['last_post'])
		{
			if ($post_data['first_post'])
			{
				$forum_update_sql .= ', forum_topics = forum_topics - 1';
				$sql = "DELETE FROM " . TOPICS_TABLE . " 
					WHERE topic_id = $topic_id 
						OR topic_moved_id = $topic_id";
				if (!$db->sql_query($sql))
				{
					trigger_error('Error in deleting post', E_USER_WARNING);
				}

				$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
					WHERE topic_id = $topic_id";
				if (!$db->sql_query($sql))
				{
					trigger_error('Error in deleting post', E_USER_WARNING);
				}
			}
		}

		remove_search_post($post_id);
	}

	if ($mode == 'poll_delete' || ($mode == 'delete' && $post_data['first_post'] && $post_data['last_post']) && $post_data['has_poll'] && $post_data['edit_poll'])
	{
		$sql = "DELETE FROM " . VOTE_DESC_TABLE . " 
			WHERE topic_id = $topic_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in deleting poll', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . VOTE_RESULTS_TABLE . " 
			WHERE vote_id = $poll_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in deleting poll', E_USER_WARNING);
		}

		$sql = "DELETE FROM " . VOTE_USERS_TABLE . " 
			WHERE vote_id = $poll_id";
		if (!$db->sql_query($sql))
		{
			trigger_error('Error in deleting poll', E_USER_WARNING);
		}
	}

	if ($mode == 'delete' && $post_data['first_post'] && $post_data['last_post'])
	{
		$meta = append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id, true);
		$message = '删除完成';
	}
	else
	{
		$meta = append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id, true);
		$message = (($mode == 'poll_delete') ? '您的投票已经成功删除' : '删除完成') . '<br />点击 <a href="' . append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id) . '">这里</a> 返回帖子页面';
	}

	$message .=  '<br />点击 <a href="' . append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $forum_id) . '">这里</a> 返回论坛';

	return;
}

function user_notification($mode, &$post_data, &$topic_title, &$forum_id, &$topic_id, &$post_id, &$notify_user)
{
	global $board_config, $db;
	global $userdata, $user_ip;

	$current_time = time();

	if ($mode != 'delete')
	{
		if ($mode == 'reply')
		{
			$sql = "SELECT ban_userid 
				FROM " . BANLIST_TABLE;
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not obtain banlist', E_USER_WARNING);
			}

			$user_id_sql = '';
			while ($row = $db->sql_fetchrow($result))
			{
				if (isset($row['ban_userid']) && !empty($row['ban_userid']))
				{
					$user_id_sql .= ', ' . $row['ban_userid'];
				}
			}

			$sql = 'SELECT u.user_id, u.user_email, u.user_notify_to_email, u.user_notify_to_pm 
				FROM ' . TOPICS_WATCH_TABLE . ' tw, ' . USERS_TABLE . ' u 
				WHERE tw.topic_id = ' . $topic_id . '  
					AND tw.user_id NOT IN (' . $userdata['user_id'] . ', ' . ANONYMOUS . $user_id_sql . ') 
					AND tw.notify_status = ' . TOPIC_WATCH_UN_NOTIFIED . ' 
					AND u.user_id = tw.user_id';
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not obtain list of topic watchers', E_USER_WARNING);
			}

			$update_watched_sql = '';
			$bcc_list_ary = array();

			$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
			$script_name = ($script_name != '') ? $script_name . '/viewtopic.php' : 'viewtopic.php';
			$server_name = trim($board_config['server_name']);
			$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://';
			$server_port = ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) . '/' : '/';

			$orig_word = array();
			$replacement_word = array();
			obtain_word_list($orig_word, $replacement_word);

			$topic_title = (count($orig_word)) ? str_replace($orig_word, $replacement_word, unprepare_message($topic_title)) : unprepare_message($topic_title);
			$topic_url = $server_protocol . $server_name . $server_port . $script_name . '?' . POST_POST_URL . "=$post_id";
			$topic_stop_watching_url =  $server_protocol . $server_name . $server_port . $script_name . '?' . POST_TOPIC_URL . "=$topic_id&unwatch=topic";
			$notify_pm_subject = '回帖通知:' . $topic_title;
			$notify_pm_msg = '您好！您收到此消息，因为您设置了浏览主题 “' . $topic_title . '” 用信息通知我的功能。您可以点击链接： ' . $topic_url . ' 转到主题页面。如果您不想再跟踪这个主题，您可以点击链接： ' . $topic_stop_watching_url;
			
			if ($row = $db->sql_fetchrow($result))
			{
				@set_time_limit(60);

				do
				{
					if ( $row['user_notify_to_pm'] )
					{
						$to_user_id = $row['user_id'];
						$sql_pm = "SELECT COUNT(privmsgs_id) AS inbox_items, MIN(privmsgs_date) AS oldest_post_time 
							FROM " . PRIVMSGS_TABLE . " 
							WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
									OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "  
									OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) 
								AND privmsgs_to_userid = " . $to_user_id;
						if ( !($result_pm = $db->sql_query($sql_pm)) )
						{
							trigger_error('对不起，您输入的用户不存在', E_USER_ERROR);
						}

						if ( $inbox_info = $db->sql_fetchrow($result_pm) )
						{
							if ($board_config['max_inbox_privmsgs'] && $inbox_info['inbox_items'] >= $board_config['max_inbox_privmsgs'])
							{
								$sql_pm = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . " 
									WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
											OR privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
											OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . "  ) 
										AND privmsgs_date = " . $inbox_info['oldest_post_time'] . " 
										AND privmsgs_to_userid = " . $to_user_id;
								if ( !$result_pm = $db->sql_query($sql_pm) )
								{
									trigger_error('Could not find oldest privmsgs (inbox)', E_USER_WARNING);
								}
								$old_privmsgs_id = $db->sql_fetchrow($result_pm);
								$old_privmsgs_id = $old_privmsgs_id['privmsgs_id'];
				
								$sql_pm = 'DELETE FROM ' . PRIVMSGS_TABLE . ' 
								WHERE privmsgs_id = ' . $old_privmsgs_id;
								if ( !$db->sql_query($sql_pm) )
								{
									trigger_error('Could not delete oldest privmsgs (inbox)'.$sql_pm, E_USER_WARNING);
								}

								$sql_pm = 'DELETE FROM ' . PRIVMSGS_TEXT_TABLE . ' 
									WHERE privmsgs_text_id = ' . $old_privmsgs_id;
								if ( !$db->sql_query($sql_pm) )
								{
									trigger_error('Could not delete oldest privmsgs text (inbox)'.$sql_pm, E_USER_WARNING);
								}
							}
						}

						$sql_info = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip)
							VALUES (" . PRIVMSGS_NEW_MAIL . ", '$notify_pm_subject', -1, " . $to_user_id . ", " . time() . ", '$user_ip')";
						if ( !($result_pm = $db->sql_query($sql_info, BEGIN_TRANSACTION)) )
						{
							trigger_error("Could not insert/update private message sent info.", E_USER_WARNING);
						}

						$privmsg_sent_id = $db->sql_nextid();

						$sql_pm = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text)
							VALUES ($privmsg_sent_id, '" . $db->sql_escape($notify_pm_msg) . "')";
						if ( !$db->sql_query($sql_pm, END_TRANSACTION) )
						{
							trigger_error('Could not insert/update private message sent text.', E_USER_WARNING);
						}

						$sql_pm = "UPDATE " . USERS_TABLE . "
							SET user_new_privmsg = user_new_privmsg + 1, user_last_privmsg = " . time() . "  
							WHERE user_id = " . $to_user_id; 
						if ( !$status = $db->sql_query($sql_pm) )
						{
							trigger_error('Could not update private message new/read status for user', E_USER_WARNING);
						}
					}

					if ($row['user_email'] != '' && $row['user_notify_to_email'])
					{
						$bcc_list_ary[$row['user_lang']][] = $row['user_email'];
					}
					$update_watched_sql .= ($update_watched_sql != '') ? ', ' . $row['user_id'] : $row['user_id'];
				}
				while ($row = $db->sql_fetchrow($result));

				if (preg_match('/[c-z]:\\\.*/i', getenv('PATH')) && !$board_config['smtp_delivery'])
				{
					$board_config['smtp_delivery'] = 1;
					$board_config['smtp_host'] = @ini_get('SMTP');
				}

				if (count($bcc_list_ary))
				{
					include(ROOT_PATH . 'includes/class/emailer.php');
					$emailer = new emailer();
					$emailer->from($board_config['board_email']);
					$emailer->replyto($board_config['board_email']);

					@reset($bcc_list_ary);
					foreach ($bcc_list_ary as $user_lang => $bcc_list)
					{
						$emailer->use_template('topic_notify');
		
						for ($i = 0; $i < count($bcc_list); $i++)
						{
							$emailer->bcc($bcc_list[$i]);
						}

						$emailer->msg = preg_replace('#[ ]?{USERNAME}#', '', $emailer->msg);

						$emailer->assign_vars(array(
							'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',
							'SITENAME' => $board_config['sitename'],
							'TOPIC_TITLE' => $topic_title, 

							'U_TOPIC' => $server_protocol . $server_name . $server_port . $script_name . '?' . POST_POST_URL . "=$post_id#$post_id", 
							'U_STOP_WATCHING_TOPIC' => $server_protocol . $server_name . $server_port . $script_name . '?' . POST_TOPIC_URL . "=$topic_id&unwatch=topic")
						);

						$emailer->send();
						$emailer->reset();
					}
				}
			}
			$db->sql_freeresult($result);

			if ($update_watched_sql != '')
			{
				$sql = "UPDATE " . TOPICS_WATCH_TABLE . "
					SET notify_status = " . TOPIC_WATCH_NOTIFIED . "
					WHERE topic_id = $topic_id
						AND user_id IN ($update_watched_sql)";
				$db->sql_query($sql);
			}
		}

		$sql = "SELECT topic_id 
			FROM " . TOPICS_WATCH_TABLE . "
			WHERE topic_id = $topic_id
				AND user_id = " . $userdata['user_id'];
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not obtain topic watch information', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		if (!$notify_user && !empty($row['topic_id']))
		{
			$sql = "DELETE FROM " . TOPICS_WATCH_TABLE . "
				WHERE topic_id = $topic_id
					AND user_id = " . $userdata['user_id'];
			if (!$db->sql_query($sql))
			{
				trigger_error('Could not delete topic watch information', E_USER_WARNING);
			}
		}
		else if ($notify_user && empty($row['topic_id']))
		{
			$sql = "INSERT INTO " . TOPICS_WATCH_TABLE . " (user_id, topic_id, notify_status)
				VALUES (" . $userdata['user_id'] . ", $topic_id, 0)";
			if (!$db->sql_query($sql))
			{
				trigger_error('Could not insert topic watch information', E_USER_WARNING);
			}
		}
	}
}


function clean_html($tag)
{
	global $board_config;

	if (empty($tag[0]))
	{
		return '';
	}

	$allowed_html_tags = preg_split('/, */', strtolower($board_config['allow_html_tags']));
	$disallowed_attributes = '/^(?:style|on)/i';

	preg_match('/<[^\w\/]*\/[\W]*(\w+)/', $tag[0], $matches);
	if (count($matches))
	{
		if (in_array(strtolower($matches[1]), $allowed_html_tags))
		{
			return  '</' . $matches[1] . '>';
		}
		else
		{
			return  htmlspecialchars('</' . $matches[1] . '>');
		}
	}

	if (in_array(strtolower($tag[1]), $allowed_html_tags))
	{
		$attributes = '';
		if (!empty($tag[2]))
		{
			preg_match_all('/[\W]*?(\w+)[\W]*?=[\W]*?(["\'])((?:(?!\2).)*)\2/', $tag[2], $test);
			for ($i = 0; $i < count($test[0]); $i++)
			{
				if (preg_match($disallowed_attributes, $test[1][$i]))
				{
					continue;
				}
				$attributes .= ' ' . $test[1][$i] . '=' . $test[2][$i] . str_replace(array('[', ']'), array('&#91;', '&#93;'), htmlspecialchars($test[3][$i])) . $test[2][$i];
			}
		}
		if (in_array(strtolower($tag[1]), $allowed_html_tags))
		{
			return '<' . $tag[1] . $attributes . '>';
		}
		else
		{
			return htmlspecialchars('<' . $tag[1] . $attributes . '>');
		}
	}
	else
	{
		return htmlspecialchars('<' .   $tag[1] . '>');
	}
}
?>