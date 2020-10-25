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

$userdata = $session->start($user_ip, PAGE_VIEWMEMBERS);
init_userprefs($userdata);

$start = get_pagination_start($board_config['topics_per_page']);

if (isset($_GET['admin']))
{
	$page_title = '管理员';
	
	page_header($page_title);
	$exclude_users = '';// 指定不显示的用户
	$template->assign_block_vars('switch_list_staff', array());
	
	$template->set_filenames(array(
		'body' => 'staff_body.tpl')
	);

	$is_auth_ary = array();
	$is_auth_ary = auth(AUTH_VIEW, AUTH_LIST_ALL, $userdata);

	$sql = "SELECT count(*) AS total
		FROM " . FORUMS_TABLE;
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法统计论坛', E_USER_WARNING);
	}
	$total = $db->sql_fetchrow($result);
	$total_forums = $total['total'];

	$sql_forums = "SELECT ug.user_id, f.forum_id, f.forum_name
		FROM ". AUTH_ACCESS_TABLE ." aa, ". USER_GROUP_TABLE ." ug, ". FORUMS_TABLE ." f
		WHERE aa.auth_mod = ". TRUE ." 
			AND ug.group_id = aa.group_id 
			AND f.forum_id = aa.forum_id
        ORDER BY f.forum_order";
				
	if( !$result_forums = $db->sql_query($sql_forums) )
	{
		trigger_error('取法取得论坛信息', E_USER_WARNING);
	}
	while( $row = $db->sql_fetchrow($result_forums) )
	{
		$display_forums = ( $is_auth_ary[$row['forum_id']]['auth_view'] ) ? true : false;
		if( $display_forums )
		{
			$forum_id = $row['forum_id'];
			$staff2[$row['user_id']][$row['forum_id']] = '<a href="'. append_sid("viewforum.php?f=$forum_id") .'">'. $row['forum_name'] .'</a>';
		}
	}
	$db->sql_freeresult($result_forums);

	$level_cat = array(
		'admin' => '超级管理员',
		'mod' 	=> '版主'
	);

	foreach ($level_cat as $level_key => $level_name) 
	{
		// 等级名称
		$template->assign_block_vars('switch_list_staff.user_level', array(
			'USER_LEVEL' => $level_name)
		);

		if ($level_key == 'admin')
		{
			$where = 'user_level = ' . ADMIN;
		}
		else if ($level_key == 'mod')
		{
			$where = 'user_level = ' . MOD;
		}

		$sql_exclude_users = ( !empty($exclude_users) ) ? ' AND user_id NOT IN ('. $exclude_users .')' : '';

		$sql_user = 'SELECT user_id, user_session_time, user_allow_viewonline, user_level, username, user_posts, user_nic_color
			FROM ' . USERS_TABLE . " 
			WHERE $where $sql_exclude_users 
			ORDER BY user_regdate";

		if( !($result_user = $db->sql_query($sql_user)) )
		{
			trigger_error('无法取得用户信息', E_USER_WARNING);
		}

		if( $db->sql_numrows($result_user) )
		{
			$j = 0;
			while( $staff = $db->sql_fetchrow($result_user) )
			{

				$user_id 		= $staff['user_id'];

				$user_status 	= ( $staff['user_session_time'] >= (time() - 60) ) ? (( $staff['user_allow_viewonline'] ) ? '(<span style="color: #0fff0f">在线</span>)' : (( $userdata['user_level'] == ADMIN || $userdata['user_id'] == $user_id ) ? '(<span style="color: #0fff0f">在线</span>)' : '')) : '';
				
				$forums 		= '';

				if( !empty($staff2[$staff['user_id']]) )
				{
					asort($staff2[$staff['user_id']]);

					$forums = implode(', ',$staff2[$staff['user_id']]);

				}
				
				if ( $total_forums == count($staff2[$staff['user_id']]) )
				{
					$forums = '<a href="'. append_sid("forum.php") .'">全部论坛</a>';
				}

				if ( $staff['user_level'] == ADMIN )
				{
					$forums = '';
				}

				$template->assign_block_vars('switch_list_staff.user_level.staff', array(
					'USERNAME' 		=> '<span style="color: ' . $staff['user_nic_color'] . '">' . $staff['username'] . '</span>',
					'POSTS' 		=> $staff['user_posts'],
					'USER_STATUS' 	=> $user_status,
					'U_PROFILE' 	=> append_sid("ucp.php?mode=viewprofile&amp;". POST_USERS_URL ."=$user_id"),
					'FORUMS' 		=> $forums,
				));
				$j++;
			}

			$db->sql_freeresult($result_user);

		}
		else
		{
			if ($level_key == 'admin')
			{
				$template->assign_block_vars('switch_list_staff.user_level.no_admin', array());
			}
			else if ($level_key == 'mod')
			{
				$template->assign_block_vars('switch_list_staff.user_level.no_mod', array());
			}
		}

	}

	$template->pparse('body');
	page_footer();

}
else
{
	if ( isset($_GET['mode']) || isset($_POST['mode']) )
	{
		$mode = ( isset($_POST['mode']) ) ? htmlspecialchars($_POST['mode']) : htmlspecialchars($_GET['mode']);
	}
	else
	{
		$mode = 'posts';
	}

	if(isset($_POST['order']))
	{
		$sort_order = ($_POST['order'] == 'ASC') ? 'ASC' : 'DESC';
	}
	else if(isset($_GET['order']))
	{
		$sort_order = ($_GET['order'] == 'ASC') ? 'ASC' : 'DESC';
	}
	else
	{
		$sort_order = 'DESC';
	}

	$mode_types_text = array('注册时间', '用户名', '发表帖子', '金币数量', 'TOP 10');
	$mode_types = array('joined', 'username', 'posts', 'money', 'topten');
	$lang = array('joined_posts' => '帖子', 'username_posts' => '帖子', 'posts_posts' => '帖子', 'money_posts' => $board_config['points_name'], 'topten_posts' => '帖子');

	$select_sort_mode = '<select name="mode">';
	for($i = 0; $i < count($mode_types_text); $i++)
	{
		$selected = ( $mode == $mode_types[$i] ) ? ' selected="selected"' : '';
		$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
	}
	$select_sort_mode .= '</select>';

	$select_sort_order = '<select name="order">';
	if($sort_order == 'ASC')
	{
		$select_sort_order .= '<option value="ASC" selected="selected">从低到高</option><option value="DESC">从高到低</option>';
	}
	else
	{
		$select_sort_order .= '<option value="ASC">从低到高</option><option value="DESC" selected="selected">从高到底</option>';
	}
	$select_sort_order .= '</select>';
	
	$page_title = '会员';
	page_header($page_title);
	
	$template->set_filenames(array(
		'body' => 'memberlist_body.tpl')
	);

	$template->assign_vars(array(
		'U_SEARCH_USER' 		=> append_sid("search.php?mode=searchuser"),

		'S_MODE_SELECT' 		=> $select_sort_mode,
		'S_ORDER_SELECT' 		=> $select_sort_order,
		'S_MODE_ACTION' 		=> append_sid("memberlist.php"))
	);

	switch( $mode )
	{
		case 'joined':
			$order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'username':
			$order_by = "username $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'posts':
			$order_by = "user_posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'money':
			$order_by = "user_points $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
		case 'topten':
			$order_by = "user_posts $sort_order LIMIT 10";
			break;
		default:
			$order_by = "user_posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
			break;
	}

	$sql = "SELECT username, user_id, user_level, user_posts, user_points, user_nic_color 
		FROM " . USERS_TABLE . "
		WHERE user_id <> " . ANONYMOUS . " 
		ORDER BY $order_by";
	if( !($result = $db->sql_query($sql)) )
	{
		trigger_error('无法查询用户', E_USER_WARNING);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$number = $i + $start + 1;
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
			$username = $row['username'];
			$user_color = ( !empty($row['user_nic_color']) ) ? ' style="color: ' . $row['user_nic_color'] . '"' : '';
			$user_id = $row['user_id'];
			$posts = ( $mode == 'money' ) ? $row['user_points'] : $row['user_posts'];

			$template->assign_block_vars('memberrow', array(
				'USERNAME' => $username,
				'NUMBER' => $number,
				'ROW_CLASS' => $row_class,
				'L_POSTS' => isset($lang[$mode . '_posts']) ? $lang[$mode . '_posts'] : $lang['joined_posts'],
				'POSTS' => $posts,
				'COLOR' => $user_color,
				'U_VIEWPROFILE' => append_sid("ucp.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	}

	if ( $mode != 'topten' || $board_config['topics_per_page'] < 10 )
	{
		$sql = "SELECT count(*) AS total
			FROM " . USERS_TABLE . "
			WHERE user_id <> " . ANONYMOUS;

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法统计用户数量', E_USER_WARNING);
		}

		if ( $total = $db->sql_fetchrow($result) )
		{
			$total_members = $total['total'];
			$pagination = generate_pagination("memberlist.php?mode=$mode&amp;order=$sort_order", $total_members, $board_config['topics_per_page'], $start). '';
		}
		$db->sql_freeresult($result);
	}
	else
	{
		$pagination = '';
		$total_members = 10;
	}

	$template->assign_vars(array(
		'PAGINATION' => $pagination)
	);

	$template->pparse('body');
	page_footer();
}

?>