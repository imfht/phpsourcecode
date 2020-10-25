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

$userdata = $session->start($user_ip, PAGE_CLASS);
init_userprefs($userdata);

$mode 		= get_var('mode', '');

$forum_id 	= get_var(POST_FORUM_URL, 0);

if ( !empty($forum_id) )
{
	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = $forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法获取论坛的信息', E_USER_WARNING);
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

$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_row);

if ( !$is_auth['auth_read'] || !$is_auth['auth_view'] )
{
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = POST_FORUM_URL . "=$forum_id" . ( ( isset($start) ) ? "&start=$start" : '' );
		login_back("viewclass.php?$redirect");
	}
	$message = ( !$is_auth['auth_view'] ) ? '论坛不存在' : '对不起，仅 ' . $is_auth['auth_read_type'] . ' 可以阅读主题';

	trigger_error($message, E_USER_ERROR);
}

if ( $is_auth['auth_mod'] && $board_config['prune_enable'] )
{
	if ( $forum_row['prune_next'] < time() && $forum_row['prune_enable'] )
	{
		require(ROOT_PATH . 'includes/functions/prune.php');
		require(ROOT_PATH . 'includes/functions/admin.php');
		auto_prune($forum_id);
	}
}

switch ($mode)
{
	// 设置专题
	case 'select':
		
		if (!$is_auth['auth_mod'])
		{
			trigger_error('您没有权限设置专题', E_USER_ERROR);
		}

		$topic_id = get_var(POST_TOPIC_URL, 0);
		$topic_class = get_var(POST_CLASS_URL, 0);

		if ($topic_class != TOPIC_UNCLASS)
		{
			$sql = "SELECT class_id 
				FROM " . CLASS_TABLE . " 
				WHERE class_id = $topic_class 
					AND class_forum = $forum_id";

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法获取专题信息', E_USER_WARNING);
			}

			if (!$db->sql_numrows($result))
			{
				trigger_error('不存在的专题', E_USER_ERROR);
			}
		}

		$sql = 'SELECT topic_id 
			FROM ' . TOPICS_TABLE . " 
			WHERE topic_id = $topic_id
			 	AND forum_id = $forum_id";

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法获取帖子信息', E_USER_WARNING);
		}

		if (!$db->sql_numrows($result))
		{
			trigger_error('不存在该帖子', E_USER_ERROR);
		}

		$sql = "UPDATE " . TOPICS_TABLE . " 
			SET topic_class = $topic_class 
			WHERE topic_id = $topic_id";

		if (!$db->sql_query($sql))
		{
			trigger_error('无法设置专题', E_USER_WARNING);
		}

		redirect(append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id));

		break;

	// 创建专题
	case 'create':
		
		if (!$is_auth['auth_mod'])
		{
			trigger_error('您没有权限创建专题', E_USER_ERROR);
		}

		if(isset($_POST['classname']))
		{
			$classname = get_var('classname', '');

			if (empty($classname))
			{
				trigger_error('专题名称必须填写', E_USER_ERROR);
			}
			
			$sql = 'INSERT INTO ' . CLASS_TABLE . " (class_name, class_forum)
				VALUES ('" . $db->sql_escape($classname) . "',  $forum_id)";

			if ( $db->sql_query($sql) )
			{
				redirect(append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id));
			}
			else
			{
				trigger_error('无法创建专题', E_USER_WARNING);
			}
		}

		redirect(append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id));

		break;

	// 修改专题
	case 'edit':

		if (!$is_auth['auth_mod'])
		{
			trigger_error('你没有权限管理', E_USER_ERROR);
		}

		$class_id = get_var(POST_CLASS_URL, 0);

		if (!$class_id)
		{
			trigger_error('没有指定专题', E_USER_ERROR);
		}

		$sql = 'SELECT class_name 
			FROM ' . CLASS_TABLE . '
			WHERE class_id = ' . $class_id;

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法获取专题信息', E_USER_WARNING);
		}

		if ( !($class_data = $db->sql_fetchrow($result)) )
		{
			trigger_error('专题不存在', E_USER_ERROR);
		}

		if (isset($_POST['classname']))
		{
			$classname = get_var('classname', '');

			if (empty($classname))
			{
				trigger_error('专题名称不能为空', E_USER_ERROR);
			}

			$sql = "UPDATE " . CLASS_TABLE . " 
				SET class_name = '" . $db->sql_escape($classname) . "' 
				WHERE class_id = $class_id";

			if ( $db->sql_query($sql) )
			{
				redirect(append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id));
			}
			else
			{
				trigger_error('无法修改专题信息', E_USER_WARNING);
			}
		}		

		$page_title = $class_data['class_name'] . '_修改专题';

		page_header($page_title);

		page_jump();

		$template->set_filenames(array(
			'edit' => 'class/class_edit.tpl')
		);

		$template->assign_vars(array(
			'CLASS_NAME'		=> $class_data['class_name'],
			'FORUM_NAME' 		=> $forum_row['forum_name'],
			'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL ."=$forum_id"),
			'U_FORUM'			=> append_sid('forum.php'),
			'U_CLASS'			=> append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id),
			'S_CLASS_ACTION'	=> append_sid('viewclass.php?mode=edit&' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $class_id))
		);

		$template->pparse('edit');

		page_footer();

		break;

	// 删除专题
	case 'delete':

		if (!$is_auth['auth_mod'])
		{
			trigger_error('你没有权限删除专题', E_USER_ERROR);
		}

		$delete_id = get_var(POST_CLASS_URL, 0);

		if (!$delete_id)
		{
			trigger_error('没有指定要删除的专题', E_USER_ERROR);
		}

		if ( isset($_POST['cancel']) )
		{
			redirect(append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id, true));
		}

		$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;
		
		if( !$confirm )
		{
			$page_title = '删除专题';

			page_header($page_title);
			
			$template->set_filenames(array(
				'confirm' => 'confirm_body.tpl')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '删除',
				'MESSAGE_TEXT'		=> '请确认是否删除该专题',
				'L_YES' 			=> '是',
				'L_NO' 				=> '否',
				'S_CONFIRM_ACTION' 	=> append_sid('viewclass.php?mode=delete&' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $delete_id))
			);

			$template->pparse('confirm');

			page_footer();
		}

		$sql = 'DELETE FROM ' . CLASS_TABLE . '
			 WHERE class_id = ' . $delete_id;

		if ( !$db->sql_query($sql) )
		{
			trigger_error('无法删除专题数据', E_USER_WARNING);
		}

		redirect(append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id, true));
		
		break;

	// 显示专题列表
	case 'list':

		$page_title = '专题列表';

		page_header($page_title);
		
		page_jump();

		// 分页管理
		$per 	= $board_config['topics_per_page'];
		$start 	= get_pagination_start($per);

		// 找出此论坛的专题
		$sql = "SELECT SQL_CALC_FOUND_ROWS class_id, class_name, class_forum
			FROM " . CLASS_TABLE . " 
			WHERE class_forum = $forum_id
			LIMIT $start, $per";

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法获取专题信息', E_USER_WARNING);
		}

		// 取得专题信息
		$classdata = $db->sql_fetchrowset($result);


		$template->set_filenames(array(
			'body' => 'class/class_list.tpl')
		);

		$total_class = count($classdata);

		for ($i=0; $i < $total_class; $i++)
		{ 
			$row_class = (($i % 2) == 0) ? 'row1' : 'row2';
			$template->assign_block_vars('class_list', array(
				'NUMBER'		=> $i + $start + 1,
				'ROW_CLASS'		=> $row_class,
				'CLASS_NAME' 	=> $classdata[$i]['class_name'],
				'U_CLASS'		=> append_sid('viewclass.php?' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $classdata[$i]['class_id']))
			);

			if ($is_auth['auth_mod'])
			{
				$template->assign_block_vars('class_list.is_mod', array(
					'U_EDIT_CLASS'		=> append_sid('viewclass.php?mode=edit&' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $classdata[$i]['class_id']),
					'U_DELETE_CLASS'	=> append_sid('viewclass.php?mode=delete&' . POST_FORUM_URL . '=' . $forum_id . '&' . POST_CLASS_URL . '=' . $classdata[$i]['class_id']))
				);
			}
		}

		if (!$db->sql_numrows($result))
		{
			$template->assign_block_vars('not_class', array());
		}

		$sql = "SELECT found_rows() AS classrow"; 

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法查询专题信息！', E_USER_WARNING);
		}

		if ( !$class = $db->sql_fetchrow($result) )
		{
			trigger_error('无法取得专题数据', E_USER_ERROR);
		}

		$class_count = $class['classrow'];

		if ($is_auth['auth_mod'])
		{
			$template->assign_block_vars('create_class', array());
		}

		$template->assign_vars(array(
			'FORUM_NAME'		=> $forum_row['forum_name'],
			'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL ."=$forum_id"),
			'U_FORUM'			=> append_sid('forum.php'),
			'S_CREATE_ACTION'	=> append_sid('viewclass.php?mode=create&' . POST_FORUM_URL . '=' . $forum_id),
			'PAGINATION' 		=> generate_pagination("viewclass.php?mode=list&" . POST_FORUM_URL . "=$forum_id", $class_count, $per, $start))
		);

		$template->pparse('body');

		page_footer();

		break;
	
	// 显示专题帖子
	default:
		$class_id = get_var(POST_CLASS_URL, 0);

		if (!$class_id)
		{
			trigger_error('没有指定专题', E_USER_ERROR);
		}
		
		$per 	= $board_config['topics_per_page'];
		$start 	= get_pagination_start($per);

		$sql = "SELECT SQL_CALC_FOUND_ROWS t.*, u.username, u.user_id, u2.username as user2, u2.user_id as id2, p.post_username, p2.post_username AS post_username2, p2.post_time 
			FROM " . TOPICS_TABLE . " t, " . USERS_TABLE . " u, " . POSTS_TABLE . " p, " . POSTS_TABLE . " p2, " . USERS_TABLE . " u2
			WHERE t.forum_id = $forum_id
				AND t.topic_poster = u.user_id
				AND p.post_id = t.topic_first_post_id
				AND p2.post_id = t.topic_last_post_id
				AND u2.user_id = p2.poster_id 
				AND topic_class = $class_id
			ORDER BY t.topic_type DESC, t.topic_last_post_id DESC 
			LIMIT $start, $per";

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法获取专题帖子信息', E_USER_WARNING);
		}
		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);
		if ($total_topics = $db->sql_numrows($result))
		{
			$i = 0;
			while( $topic_class = $db->sql_fetchrow($result) )
			{
				// 主题标题
				$topic_title 		= ( count($orig_word) ) ? str_replace($orig_word, $replacement_word, $topic_class['topic_title']) : $topic_class['topic_title'];
				$topic_type 		= $topic_class['topic_type'];
				$views				= $topic_class['topic_views'];
				$replies 			= $topic_class['topic_replies'];
				// 公告帖子
				if( $topic_type == POST_ANNOUNCE )
				{
					$topic_type = make_style_image('topic_announcement');
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
				if( $topic_class['topic_vote'] )
				{
					$topic_type = make_style_image('topic_poll', '投票帖子', '【投】');
				}
				
				// 从其他论坛移动过来的帖子
				if( $topic_class['topic_status'] == TOPIC_MOVED )
				{
					$topic_type = make_style_image('topic_move', '移动过来的帖子', '【移】');
					$topic_id = $topic_class['topic_moved_id'];
				}
				
				// 附件图标
				if (intval($topic_class['topic_attachment']) == 0 || (!($is_auth['auth_download'] && $is_auth['auth_view'])) || intval($board_config['disable_mod']) || $board_config['topic_icon'] == '')
				{
					$attachment_image = '';
				}
				else
				{

					$attachment_image = '<img src="' . $board_config['topic_icon'] . '" alt="附" title="带有附件的帖子"/>';
				}
				
				$topic_id 			= $topic_class['topic_id'];
				$row_class 			= (($i % 2) == 0) ? 'row1' : 'row2';
				$view_topic_url 	= append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id");
				$last_post_author 	= ( $topic_class['id2'] == ANONYMOUS ) ? ( ($topic_class['post_username2'] != '' ) ? $topic_class['post_username2'] . ' ' : '匿名用户' . ' ' ) :  $topic_class['user2']  ;
				$s_last_post 		= '<a href="' . append_sid("viewtopic.php?"  . POST_POST_URL . '=' . $topic_class['topic_last_post_id']) . '#' . $topic_class['topic_last_post_id'] . '">»</a>';
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

		$page_title = $forum_row['forum_name'] . '的精华帖子';
		page_header($page_title);
		
		page_jump();

		$template->set_filenames(array(
			'body' => 'class/viewclass_body.tpl')
		);

		// 计算出查询的记录总数
		$sql = 'SELECT found_rows() AS rowcount'; 

		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法统计在线用户！', E_USER_WARNING);
		}

		$total_class 		= $db->sql_fetchrow($result);
		$total_all_class 	= $total_class['rowcount'];

		$template->assign_vars(array(
			'FORUM_NAME' 		=> $forum_row['forum_name'],
			'U_VIEW_FORUM' 		=> append_sid("viewforum.php?" . POST_FORUM_URL ."=$forum_id"),
			'U_FORUM'			=> append_sid('forum.php'),
			'U_CLASS'			=> append_sid('viewclass.php?mode=list&' . POST_FORUM_URL . '=' . $forum_id),
			'PAGINATION' 		=> generate_pagination("viewclass.php?" . POST_FORUM_URL . "=$forum_id&amp;&amp;" . POST_CLASS_URL . "=$class_id&amp;", $total_all_class, $board_config['topics_per_page'], $start))
		);

		$template->pparse('body');

		page_footer();

		break;
}

?>