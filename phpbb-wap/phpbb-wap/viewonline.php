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

// 初始化 $userdata 
$userdata = $session->start($user_ip, PAGE_VIEWONLINE);
init_userprefs($userdata);

// 取得分页函数所需的值
$per 	= $board_config['posts_per_page'];
$start 	= get_pagination_start($per);

$page_title = '在线状态';

page_header($page_title);

$template->set_filenames(array(
	'body' => 'viewonline_body.tpl')
);

// 获取论坛的信息
$sql = 'SELECT forum_name, forum_id
	FROM ' . FORUMS_TABLE;
if ( $result = $db->sql_query($sql) )
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$forum_data[$row['forum_id']] = $row['forum_name'];
	}
}
else
{
	trigger_error('无法查询 ' . FORUMS_TABLE . ' 表', E_USER_WARNING);
}

$hidden_user_sql = ( $userdata['user_level'] == ADMIN ) ? '' : 'AND u.user_allow_viewonline NOT IN (' . HIDDEN_USER . ')';

// 查询出所有活动的用户
$sql = 'SELECT SQL_CALC_FOUND_ROWS u.user_id, u.username, u.user_nic_color, u.user_allow_viewonline, u.user_level, u.user_avatar_type, u.user_avatar, u.user_allowavatar, s.session_logged_in, s.session_time, s.session_page, s.session_ip
	FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . ' s
	WHERE u.user_id = s.session_user_id
		AND s.session_time >= ' . ( time() - 3600 ) . '
		' . $hidden_user_sql . ' 
	ORDER BY s.session_time DESC
	LIMIT ' . $start . ', ' . $per;
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法获取用户的session信息', E_USER_WARNING);
}

$prev_user 			= 0;
$prev_ip 			= '';
$which_counter		= 0;
$view_online 		= false;

while ( $row = $db->sql_fetchrow($result) )
{
	$user_id = $row['user_id'];
	
	// 已登录用户
	if ( $row['session_logged_in'] ) 
	{

		$username = ($row['user_nic_color'] == '') ? $row['username'] : '<span style="color: ' . $row['user_nic_color'] . '">' . $row['username'] . '</span>';

		// 隐身用户
		if ( !$row['user_allow_viewonline'] )
		{
			$view_online 	= ( $userdata['user_level'] == ADMIN ) ? true : false;
			$username 		= '' . $username . ' [隐身]';
		}
		// 在线用户
		else
		{
			$view_online = true;
		}

		$prev_user 		= $user_id;
		
	}
	else
	{
		// 游客
		$username 		= '匿名用户';
		$view_online 	= true;
	}

	$prev_ip = $row['session_ip'];

	if ( $view_online )
	{
		if ( $row['session_page'] < 1 )
		{
			switch( $row['session_page'] )
			{
				case PAGE_INDEX:
					$location = '首页';
					$location_url = 'index.php';
					break;
				case PAGE_POSTING:
					$location = '发表帖子';
					$location_url = 'index.php';
					break;
				case PAGE_LOGIN:
					$location = '登录';
					$location_url = 'index.php';
					break;
				case PAGE_SEARCH:
					$location = '搜索';
					$location_url = 'search.php';
					break;
				case PAGE_PROFILE:
					$location = '用户资料';
					$location_url = 'index.php';
					break;
				case PAGE_VIEWONLINE:
					$location = '在线状态';
					$location_url = 'viewonline.php';
					break;
				case PAGE_VIEWMEMBERS:
					$location = '用户列表';
					$location_url = 'memberlist.php';
					break;
				case PAGE_PRIVMSGS:
					$location = '信息';
					$location_url = 'privmsg.php';
					break;
				case PAGE_DOWNLOAD:
					$location = '下载附件';
					$location_url = 'index.php';
					break;
				case PAGE_MODS:
					$location = '应用中心';
					$location_url = 'mods.php';
					break;
				case PAGE_ARTICLE:
					$location = '文章首页';
					$location_url = 'article.php';					
					break;
				case PAGE_ALBUM:
					$location = '相册';
					$location_url = 'album.php';	
					break;
				case PAGE_GROUPCP:
					$location = '小组';
					$location_url = 'groupcp.php';
					break;
				default:
					$location = '一个无人知晓的地方漫游着';
					$location_url = 'index.php';
			}
		}
		else
		{
			$location_url 	= append_sid('viewforum.php?' . POST_FORUM_URL . '=' . $row['session_page']);
			$location 		= $forum_data[$row['session_page']];
		}
		
		$row_class = ( !($which_counter % 2) ) ? 'row1' : 'row2';

		$avatar_img = ''; 
		if ( $row['user_avatar_type'] && $row['user_allowavatar'] ) 
		{ 
			switch( $row['user_avatar_type'] ) 
			{ 
				case USER_AVATAR_UPLOAD: 
					$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" class="avatar" title="' . $row['username'] . '" alt="." width="48" hight="48" />' : make_style_image('topic_avatar', $row['username'], '.', 'class="avatar"'); 
				break; 
				case USER_AVATAR_REMOTE: 
					$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" class="avatar" alt="." title="' . $row['username'] . '" width="48" hight="48" />' : make_style_image('topic_avatar', $row['username'], '.', 'class="avatar"'); 
				break; 
				default:
					$avatar_img = make_style_image('topic_avatar', $row['username'], '.', 'class="avatar"');
			} 
		}
		else
		{
			$avatar_img = make_style_image('topic_avatar', $row['username'], '.', 'class="avatar"');
		}		

		if ((time() - $row['session_time']) < 60)
		{
			$llll = (time() - $row['session_time'] + 1) . '秒';
		}
		else
		{
			$llll = floor((time() - $row['session_time']) / 60) . '分钟';
		}

		$template->assign_block_vars('online_row', array(
			'ROW_CLASS'			=> $row_class,
			'USERNAME' 			=> $username,
			'USER_AVATER'		=> $avatar_img,
			'LASTUPDATE' 		=> $llll,
			'FORUM_LOCATION' 	=> $location,
			'PREV_IP'			=> decode_ip($prev_ip),
			'U_USER_PROFILE' 	=> append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $user_id),
			'U_FORUM_LOCATION' 	=> append_sid($location_url))
		);
		
		$which_counter++;
	}
}

// 没有用户在线
if ( $which_counter == 0 )
{
	$template->assign_block_vars('not_online_user', array());
}

// 计算出查询的记录总数
$sql = 'SELECT found_rows() AS rowcount'; 

if ( !$result = $db->sql_query($sql) )
{
	trigger_error('无法统计在线用户！', E_USER_WARNING);
}

$total_online 		= $db->sql_fetchrow($result);
$total_all_online 	= $total_online['rowcount'];

$template->assign_vars(array(
	'TOTAL_ONLINE_USER'				=> $total_all_online, 
	'PAGINATION'					=> generate_pagination('viewonline.php?', $total_all_online, $per, $start))
);

$template->pparse('body');

page_footer();

?>