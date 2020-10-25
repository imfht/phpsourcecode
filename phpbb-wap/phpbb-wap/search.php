<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

define('IN_PHPBB', true);
define('ROOT_PATH', './');
require(ROOT_PATH . 'common.php');
require(ROOT_PATH . 'includes/functions/bbcode.php');
require(ROOT_PATH . 'includes/functions/search.php');

$userdata = $session->start($user_ip, PAGE_SEARCH);
init_userprefs($userdata);

if ( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
}
else
{
	$mode = '';
}

if ( isset($_POST['search_keywords']) || isset($_GET['search_keywords']) )
{
	$search_keywords = ( isset($_POST['search_keywords']) ) ? $_POST['search_keywords'] : $_GET['search_keywords'];
}
else
{
	$search_keywords = '';
}

if ( isset($_POST['search_author']) || isset($_GET['search_author']))
{
	$search_author = ( isset($_POST['search_author']) ) ? $_POST['search_author'] : $_GET['search_author'];
	$search_author = phpbb_clean_username($search_author);
}
else
{
	$search_author = '';
}

$search_id = ( isset($_GET['search_id']) ) ? $_GET['search_id'] : '';

$show_results = ( isset($_POST['show_results']) ) ? $_POST['show_results'] : 'posts';
$show_results = ($show_results == 'topics') ? 'topics' : 'posts';

if ( isset($_POST['search_terms']) )
{
	$search_terms = ( $_POST['search_terms'] == 'all' ) ? 1 : 0;
}
else
{
	$search_terms = 0;
}

if ( isset($_POST['search_fields']) )
{
	$search_fields = ( $_POST['search_fields'] == 'all' ) ? 1 : 0;
}
else
{
	$search_fields = 0;
}

$return_chars = ( isset($_POST['return_chars']) ) ? intval($_POST['return_chars']) : 200;

$search_cat = ( isset($_POST['search_cat']) ) ? intval($_POST['search_cat']) : -1;
$search_forum = ( isset($_POST['search_forum']) ) ? intval($_POST['search_forum']) : -1;

$sort_by = ( isset($_POST['sort_by']) ) ? intval($_POST['sort_by']) : 0;

if ( isset($_POST['sort_dir']) )
{
	$sort_dir = ( $_POST['sort_dir'] == 'DESC' ) ? 'DESC' : 'ASC';
}
else
{
	$sort_dir =  'DESC';
}

if ( !empty($_POST['search_time']) || !empty($_GET['search_time']))
{
	$search_time = time() - ( ( ( !empty($_POST['search_time']) ) ? intval($_POST['search_time']) : intval($_GET['search_time']) ) * 86400 );
	$topic_days = (!empty($_POST['search_time'])) ? intval($_POST['search_time']) : intval($_GET['search_time']);
}
else
{
	$search_time = 0;
	$topic_days = 0;
}

$start = get_pagination_start($board_config['posts_per_page']);

$sort_by_types = array('发表时间', '发表帖子', '帖子标题', '作者', '论坛');

$multibyte_charset = 'utf-8, big5, shift_jis, euc-kr, gb2312';

if ( $mode == 'searchuser' )
{
	if ( isset($_POST['search_username']) && !empty($_POST['search']) )
	{
		username_search($_POST['search_username']);
	}
	elseif ( isset($_POST['username_list']) && !empty($_POST['use']) )
	{
		$u = intval($_POST['username_list']);
		redirect(append_sid("ucp.php?mode=viewprofile&u=$u"));
	}
	else
	{
		username_search('');
	}

	exit;
}
else if ( $search_keywords != '' || $search_author != '' || $search_id )
{
	$store_vars = array('search_results', 'total_match_count', 'split_search', 'sort_by', 'sort_dir', 'show_results', 'return_chars');
	$search_results = '';
	$limiter = 5000;
	$current_time = time();

	if ( $search_id == 'newposts' || $search_id == 'egosearch' || $search_id == 'unanswered' || $search_keywords != '' || $search_author != '')
	{
		$total_match_count 	= array();
		$split_search 		= array();
		if ( $search_id == 'newposts' || $search_id == 'egosearch' || ( $search_author != '' && $search_keywords == '' && $mode != 'all_topics' )  )
		{
			if ( $search_id == 'newposts' )
			{
				if ( $userdata['session_logged_in'] )
				{
					$sql = "SELECT post_id 
						FROM " . POSTS_TABLE . " 
						WHERE post_time >= " . $userdata['user_lastvisit'];
				}
				else
				{
					login_back("search.php?search_id=newposts");
				}

				$show_results = 'topics';
				$sort_by = 0;
				$sort_dir = 'DESC';
			}
			else if ( $search_id == 'egosearch' )
			{
				if ( $userdata['session_logged_in'] )
				{
					$sql = "SELECT post_id 
						FROM " . POSTS_TABLE . " 
						WHERE poster_id = " . $userdata['user_id'];
				}
				else
				{
					login_back("search.php?search_id=egosearch");
				}

				$show_results = 'topics';
				$sort_by = 0;
				$sort_dir = 'DESC';
			}
			else
			{
				$search_author = str_replace('*', '%', trim($search_author));

				if( ( strpos($search_author, '%') !== false ) && ( strlen(str_replace('%', '', $search_author)) < $board_config['search_min_chars'] ) )
				{
					$search_author = '';
				}

				$sql = "SELECT user_id
					FROM " . USERS_TABLE . "
					WHERE username LIKE '" . str_replace("\'", "''", $search_author) . "'";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error("Couldn't obtain list of matching users (searching for: $search_author)", E_USER_WARNING);
				}

				$matching_userids = '';
				if ( $row = $db->sql_fetchrow($result) )
				{
					do
					{
						$matching_userids .= ( ( $matching_userids != '' ) ? ', ' : '' ) . $row['user_id'];
					}
					while( $row = $db->sql_fetchrow($result) );
				}
				else
				{
					trigger_error('Sorry！系统没有为您搜索到任何符合条件的结果', E_USER_ERROR);
				}

				$sql = "SELECT post_id 
					FROM " . POSTS_TABLE . " 
					WHERE poster_id IN ($matching_userids)";
				
				if ($search_time)
				{
					$sql .= " AND post_time >= " . $search_time;
				}
			}

			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain matched posts list', E_USER_WARNING);
			}

			$search_ids = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$search_ids[] = $row['post_id'];
			}
			$db->sql_freeresult($result);

			$total_match_count = count($search_ids);

		}
		else if ( $search_keywords != '' )
		{
			// 忽略的词组
			$stopword_array = @file(ROOT_PATH . 'includes/search_word/search_stopwords.txt'); 

			// 同义词
			$synonym_array = @file(ROOT_PATH . 'includes/search_word/search_synonyms.txt'); 

			$stripped_keywords = stripslashes($search_keywords);
			$split_search = ( !strstr($multibyte_charset, 'utf-8') ) ?  split_words(clean_words('search', $stripped_keywords, $stopword_array, $synonym_array), 'search') : explode(' ', $search_keywords);	
			unset($stripped_keywords);

			$search_msg_only = ( !$search_fields ) ? "AND m.title_match = 0" : ( ( strstr($multibyte_charset, 'utf-8') ) ? '' : '' );

			$word_count = 0;
			$current_match_type = 'or';

			$word_match = array();
			$result_list = array();

			for($i = 0; $i < count($split_search); $i++)
			{
				if ( strlen(str_replace(array('*', '%'), '', trim($split_search[$i]))) < $board_config['search_min_chars'] )
				{
					$split_search[$i] = '';
					continue;
				}

				switch ( $split_search[$i] )
				{
					case 'and':
						$current_match_type = 'and';
						break;

					case 'or':
						$current_match_type = 'or';
						break;

					case 'not':
						$current_match_type = 'not';
						break;

					default:
						if ( !empty($search_terms) )
						{
							$current_match_type = 'and';
						}

						if ( !strstr($multibyte_charset, 'utf-8') )
						{
							$match_word = str_replace('*', '%', $split_search[$i]);
							$sql = "SELECT m.post_id 
								FROM " . SEARCH_WORD_TABLE . " w, " . SEARCH_MATCH_TABLE . " m 
								WHERE w.word_text LIKE '$match_word' 
									AND m.word_id = w.word_id 
									AND w.word_common <> 1 
									$search_msg_only";
						}
						else
						{
							$match_word =  addslashes('%' . str_replace('*', '', $split_search[$i]) . '%');
							$search_msg_only = ( $search_fields ) ? "OR post_subject LIKE '$match_word'" : '';
							$sql = "SELECT post_id
								FROM " . POSTS_TEXT_TABLE . "
								WHERE post_text LIKE '$match_word'
								$search_msg_only";
						}
						if ( !($result = $db->sql_query($sql)) )
						{
							trigger_error('Could not obtain matched posts list', E_USER_WARNING);
						}

						$row = array();
						while( $temp_row = $db->sql_fetchrow($result) )
						{
							$row[$temp_row['post_id']] = 1;

							if ( !$word_count )
							{
								$result_list[$temp_row['post_id']] = 1;
							}
							else if ( $current_match_type == 'or' )
							{
								$result_list[$temp_row['post_id']] = 1;
							}
							else if ( $current_match_type == 'not' )
							{
								$result_list[$temp_row['post_id']] = 0;
							}
						}

						if ( $current_match_type == 'and' && $word_count )
						{
							@reset($result_list);
							foreach($result_list as $post_id => $match_count)
							{
								if ( !$row[$post_id] )
								{
									$result_list[$post_id] = 0;
								}
							}
						}

						$word_count++;

						$db->sql_freeresult($result);
					}
			}

			@reset($result_list);

			$search_ids = array();
			foreach($result_list as $post_id => $matches)
			{
				if ( $matches )
				{
					$search_ids[] = $post_id;
				}
			}	
			
			unset($result_list);
			$total_match_count = count($search_ids);
		}

		$auth_sql = '';
		if ( $search_forum != -1 )
		{
			$is_auth = auth(AUTH_READ, $search_forum, $userdata);

			if ( !$is_auth['auth_read'] )
			{
				trigger_error('您没有权限搜索任何论坛', E_USER_ERROR);
			}

			$auth_sql = "f.forum_id = $search_forum";
		}
		else
		{
			$is_auth_ary = auth(AUTH_READ, AUTH_LIST_ALL, $userdata); 

			if ( $search_cat != -1 )
			{
				$auth_sql = "f.cat_id = $search_cat";
			}

			$ignore_forum_sql = '';
			foreach($is_auth_ary as $key => $value)
			{
				if ( !$value['auth_read'] )
				{
					$ignore_forum_sql .= ( ( $ignore_forum_sql != '' ) ? ', ' : '' ) . $key;
				}
			}

			if ( $ignore_forum_sql != '' )
			{
				$auth_sql .= ( $auth_sql != '' ) ? " AND f.forum_id NOT IN ($ignore_forum_sql) " : "f.forum_id NOT IN ($ignore_forum_sql) ";
			}
		}

		if ( $search_author != '' )
		{
			$search_author = str_replace('*', '%', trim($search_author));

			if( ( strpos($search_author, '%') !== false ) && ( strlen(str_replace('%', '', $search_author)) < $board_config['search_min_chars'] ) )
			{
				$search_author = '';
			}
		}

		if ( $total_match_count )
		{
			if ( $show_results == 'topics' )
			{
				$search_id_chunks = array();
				$count = 0;
				$chunk = 0;

				if (count($search_ids) > $limiter)
				{
					for ($i = 0; $i < count($search_ids); $i++) 
					{
						if ($count == $limiter)
						{
							$chunk++;
							$count = 0;
						}
					
						$search_id_chunks[$chunk][$count] = $search_ids[$i];
						$count++;
					}
				}
				else
				{
					$search_id_chunks[0] = $search_ids;
				}

				$search_ids = array();

				for ($i = 0; $i < count($search_id_chunks); $i++)
				{
					$where_sql = '';

					if ( $search_time )
					{
						$where_sql .= ( $search_author == '' && $auth_sql == ''  ) ? " AND post_time >= $search_time " : " AND p.post_time >= $search_time ";
					}
	
					if ( $search_author == '' && $auth_sql == '' )
					{
						$sql = "SELECT topic_id 
							FROM " . POSTS_TABLE . "
							WHERE post_id IN (" . implode(", ", $search_id_chunks[$i]) . ") 
							$where_sql 
							GROUP BY topic_id";
					}
					else
					{
						$from_sql = POSTS_TABLE . " p"; 

						if ( $search_author != '' )
						{
							$from_sql .= ", " . USERS_TABLE . " u";
							$where_sql .= " AND u.user_id = p.poster_id AND u.username LIKE '$search_author' ";
						}

						if ( $auth_sql != '' )
						{
							$from_sql .= ", " . FORUMS_TABLE . " f";
							$where_sql .= " AND f.forum_id = p.forum_id AND $auth_sql";
						}

						$sql = "SELECT p.topic_id 
							FROM $from_sql 
							WHERE p.post_id IN (" . implode(", ", $search_id_chunks[$i]) . ") 
								$where_sql 
							GROUP BY p.topic_id";
					}

					if ( !($result = $db->sql_query($sql)) )
					{
						trigger_error('Could not obtain topic ids', E_USER_WARNING);
					}

					while ($row = $db->sql_fetchrow($result))
					{
						$search_ids[] = $row['topic_id'];
					}
					$db->sql_freeresult($result);
				}

				$total_match_count = count($search_ids);
		
			}
			else if ( $search_author != '' || $search_time || $auth_sql != '' )
			{
				$search_id_chunks = array();
				$count = 0;
				$chunk = 0;

				if (count($search_ids) > $limiter)
				{
					for ($i = 0; $i < count($search_ids); $i++) 
					{
						if ($count == $limiter)
						{
							$chunk++;
							$count = 0;
						}
					
						$search_id_chunks[$chunk][$count] = $search_ids[$i];
						$count++;
					}
				}
				else
				{
					$search_id_chunks[0] = $search_ids;
				}

				$search_ids = array();

				for ($i = 0; $i < count($search_id_chunks); $i++)
				{
					$where_sql = ( $search_author == '' && $auth_sql == '' ) ? 'post_id IN (' . implode(', ', $search_id_chunks[$i]) . ')' : 'p.post_id IN (' . implode(', ', $search_id_chunks[$i]) . ')';
					$select_sql = ( $search_author == '' && $auth_sql == '' ) ? 'post_id' : 'p.post_id';
					$from_sql = (  $search_author == '' && $auth_sql == '' ) ? POSTS_TABLE : POSTS_TABLE . ' p';

					if ( $search_time )
					{
						$where_sql .= ( $search_author == '' && $auth_sql == '' ) ? " AND post_time >= $search_time " : " AND p.post_time >= $search_time";
					}

					if ( $auth_sql != '' )
					{
						$from_sql .= ", " . FORUMS_TABLE . " f";
						$where_sql .= " AND f.forum_id = p.forum_id AND $auth_sql";
					}

					if ( $search_author != '' )
					{
						$from_sql .= ", " . USERS_TABLE . " u";
						$where_sql .= " AND u.user_id = p.poster_id AND u.username LIKE '$search_author'";
					}

					$sql = "SELECT " . $select_sql . " 
						FROM $from_sql 
						WHERE $where_sql";
					if ( !($result = $db->sql_query($sql)) )
					{
						trigger_error('Could not obtain post ids', E_USER_WARNING);
					}

					while( $row = $db->sql_fetchrow($result) )
					{
						$search_ids[] = $row['post_id'];
					}
					$db->sql_freeresult($result);
				}

				$total_match_count = count($search_ids);
			}
		}
		else if ( $search_id == 'unanswered' )
		{
			if ( $auth_sql != '' )
			{
				$sql = "SELECT t.topic_id, f.forum_id
					FROM " . TOPICS_TABLE . "  t, " . FORUMS_TABLE . " f
					WHERE t.topic_replies = 0 
						AND t.forum_id = f.forum_id
						AND t.topic_moved_id = 0
						AND $auth_sql";
			}
			else
			{
				$sql = "SELECT topic_id 
					FROM " . TOPICS_TABLE . "  
					WHERE topic_replies = 0 
						AND topic_moved_id = 0";
			}
				
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain post ids', E_USER_WARNING);
			}

			$search_ids = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$search_ids[] = $row['topic_id'];
			}
			$db->sql_freeresult($result);

			$total_match_count = count($search_ids);
			$show_results = 'topics';
			$sort_by = 0;
			$sort_dir = 'DESC';
		}
		else if ( $mode == 'all_topics' && $search_author != '' )
		{
			$sql = "SELECT user_id
				FROM " . USERS_TABLE . "
				WHERE username LIKE '" . str_replace("\'", "''", $search_author) . "'";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error("Couldn't obtain list of matching users (searching for: $search_author)", E_USER_WARNING);
			}

			$matching_userids = '';
			if ( $row = $db->sql_fetchrow($result) )
			{
				do
				{
					$matching_userids .= ( ( $matching_userids != '' ) ? ', ' : '' ) . $row['user_id'];
				}
				while( $row = $db->sql_fetchrow($result) );
			}
			else
			{
				trigger_error('没有找到本匹配的主题或者贴子', E_USER_ERROR);
			}

			$sql = "SELECT topic_id 
				FROM " . TOPICS_TABLE . " 
				WHERE topic_poster IN ($matching_userids)";
			if ($search_time)
			{
				$sql .= " AND topic_time >= " . $search_time;
			}

			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain matched topics list', E_USER_WARNING);
			}

			$search_ids = array();
			while( $row = $db->sql_fetchrow($result) )
			{
				$search_ids[] = $row['topic_id'];
			}
			$db->sql_freeresult($result);

			if ( count($search_ids) == 0 )
			{
				trigger_error('没有找到本匹配的主题或者贴子', E_USER_ERROR);
			}

			$search_id_chunks = array();
			$count = 0;
			$chunk = 0;
			if (count($search_ids) > $limiter)
			{
				for ($i = 0; $i < count($search_ids); $i++) 
				{
					if ($count == $limiter)
					{
						$chunk++;
						$count = 0;
					}
					
					$search_id_chunks[$chunk][$count] = $search_ids[$i];
					$count++;
				}
			}
			else
			{
				$search_id_chunks[0] = $search_ids;
			}

			$search_ids = array();

			for ($i = 0; $i < count($search_id_chunks); $i++)
			{
				$where_sql = ( $search_author == '' && $auth_sql == '' ) ? 'topic_id IN (' . implode(', ', $search_id_chunks[$i]) . ')' : 't.topic_id IN (' . implode(', ', $search_id_chunks[$i]) . ')';
				$select_sql = ( $search_author == '' && $auth_sql == '' ) ? 'topic_id_id' : 't.topic_id';
				$from_sql = (  $search_author == '' && $auth_sql == '' ) ? TOPICS_TABLE : TOPICS_TABLE . ' t';

				if ( $search_time )
				{
					$where_sql .= ( $search_author == '' && $auth_sql == '' ) ? " AND topic_time >= $search_time " : " AND t.topic_time >= $search_time";
				}

				if ( $auth_sql != '' )
				{
					$from_sql .= ", " . FORUMS_TABLE . " f";
					$where_sql .= " AND f.forum_id = t.forum_id AND $auth_sql";
				}

				if ( $search_author != '' )
				{
					$from_sql .= ", " . USERS_TABLE . " u";
					$where_sql .= " AND u.user_id = t.topic_poster AND u.username LIKE '$search_author'";
				}

				$sql = "SELECT " . $select_sql . " 
					FROM $from_sql 
					WHERE $where_sql";
				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Could not obtain post ids', E_USER_WARNING);
				}

				while( $row = $db->sql_fetchrow($result) )
				{
					$search_ids[] = $row['topic_id'];
				}
				$db->sql_freeresult($result);
			}

			$total_match_count = count($search_ids);
			$sort_by = 5;
			$sort_dir = 'DESC';
			$show_results = 'topics';
		}
		else
		{
			trigger_error('没有找到本匹配的主题或者贴子', E_USER_ERROR);
		}

		$sql = 'DELETE FROM ' . SEARCH_TABLE . '
			WHERE search_time < ' . ($current_time - (int) $board_config['session_length']);
		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not delete old search id sessions', E_USER_WARNING);
		}

		$search_results = implode(', ', $search_ids);
		$per_page = ( $show_results == 'posts' ) ? $board_config['posts_per_page'] : $board_config['topics_per_page'];
		$store_search_data = array();

		for($i = 0; $i < count($store_vars); $i++)
		{
			$store_search_data[$store_vars[$i]] = $$store_vars[$i];
		}

		$result_array = serialize($store_search_data);
		unset($store_search_data);

		mt_srand ((double) microtime() * 1000000);
		$search_id = mt_rand();

		$sql = "UPDATE " . SEARCH_TABLE . " 
			SET search_id = $search_id, search_time = $current_time, search_array = '" . str_replace("\'", "''", $result_array) . "'
			WHERE session_id = '" . $userdata['session_id'] . "'";
		if ( !($result = $db->sql_query($sql)) || !$db->sql_affectedrows() )
		{
			$sql = "INSERT INTO " . SEARCH_TABLE . " (search_id, session_id, search_time, search_array) 
				VALUES($search_id, '" . $userdata['session_id'] . "', $current_time, '" . str_replace("\'", "''", $result_array) . "')";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not insert search results', E_USER_WARNING);
			}
		}
	}
	else
	{
		$search_id = intval($search_id);
		if ( $search_id )
		{
			$sql = "SELECT search_array 
				FROM " . SEARCH_TABLE . " 
				WHERE search_id = $search_id  
					AND session_id = '". $userdata['session_id'] . "'";
			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not obtain search results', E_USER_WARNING);
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				$search_data = unserialize($row['search_array']);
				for($i = 0; $i < count($store_vars); $i++)
				{
					$$store_vars[$i] = $search_data[$store_vars[$i]];
				}
			}
		}
	}

	if ( $search_results != '' )
	{
		if ( $show_results == 'posts' )
		{
			$sql = "SELECT pt.post_text, pt.bbcode_uid, pt.post_subject, p.*, f.forum_id, f.forum_name, t.*, u.username, u.user_id  
				FROM " . FORUMS_TABLE . " f, " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . POSTS_TEXT_TABLE . " pt 
				WHERE p.post_id IN ($search_results)
					AND pt.post_id = p.post_id
					AND f.forum_id = p.forum_id
					AND p.topic_id = t.topic_id
					AND p.poster_id = u.user_id";
			$per_page = $board_config['posts_per_page'];
		}
		else
		{
			$sql = "SELECT t.*, f.forum_id, f.forum_name, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_username, p2.post_username AS post_username2, p2.post_time 
				FROM " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2, " . USERS_TABLE . " u2
				WHERE t.topic_id IN ($search_results) 
					AND t.topic_poster = u.user_id
					AND f.forum_id = t.forum_id 
					AND p.post_id = t.topic_first_post_id
					AND p2.post_id = t.topic_last_post_id
					AND u2.user_id = p2.poster_id";
			$per_page = $board_config['topics_per_page'];
		}

		$sql .= " ORDER BY ";
		switch ( $sort_by )
		{
			case 1:
				$sql .= ( $show_results == 'posts' ) ? 'pt.post_subject' : 't.topic_title';
				break;
			case 2:
				$sql .= 't.topic_title';
				break;
			case 3:
				$sql .= 'u.username';
				break;
			case 4:
				$sql .= 'f.forum_id';
				break;
			case 5:
				$sql .= 't.topic_time';
				break;
			default:
				$sql .= ( $show_results == 'posts' ) ? 'p.post_time' : 'p2.post_time';
				break;
		}
		$sql .= " $sort_dir LIMIT $start, " . $per_page;

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not obtain search results', E_USER_WARNING);
		}

		$searchset = array();
		while( $row = $db->sql_fetchrow($result) )
		{
			$searchset[] = $row;
		}
		
		$db->sql_freeresult($result);		

		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);

		$page_title = '搜索';
		page_header($page_title);	

		if ( $show_results == 'posts' )
		{
			$template->set_filenames(array(
				'body' => 'search/search_results_posts.tpl')
			);
		}
		else
		{
			$template->set_filenames(array(
				'body' => 'search/search_results_topics.tpl')
			);
		}

		$l_search_matches = '为您找到 ' . $total_match_count . ' 个搜索结果';

		$template->assign_vars(array(
			'L_SEARCH_MATCHES' => $l_search_matches)
		);

		$highlight_active = '';
		$highlight_match = array();
		
		$count_split_search = count($split_search);
		for($j = 0; $j < $count_split_search; $j++ )
		{
			$split_word = $split_search[$j];

			if ( $split_word != 'and' && $split_word != 'or' && $split_word != 'not' )
			{
				$highlight_match[] = '#\b(' . str_replace("*", "([\w]+)?", $split_word) . ')\b#is';
				$highlight_active .= " " . $split_word;

				$count_synonym_array = count($synonym_array);

				for ($k = 0; $k < $count_synonym_array; $k++)
				{ 
					list($replace_synonym, $match_synonym) = explode(' ', trim(strtolower($synonym_array[$k]))); 

					if ( $replace_synonym == $split_word )
					{
						$highlight_match[] = '#\b(' . str_replace("*", "([\w]+)?", $replace_synonym) . ')\b#is';
						$highlight_active .= ' ' . $match_synonym;
					}
				} 
			}
		}

		$highlight_active = urlencode(trim($highlight_active));

		$tracking_topics = ( isset($_COOKIE[$board_config['cookie_name'] . '_t']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_t']) : array();
		$tracking_forums = ( isset($_COOKIE[$board_config['cookie_name'] . '_f']) ) ? unserialize($_COOKIE[$board_config['cookie_name'] . '_f']) : array();

		for($i = 0; $i < count($searchset); $i++)
		{
		
			$forum_url 		= append_sid("viewforum.php?" . POST_FORUM_URL . '=' . $searchset[$i]['forum_id']);
			$topic_url 		= append_sid("viewtopic.php?" . POST_TOPIC_URL . '=' . $searchset[$i]['topic_id'] . "&amp;highlight=$highlight_active");
			$topic_title 	= $searchset[$i]['topic_title'];
			$forum_id 		= $searchset[$i]['forum_id'];
			$topic_id 		= $searchset[$i]['topic_id'];

			if ( $show_results == 'posts' )
			{
				$message 	= $searchset[$i]['post_text'];
				$post_url 	= append_sid('viewtopic.php?' . POST_POST_URL . '=' . $searchset[$i]['post_id'] . "&amp;highlight=$highlight_active") . '#' . $searchset[$i]['post_id'];

				if ( isset($return_chars) )
				{
					$bbcode_uid = $searchset[$i]['bbcode_uid'];

					if ( $return_chars != -1 )
					{
						$message = strip_tags($message);
						$message = preg_replace("/\[.*?:$bbcode_uid:?.*?\]/si", '', $message);
						$message = preg_replace('/\[url\]|\[\/url\]/si', '', $message);
						$message = preg_replace('/\[mp3\]|\[\/mp3\]/si', '', $message);
						$message = ( mb_strlen($message, 'UTF-8') > $return_chars ) ? mb_substr($message, 0, $return_chars, 'UTF-8') . ' ...' : $message;
					}
					else
					{

						$message = make_clickable($message);

						if ( $highlight_active )
						{
							if ( preg_match('/<.*>/', $message) )
							{
								$message = preg_replace($highlight_match, '<!-- #sh -->\1<!-- #eh -->', $message);

								$end_html = 0;
								$start_html = 1;
								$temp_message = '';
								$message = ' ' . $message . ' ';

								while( $start_html = strpos($message, '<', $start_html) )
								{
									$grab_length = $start_html - $end_html - 1;
									$temp_message .= substr($message, $end_html + 1, $grab_length);

									if ( $end_html = strpos($message, '>', $start_html) )
									{
										$length = $end_html - $start_html + 1;
										$hold_string = substr($message, $start_html, $length);

										if ( strrpos(' ' . $hold_string, '<') != 1 )
										{
											$end_html = $start_html + 1;
											$end_counter = 1;

											while ( $end_counter && $end_html < strlen($message) )
											{
												if ( substr($message, $end_html, 1) == '>' )
												{
													$end_counter--;
												}
												else if ( substr($message, $end_html, 1) == '<' )
												{
													$end_counter++;
												}

												$end_html++;
											}

											$length = $end_html - $start_html + 1;
											$hold_string = substr($message, $start_html, $length);
											$hold_string = str_replace('<!-- #sh -->', '', $hold_string);
											$hold_string = str_replace('<!-- #eh -->', '', $hold_string);
										}
										else if ( $hold_string == '<!-- #sh -->' )
										{
											$hold_string = str_replace('<!-- #sh -->', '<span style="color:red;"><strong>', $hold_string);
										}
										else if ( $hold_string == '<!-- #eh -->' )
										{
											$hold_string = str_replace('<!-- #eh -->', '</strong></span>', $hold_string);
										}

										$temp_message .= $hold_string;

										$start_html += $length;
									}
									else
									{
										$start_html = strlen($message);
									}
								}

								$grab_length = strlen($message) - $end_html - 1;
								$temp_message .= substr($message, $end_html + 1, $grab_length);

								$message = trim($temp_message);
							}
							else
							{
								$message = preg_replace($highlight_match, '<span style="color:red;"><strong>\1</strong></span>', $message);
							}
						}
					}

					if ( count($orig_word) )
					{
						$topic_title = str_replace($orig_word, $replacement_word, $topic_title);
						$post_subject = ( $searchset[$i]['post_subject'] != "" ) ? str_replace($orig_word, $replacement_word, $searchset[$i]['post_subject']) : $topic_title;

						$message = str_replace($orig_word, $replacement_word, $message);
					}
					else
					{
						$post_subject = ( $searchset[$i]['post_subject'] != '' ) ? $searchset[$i]['post_subject'] : $topic_title;
					}

					$message = str_replace("\n", '&nbsp;', $message);

				}
				
				$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('searchresults', array(
					'ROW_CLASS'		=> $row_class,
					'L_NUMBER'		=> $i + $start + 1,
					'TOPIC_TITLE' 	=> $topic_title,
					'FORUM_NAME' 	=> $searchset[$i]['forum_name'],
					'MESSAGE' 		=> $message,
					'U_POST' 		=> $post_url,
					'U_TOPIC' 		=> $topic_url,
					'U_FORUM' 		=> $forum_url)
				);
			}
			else
			{
				$message = '';

				if ( count($orig_word) )
				{
					$topic_title = str_replace($orig_word, $replacement_word, $searchset[$i]['topic_title']);
				}

				$topic_author 		= ( $searchset[$i]['user_id'] != ANONYMOUS ) ? '<a href="' . append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . '=' . $searchset[$i]['user_id']) . '">' : '';
				$topic_author 		.= ( $searchset[$i]['user_id'] != ANONYMOUS ) ? $searchset[$i]['username'] : ( ( $searchset[$i]['post_username'] != '' ) ? $searchset[$i]['post_username'] : '匿名' );
				$topic_author 		.= ( $searchset[$i]['user_id'] != ANONYMOUS ) ? '</a>' : '';
				$first_post_time 	= create_date($board_config['default_dateformat'], $searchset[$i]['topic_time'], $board_config['board_timezone']);

				$last_post_url 		= '<a href="' . append_sid("viewtopic.php?"  . POST_POST_URL . '=' . $searchset[$i]['topic_last_post_id']) . '#' . $searchset[$i]['topic_last_post_id'] . '">»</a>';

				$row_class 			= ( !($i % 2) ) ? 'row1' : 'row2';

				$template->assign_block_vars('searchresults', array(
					'ROW_CLASS'				=> $row_class,
					'L_NUMBER'				=> $i + $start + 1,
					'FORUM_NAME' 			=> $searchset[$i]['forum_name'],
					'TOPIC_TITLE' 			=> $topic_title,
					'TOPIC_AUTHOR' 			=> $topic_author, 
					'LAST_POST' 			=> $last_post_url,
					'FIRST_POST_TIME' 		=> $first_post_time, 
					'U_VIEW_FORUM' 			=> $forum_url, 
					'U_VIEW_TOPIC'		 	=> $topic_url)
				);
			}
		}
		
		$base_url = "search.php?search_id=$search_id";

		if (isset($_GET['ucp']))
		{
			$template->assign_block_vars('from_ucp', array());
			$template->assign_vars(array(
				'U_UCP'		=> append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $search_author))
			);
		}
		else
		{
			$template->assign_block_vars('from_search', array());
			$template->assign_vars(array(
				'U_SEARCH'		=> append_sid('search.php'))
			);
		}
		
		$template->assign_vars(array(
			'PAGINATION' 	=> generate_pagination($base_url, $total_match_count, $per_page, $start))
		);

		$template->pparse('body');

		page_footer();
	}
	else
	{
		trigger_error('Sorry！系统没有为您搜索到任何符合条件的结果', E_USER_ERROR);
	}
}

$sql = "SELECT c.cat_title, c.cat_id, f.forum_name, f.forum_id  
	FROM " . CATEGORIES_TABLE . " c, " . FORUMS_TABLE . " f
	WHERE f.cat_id = c.cat_id 
	ORDER BY c.cat_order, f.forum_order";

$result = $db->sql_query($sql);

if ( !$result )
{
	trigger_error('Could not obtain forum_name/forum_id', E_USER_WARNING);
}

$is_auth_ary = auth(AUTH_READ, AUTH_LIST_ALL, $userdata);

$s_forums = '';
while( $row = $db->sql_fetchrow($result) )
{
	if ( $is_auth_ary[$row['forum_id']]['auth_read'] )
	{
		$s_forums .= '<option value="' . $row['forum_id'] . '">' . $row['forum_name'] . '</option>';
		if ( empty($list_cat[$row['cat_id']]) )
		{
			$list_cat[$row['cat_id']] = $row['cat_title'];
		}
	}
}

if ( $s_forums != '' )
{
	$s_forums = '<option value="-1">全部</option>' . $s_forums;
	$s_categories = '<option value="-1">全部</option>';
	
	foreach($list_cat as $cat_id => $cat_title)
	{
		$s_categories .= '<option value="' . $cat_id . '">' . $cat_title . '</option>';
	}
}
else
{
	trigger_error('您没有搜索论坛的权限', E_USER_ERROR);
}

$s_characters = '<option value="0" selected="selected">不显示</option>';
$s_characters .= '<option value="25">20字</option>';
$s_characters .= '<option value="50">50字</option>';

for($i = 100; $i < 1100 ; $i += 100)
{
	$s_characters .= '<option value="' . $i . '>' . $i . '字</option>';
}

$s_characters .= '<option value="-1">显示全部</option>';

$s_sort_by = "";
for($i = 0; $i < count($sort_by_types); $i++)
{
	$s_sort_by .= '<option value="' . $i . '">' . $sort_by_types[$i] . '</option>';
}

$previous_days = array(0, 1, 7, 14, 30, 90, 180, 364);
$previous_days_text = array('全部', '一天内', '一周内', '两周内', '一个月内', '三个月内', '半年内', '一年内');

$s_time = '';
for($i = 0; $i < count($previous_days); $i++)
{
	$selected = ( $topic_days == $previous_days[$i] ) ? ' selected="selected"' : '';
	$s_time .= '<option value="' . $previous_days[$i] . '"' . $selected . '>' . $previous_days_text[$i] . '</option>';
}

$page_title = '搜索网站';

page_header($page_title);

$template->set_filenames(array(
	'body' => 'search/search_body.tpl')
);

$template->assign_vars(array(
	'S_CHARACTER_OPTIONS'	=> $s_characters,
	'S_FORUM_OPTIONS' 		=> $s_forums, 
	'S_CATEGORY_OPTIONS' 	=> $s_categories, 
	'S_TIME_OPTIONS'		=> $s_time, 
	'S_SORT_OPTIONS' 		=> $s_sort_by,
	'S_HIDDEN_FIELDS' 		=> '')
);

$template->pparse('body');

page_footer();
?>