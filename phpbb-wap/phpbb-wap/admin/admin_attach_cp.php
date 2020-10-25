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

if (!empty($setmodules))
{
	$filename = basename(__FILE__);
	$module['附件']['附件统计'] = $filename;
	return;
}
define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

if (!intval($board_config['allow_ftp_upload']))
{
	if ( ($board_config['upload_dir'][0] == '/') || ( ($board_config['upload_dir'][0] != '/') && ($board_config['upload_dir'][1] == ':') ) )
	{
		$upload_dir = $board_config['upload_dir'];
	}
	else
	{
		$upload_dir = '../' . $board_config['upload_dir'];
	}
}
else
{
	$upload_dir = $board_config['download_path'];
}

require(ROOT_PATH . 'includes/functions/selects.php');
require(ROOT_PATH . 'includes/attach/functions_admin.php');

$start 		= get_var('start', 0);
$sort_order = get_var('order', 'ASC');
$sort_order = ($sort_order == 'ASC') ? 'ASC' : 'DESC';
$mode 		= get_var('mode', '');
$mode 		= htmlspecialchars($mode);
$view 		= get_var('view', '');
$uid 		= (isset($_POST['u_id'])) ? get_var('u_id', 0) : get_var('uid', 0);

$view = (isset($_POST['search']) && $_POST['search']) ? 'attachments' : $view;

if ($view == 'username')
{
	$mode_types_text = array('用户名', '附件总数', '附件总大小');
	$mode_types = array('username', 'attachments', 'filesize');

	if (!$mode)
	{
		$mode = 'attachments';
		$sort_order = 'DESC';
	}
}
else if ($view == 'attachments')
{
	$mode_types_text = array('文件名称', '文件描述', '文件扩展名', '文件大小', '下载次数', '发表时间'/* ,'附件帖子'*/);
	$mode_types = array('real_filename', 'comment', 'extension', 'filesize', 'downloads', 'post_time'/*, 'posts'*/);

	if (!$mode)
	{
		$mode = 'real_filename';
		$sort_order = 'ASC';
	}
}
else if ($view == 'search')
{
	$mode_types_text = array('文件名称', '文件描述', '文件后缀', '文件大小', '下载数量', '发表时间', /*$lang['Sort_Posts']*/);
	$mode_types = array('real_filename', 'comment', 'extension', 'filesize', 'downloads', 'post_time'/*, 'posts'*/);

	$sort_order = 'DESC';
}
else
{
	$view = 'stats';
	$mode_types_text = array();
	$sort_order = 'ASC';
}

$do_pagination = ($view != 'stats' && $view != 'search') ? true : false;

$order_by = '';

if ($view == 'username')
{
	switch ($mode)
	{
		case 'username':
			$order_by = 'ORDER BY u.username ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;

		case 'attachments':
			$order_by = 'ORDER BY total_attachments ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		case 'filesize':
			$order_by = 'ORDER BY total_size ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		default:
			$mode = 'attachments';
			$sort_order = 'DESC';
			$order_by = 'ORDER BY total_attachments ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	}
}
else if ($view == 'attachments')
{
	switch ($mode)
	{
		case 'filename':
			$order_by = 'ORDER BY a.real_filename ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;

		case 'comment':
			$order_by = 'ORDER BY a.comment ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		case 'extension':
			$order_by = 'ORDER BY a.extension ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		case 'filesize':
			$order_by = 'ORDER BY a.filesize ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		case 'downloads':
			$order_by = 'ORDER BY a.download_count ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		case 'post_time':
			$order_by = 'ORDER BY a.filetime ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
		
		default:
			$mode = 'a.real_filename';
			$sort_order = 'ASC';
			$order_by = 'ORDER BY a.real_filename ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	}
}

$view_types_text 	= array('附件统计', '搜索附件', '用户附件', '所有附件');
$view_types 		= array('stats', 'search', 'username', 'attachments');

$select_view = '<select name="view">';

for($i = 0; $i < count($view_types_text); $i++)
{
	$selected = ($view == $view_types[$i]) ? ' selected="selected"' : '';
	$select_view .= '<option value="' . $view_types[$i] . '"' . $selected . '>' . $view_types_text[$i] . '</option>';
}
$select_view .= '</select>';

if (count($mode_types_text) > 0)
{
	$select_sort_mode = '<select name="mode">';

	for($i = 0; $i < count($mode_types_text); $i++)
	{
		$selected = ($mode == $mode_types[$i]) ? ' selected="selected"' : '';
		$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
	}
	$select_sort_mode .= '</select>';
}

$select_sort_order = '<select name="order">';
if ($sort_order == 'ASC')
{
	$select_sort_order .= '<option value="ASC" selected="selected">递增</option><option value="DESC">递减</option>';
}
else
{
	$select_sort_order .= '<option value="ASC">递增</option><option value="DESC" selected="selected">递减</option>';
}
$select_sort_order .= '</select>';

$submit_change 	= (isset($_POST['submit_change'])) ? TRUE : FALSE;
$delete 		= (isset($_POST['delete'])) ? TRUE : FALSE;
$delete_id_list	= get_var('delete_id_list', array(0));
$confirm 		= (isset($_POST['confirm'])) ? TRUE : FALSE;

if ($confirm && count($delete_id_list) > 0)
{
	$attachments = array();

	delete_attachment(0, $delete_id_list);
}
else if ($delete && count($delete_id_list) > 0)
{
	$hidden_fields = '<input type="hidden" name="view" value="' . $view . '" />';
	$hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';
	$hidden_fields .= '<input type="hidden" name="order" value="' . $sort_order . '" />';
	$hidden_fields .= '<input type="hidden" name="u_id" value="' . $uid . '" />';
	$hidden_fields .= '<input type="hidden" name="start" value="' . $start . '" />';

	for ($i = 0; $i < count($delete_id_list); $i++)
	{
		$hidden_fields .= '<input type="hidden" name="delete_id_list[]" value="' . $delete_id_list[$i] . '" />';
	}

	$template->set_filenames(array(
		'confirm' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE'		=> '删除附件',
		'MESSAGE_TEXT'		=> '请确认是否删除？',

		'L_YES'				=> '是',
		'L_NO'				=> '否',

		'S_CONFIRM_ACTION'	=> append_sid('admin_attach_cp.php'),
		'S_HIDDEN_FIELDS'	=> $hidden_fields)
	);

	$template->pparse('confirm');
	
	page_footer();
}

$template->assign_vars(array(
	'S_VIEW_SELECT'	=> $select_view,
	'S_MODE_ACTION'	=> append_sid('admin_attach_cp.php'))
);

if ($submit_change && $view == 'attachments')
{
	$attach_change_list = get_var('attach_id_list', array(0));
	$attach_comment_list = get_var('attach_comment_list', array(''));
	$attach_download_count_list = get_var('attach_count_list', array(0));

	$attachments = array();

	for ($i = 0; $i < count($attach_change_list); $i++)
	{
		$attachments['_' . $attach_change_list[$i]]['comment'] = $attach_comment_list[$i];
		$attachments['_' . $attach_change_list[$i]]['download_count'] = $attach_download_count_list[$i];
	}

	$sql = 'SELECT *
		FROM ' . ATTACHMENTS_DESC_TABLE . '
		ORDER BY attach_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t get Attachment informations', E_USER_WARNING);
	}

	while ($attachrow = $db->sql_fetchrow($result))
	{
		if (isset($attachments['_' . $attachrow['attach_id']]))
		{
			if ($attachrow['comment'] != $attachments['_' . $attachrow['attach_id']]['comment'] || $attachrow['download_count'] != $attachments['_' . $attachrow['attach_id']]['download_count'])
			{
				$sql = "UPDATE " . ATTACHMENTS_DESC_TABLE . " 
					SET comment = '" . $db->sql_escape($attachments['_' . $attachrow['attach_id']]['comment']) . "', download_count = " . (int) $attachments['_' . $attachrow['attach_id']]['download_count'] . "
					WHERE attach_id = " . (int) $attachrow['attach_id'];
				
				if (!$db->sql_query($sql))
				{
					trigger_error('Couldn\'t update Attachments Informations', E_USER_WARNING);
				}
			}
		}
	}
	$db->sql_freeresult($result);
}

if ($view == 'stats')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_cp_body.tpl')
	);

	$upload_dir_size = get_formatted_dirsize();

	if ($board_config['attachment_quota'] >= 1048576)
	{
		$attachment_quota = round($board_config['attachment_quota'] / 1048576 * 100) / 100 . ' MB';
	}
	else if ($board_config['attachment_quota'] >= 1024)
	{
		$attachment_quota = round($board_config['attachment_quota'] / 1024 * 100) / 100 . ' KB';
	}
	else
	{
		$attachment_quota = $board_config['attachment_quota'] . ' Bytes';
	}

	$sql = "SELECT count(*) AS total
		FROM " . ATTACHMENTS_DESC_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error getting total attachments', E_USER_WARNING);
	}

	$total = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$number_of_attachments = $total['total'];

	$sql = "SELECT post_id
		FROM " . ATTACHMENTS_TABLE . "
		WHERE post_id <> 0
		GROUP BY post_id";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error getting total posts', E_USER_WARNING);
	}

	$number_of_posts = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$sql = "SELECT privmsgs_id
		FROM " . ATTACHMENTS_TABLE . "
		WHERE privmsgs_id <> 0
		GROUP BY privmsgs_id";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error getting total private messages', E_USER_WARNING);
	}

	$number_of_pms = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$sql = "SELECT p.topic_id
		FROM " . ATTACHMENTS_TABLE . " a, " . POSTS_TABLE . " p
		WHERE a.post_id = p.post_id
		GROUP BY p.topic_id";

	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Error getting total topics', E_USER_WARNING);
	}

	$number_of_topics = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$sql = "SELECT user_id_1
		FROM " . ATTACHMENTS_TABLE . "
		WHERE (post_id <> 0)
		GROUP BY user_id_1";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error getting total users', E_USER_WARNING);
	}

	$number_of_users = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'TOTAL_FILESIZE'			=> $upload_dir_size,
		'ATTACH_QUOTA'				=> $attachment_quota,
		'NUMBER_OF_ATTACHMENTS'		=> $number_of_attachments,
		'NUMBER_OF_POSTS'			=> $number_of_posts,
		'NUMBER_OF_TOPICS'			=> $number_of_topics,
		'NUMBER_OF_USERS'			=> $number_of_users)
	);

}

if ($view == 'search')
{
	$sql = "SELECT c.cat_title, c.cat_id, f.forum_name, f.forum_id  
		FROM " . CATEGORIES_TABLE . " c, " . FORUMS_TABLE . " f
		WHERE f.cat_id = c.cat_id 
		ORDER BY c.cat_id, f.forum_order";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not obtain forum_name/forum_id', E_USER_WARNING);
	}

	$s_forums = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$s_forums .= '<option value="' . $row['forum_id'] . '">' . $row['forum_name'] . '</option>';

		if( empty($list_cat[$row['cat_id']]) )
		{
			$list_cat[$row['cat_id']] = $row['cat_title'];
		}
	}

	if ($s_forums != '')
	{
		$s_forums = '<option value="0">全部论坛</option>' . $s_forums;

		$s_categories = '<option value="0">全部论坛栏目</option>';

		foreach ($list_cat as $cat_id => $cat_title)
		{
			$s_categories .= '<option value="' . $cat_id . '">' . $cat_title . '</option>';
		}
	}
	else
	{
		trigger_error('没有创建论坛', E_USER_ERROR);
	}
	
	$template->set_filenames(array(
		'body' => 'admin/attach_cp_search.tpl')
	);

	$template->assign_vars(array(
		'S_SEARCH_ACTION'			=> append_sid('admin_attach_cp.php?mode=search'),
		'S_FORUM_OPTIONS'			=> $s_forums, 
		'S_CATEGORY_OPTIONS'		=> $s_categories,
		'S_SORT_OPTIONS'			=> $select_sort_mode,
		'S_SORT_ORDER'				=> $select_sort_order)
	);
}

if ($view == 'username')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_cp_user.tpl')
	);

	$template->assign_vars(array(
		'S_MODE_SELECT'			=> $select_sort_mode,
		'S_ORDER_SELECT'		=> $select_sort_order)
	);

	$sql = "SELECT u.username, a.user_id_1 as user_id, count(*) as total_attachments
		FROM " . ATTACHMENTS_TABLE . " a, " . USERS_TABLE . " u
		WHERE a.user_id_1 = u.user_id
		GROUP BY a.user_id_1, u.username"; 

	if ($mode != 'filesize')
	{
		$sql .= ' ' . $order_by;
	}
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t query attachments', E_USER_WARNING);
	}

	$members = $db->sql_fetchrowset($result);
	$num_members = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_members > 0)
	{
		for ($i = 0; $i < $num_members; $i++)
		{
			$sql = "SELECT attach_id 
				FROM " . ATTACHMENTS_TABLE . "
				WHERE user_id_1 = " . intval($members[$i]['user_id']) . " 
				GROUP BY attach_id";
		
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Couldn\'t query attachments', E_USER_WARNING);
			}
		
			$attach_ids = $db->sql_fetchrowset($result);
			$num_attach_ids = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			$attach_id = array();

			for ($j = 0; $j < $num_attach_ids; $j++)
			{
				$attach_id[] = intval($attach_ids[$j]['attach_id']);
			}
			
			if (count($attach_id))
			{
				$sql = "SELECT sum(filesize) as total_size
					FROM " . ATTACHMENTS_DESC_TABLE . "
					WHERE attach_id IN (" . implode(', ', $attach_id) . ")";

				if ( !($result = $db->sql_query($sql)) )
				{
					trigger_error('Couldn\'t query attachments', E_USER_WARNING);
				}

				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$members[$i]['total_size'] = (int) $row['total_size'];
			}
		}
		
		if ($mode == 'filesize')
		{
			$members = sort_multi_array($members, 'total_size', $sort_order, FALSE);
			$members = limit_array($members, $start, $board_config['topics_per_page']);
		}
		for ($i = 0; $i < count($members); $i++)
		{
			$username = $members[$i]['username'];
			$total_attachments = $members[$i]['total_attachments'];
			$total_size = $members[$i]['total_size'];

			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

			$template->assign_block_vars('memberrow', array(
				'ROW_NUMBER'		=> $i + start + 1,
				'ROW_CLASS'			=> $row_class,
				'USERNAME'			=> $username,
				'TOTAL_ATTACHMENTS'	=> $total_attachments,
				'TOTAL_SIZE'		=> round(($total_size / MEGABYTE), 2),
				'U_VIEW_MEMBER'		=> append_sid('admin_attach_cp.php' . '?view=attachments&amp;uid=' . $members[$i]['user_id']))
			);
		}
	}
	else
	{
		$template->assign_block_vars('empty_memberrow', array());
	}

	$sql = "SELECT user_id_1
		FROM " . ATTACHMENTS_TABLE . "
		GROUP BY user_id_1";

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Error getting total users', E_USER_WARNING);
	}

	$total_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);
}

if ($view == 'attachments')
{
	$user_based = ($uid) ? TRUE : FALSE;
	$search_based = (isset($_POST['search']) && $_POST['search']) ? TRUE : FALSE;
	
	$template->set_filenames(array(
		'body' => 'admin/attach_cp_attachments.tpl')
	);

	$template->assign_vars(array(
		'S_MODE_SELECT'			=> $select_sort_mode,
		'S_ORDER_SELECT'		=> $select_sort_order)
	);

	$total_rows = 0;

	if ($user_based)
	{
		$sql = "SELECT username 
			FROM " . USERS_TABLE . " 
			WHERE user_id = " . intval($uid);

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Error getting username', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$username = $row['username'];

		$s_hidden = '<input type="hidden" name="u_id" value="' . intval($uid) . '" />';
	
		$template->assign_block_vars('switch_user_based', array());

		$template->assign_vars(array(
			'S_USER_HIDDEN'			=> $s_hidden,
			'STATISTICS_FOR_USER'	=> $username . '的附件列表')
		);

		$sql = "SELECT attach_id 
			FROM " . ATTACHMENTS_TABLE . "
			WHERE user_id_1 = " . intval($uid) . "
			GROUP BY attach_id";
		
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Couldn\'t query attachments', E_USER_WARNING);
		}
		
		$attach_ids = $db->sql_fetchrowset($result);
		$num_attach_ids = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ($num_attach_ids == 0)
		{
			trigger_error('For some reason no Attachments are assigned to the User ' . $username, E_USER_ERROR);
		}
		
		$total_rows = $num_attach_ids;

		$attach_id = array();

		for ($j = 0; $j < $num_attach_ids; $j++)
		{
			$attach_id[] = intval($attach_ids[$j]['attach_id']);
		}
			
		$sql = "SELECT a.*
			FROM " . ATTACHMENTS_DESC_TABLE . " a
			WHERE a.attach_id IN (" . implode(', ', $attach_id) . ") " .
			$order_by;
		
	}
	else if ($search_based)
	{
		$attachments = search_attachments($order_by, $total_rows);
	}
	else
	{
		$sql = "SELECT a.*
			FROM " . ATTACHMENTS_DESC_TABLE . " a " .
			$order_by;
	}

	if (!$search_based)
	{
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Couldn\'t query attachments', E_USER_WARNING);
		}

		$attachments = $db->sql_fetchrowset($result);
		$num_attach = $db->sql_numrows($result);
		$db->sql_freeresult($result);
	}
	
	if (count($attachments) > 0)
	{
		for ($i = 0; $i < count($attachments); $i++)
		{
			$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" />';

			for ($j = 0; $j < count($delete_id_list); $j++)
			{
				if ($delete_id_list[$j] == $attachments[$i]['attach_id'])
				{
					$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" checked="checked" />';
					break;
				}
			}

			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

			$post_titles = array();

			$sql = "SELECT *
				FROM " . ATTACHMENTS_TABLE . "
				WHERE attach_id = " . intval($attachments[$i]['attach_id']);

			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Couldn\'t query attachments', E_USER_WARNING);
			}

			$ids = $db->sql_fetchrowset($result);
			$num_ids = $db->sql_numrows($result);
			$db->sql_freeresult($result);
			
			for ($j = 0; $j < $num_ids; $j++)
			{
				if ($ids[$j]['post_id'] != 0)
				{
					$sql = "SELECT t.topic_title
						FROM " . TOPICS_TABLE . " t, " . POSTS_TABLE . " p
						WHERE p.post_id = " . intval($ids[$j]['post_id']) . " AND p.topic_id = t.topic_id
						GROUP BY t.topic_id, t.topic_title";

					if (!($result = $db->sql_query($sql)))
					{
						trigger_error('Couldn\'t query topic', E_USER_WARNING);
					}

					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
			
					$post_title = $row['topic_title'];

					if (mb_strlen($post_title, 'UTF-8') > 20)
					{
						$post_title = mb_substr($post_title, 0, 20, 'UTF-8') . '...';
					}

					$view_topic = append_sid(ROOT_PATH . 'viewtopic.php' . '?' . POST_POST_URL . '=' . $ids[$j]['post_id'] . '#' . $ids[$j]['post_id']);

					$post_titles[] = '<a href="' . $view_topic . '" class="gen">' . $post_title . '</a>';
				}
				else
				{
					$post_titles[] = '信息列表';
				}
			}

			$post_titles = implode('<br />', $post_titles);

			$hidden_field = '<input type="hidden" name="attach_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" />';

			$template->assign_block_vars('attachrow', array(
				'ROW_NUMBER'	=> $i + $start + 1,
				'ROW_CLASS'		=> $row_class,
				'FILENAME'		=> $attachments[$i]['real_filename'],
				'COMMENT'		=> $attachments[$i]['comment'],
				'EXTENSION'		=> $attachments[$i]['extension'],
				'SIZE'			=> round(($attachments[$i]['filesize'] / MEGABYTE), 2),
				'DOWNLOAD_COUNT'=> $attachments[$i]['download_count'],
				'POST_TIME'		=> create_date($board_config['default_dateformat'], $attachments[$i]['filetime'], $board_config['board_timezone']),
				'POST_TITLE'	=> $post_titles,

				'S_DELETE_BOX'	=> $delete_box,
				'S_HIDDEN'		=> $hidden_field,
				'U_VIEW_ATTACHMENT'	=> append_sid(ROOT_PATH . 'download.php' . '?id=' . $attachments[$i]['attach_id']))
//				'U_VIEW_POST' => ($attachments[$i]['post_id'] != 0) ? append_sid("../viewtopic." . php . "?" . POST_POST_URL . "=" . $attachments[$i]['post_id'] . "#" . $attachments[$i]['post_id']) : '')
			);
			
		}
	}
	else
	{
		$template->assign_block_vars('not_attachrow', array());
	}

	if (!$search_based && !$user_based)
	{
		if ($total_attachments == 0)
		{
			$sql = 'SELECT attach_id 
				FROM ' . ATTACHMENTS_DESC_TABLE;

			if ( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Could not query Attachment Description Table', E_USER_WARNING);
			}

			$total_rows = $db->sql_numrows($result);
			$db->sql_freeresult($result);
		}
	}
}

$template->assign_block_vars('view_all_attch', array());

if ($do_pagination && $total_rows > $board_config['topics_per_page'])
{
	$pagination = generate_pagination('admin_attach_cp.php' . '?view=' . $view . '&amp;mode=' . $mode . '&amp;order=' . $sort_order . '&amp;uid=' . $uid, $total_rows, $board_config['topics_per_page'], $start).'&nbsp;';

	$template->assign_vars(array(
		'PAGINATION'	=> $pagination)
	);
}


$template->pparse('body');

page_footer();

?>