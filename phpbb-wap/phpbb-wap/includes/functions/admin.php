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

function make_forum_select($box_name, $ignore_forum = false, $select_forum = '')
{
	global $db, $userdata;

	$is_auth_ary = auth(AUTH_READ, AUTH_LIST_ALL, $userdata);

	$sql = 'SELECT f.forum_id, f.forum_name
		FROM ' . CATEGORIES_TABLE . ' c, ' . FORUMS_TABLE . ' f
		WHERE f.cat_id = c.cat_id 
		ORDER BY c.cat_order, f.forum_order';
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Couldn not obtain forums information', E_USER_WARNING);
	}

	$forum_list = '';
	while( $row = $db->sql_fetchrow($result) )
	{
		if ( $is_auth_ary[$row['forum_id']]['auth_read'] && $ignore_forum != $row['forum_id'] )
		{
			$selected = ( $select_forum == $row['forum_id'] ) ? ' selected="selected"' : '';
			$forum_list .= '<option value="' . $row['forum_id'] . '"' . $selected .'>' . $row['forum_name'] . '</option>';
		}
	}

	$forum_list = ( $forum_list == '' ) ? '没有论坛' : '<select name="' . $box_name . '">' . $forum_list . '</select>';

	return $forum_list;
}

function sync($type, $id = false)
{
	global $db;

	switch($type)
	{
		case 'all forums':
			$sql = 'SELECT forum_id
				FROM ' . FORUMS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get forum IDs', E_USER_WARNING);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				sync('forum', $row['forum_id']);
			}
		   	break;

		case 'all topics':
			$sql = 'SELECT topic_id
				FROM ' . TOPICS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get topic ID', E_USER_WARNING);
			}

			while( $row = $db->sql_fetchrow($result) )
			{
				sync('topic', $row['topic_id']);
			}
			break;

	  	case 'forum':
			$sql = 'SELECT MAX(post_id) AS last_post, COUNT(post_id) AS total 
				FROM ' . POSTS_TABLE . "  
				WHERE forum_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get post ID', E_USER_WARNING);
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				$last_post = ( $row['last_post'] ) ? $row['last_post'] : 0;
				$total_posts = ($row['total']) ? $row['total'] : 0;
			}
			else
			{
				$last_post = 0;
				$total_posts = 0;
			}

			$sql = 'SELECT COUNT(topic_id) AS total
				FROM ' . TOPICS_TABLE . "
				WHERE forum_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get topic count', E_USER_WARNING);
			}

			$total_topics = ( $row = $db->sql_fetchrow($result) ) ? ( ( $row['total'] ) ? $row['total'] : 0 ) : 0;

			$sql = 'UPDATE ' . FORUMS_TABLE . "
				SET forum_last_post_id = $last_post, forum_posts = $total_posts, forum_topics = $total_topics
				WHERE forum_id = $id";
			if ( !$db->sql_query($sql) )
			{
				trigger_error('Could not update forum', E_USER_WARNING);
			}

			break;

		case 'topic':
			$sql = 'SELECT MAX(post_id) AS last_post, MIN(post_id) AS first_post, COUNT(post_id) AS total_posts
				FROM ' . POSTS_TABLE . "
				WHERE topic_id = $id";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not get post ID', E_USER_WARNING);
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				if ($row['total_posts'])
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . ' 
						SET topic_replies = ' . ($row['total_posts'] - 1) . ', topic_first_post_id = ' . $row['first_post'] . ', topic_last_post_id = ' . $row['last_post'] . "
						WHERE topic_id = $id";

					if (!$db->sql_query($sql))
					{
						trigger_error('Could not update topic', E_USER_WARNING);
					}
				}
				else
				{
					$sql = 'SELECT topic_moved_id 
						FROM ' . TOPICS_TABLE . " 
						WHERE topic_id = $id";

					if (!($result = $db->sql_query($sql)))
					{
						trigger_error('Could not get topic ID', E_USER_WARNING);
					}

					if ($row = $db->sql_fetchrow($result))
					{
						if (!$row['topic_moved_id'])
						{
							$sql = 'DELETE FROM ' . TOPICS_TABLE . " WHERE topic_id = $id";
			
							if (!$db->sql_query($sql))
							{
								trigger_error('Could not remove topic', E_USER_WARNING);
							}
						}
					}

					$db->sql_freeresult($result);
				}
				attachment_sync_topic($id);
			}
			break;
	}
	
	return true;
}

?>