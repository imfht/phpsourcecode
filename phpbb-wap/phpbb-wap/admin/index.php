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

$no_page_header = true;
define('ROOT_PATH', './../');
require('./pagestart.php');

function inarray($needle, $haystack)
{ 
	for($i = 0; $i < count($haystack); $i++ )
	{ 
		if( $haystack[$i] == $needle )
		{ 
			return true; 
		} 
	} 
	return false; 
}

if( isset($_GET['pane']) && $_GET['pane'] == 'left' || isset($_GET['pane']) && $_GET['pane'] == 'right' )
{
	$dir = @opendir('.');

	$setmodules = 1;
	while( $file = @readdir($dir) )
	{
		if( preg_match("/^admin_.*?\.php$/", $file) )
		{
			include('./' . $file);
		}
	}

	@closedir($dir);

	unset($setmodules);

	page_header();

	$template->set_filenames(array(
		'body' => 'admin/index_navigate.tpl')
	);

	$template->assign_vars(array(
		'U_FORUM_INDEX' => append_sid(ROOT_PATH . 'index.php'),
		'U_ADMIN_INDEX' => append_sid('index.php?pane=left'))
	);

	ksort($module);
	
	foreach($module as $cat => $action_array)
	{
		$cat = preg_replace('/_/', ' ', $cat);
		
		$template->assign_block_vars('catrow', array(
			'ADMIN_CATEGORY' 	=> $cat,
			'U_ADMIN_CATEGORY'	=> append_sid('index.php?pane=left&amp;c=' . $cat))
		);		

		$is_cat = ( isset($_GET[POST_CAT_URL]) && !empty($_GET[POST_CAT_URL]) ) ? true : false;
		if ( $is_cat )
		{
			if( $cat == $_GET[POST_CAT_URL] )
			{
				ksort($action_array);
				foreach($action_array as $action => $file)
				{
					$action = preg_replace('/_/', ' ', $action);

					$template->assign_block_vars('catrow.modulerow', array(
						'ADMIN_MODULE' 		=> $action,
						'U_ADMIN_MODULE' 	=> append_sid($file))
					);
				}
			}
		}
	}

	$template->pparse('body');

	page_footer();
}
elseif( isset($_GET['pane']) && $_GET['pane'] == 'statistic' )
{

	page_header();

	$template->set_filenames(array(
		'body' => 'admin/index_body.tpl')
	);

	$total_posts 	= get_db_stat('postcount');
	$total_users 	= get_db_stat('usercount');
	$total_topics 	= get_db_stat('topiccount');
	$start_date 	= create_date($board_config['default_dateformat'], $board_config['board_startdate'], $board_config['board_timezone']);
	$boarddays 		= ( time() - $board_config['board_startdate'] ) / 86400;
	$posts_per_day 	= sprintf("%.2f", $total_posts / $boarddays);
	$topics_per_day = sprintf("%.2f", $total_topics / $boarddays);
	$users_per_day 	= sprintf("%.2f", $total_users / $boarddays);

	$avatar_dir_size = 0;

	if ($avatar_dir = @opendir(ROOT_PATH . $board_config['avatar_path']))
	{
		while( $file = @readdir($avatar_dir) )
		{
			if( $file != '.' && $file != '..' )
			{
				$avatar_dir_size += @filesize(ROOT_PATH . $board_config['avatar_path'] . '/' . $file);
			}
		}
		@closedir($avatar_dir);

		if($avatar_dir_size >= 1048576)
		{
			$avatar_dir_size = round($avatar_dir_size / 1048576 * 100) / 100 . ' MB';
		}
		else if($avatar_dir_size >= 1024)
		{
			$avatar_dir_size = round($avatar_dir_size / 1024 * 100) / 100 . ' KB';
		}
		else
		{
			$avatar_dir_size = $avatar_dir_size . ' Bytes';
		}

	}
	else
	{
		$avatar_dir_size = '不可用';
	}

	if($posts_per_day > $total_posts)
	{
		$posts_per_day = $total_posts;
	}

	if($topics_per_day > $total_topics)
	{
		$topics_per_day = $total_topics;
	}

	if($users_per_day > $total_users)
	{
		$users_per_day = $total_users;
	}

	//检测数据库版本
	$sql = 'SELECT VERSION() AS mysql_version';
	
	if($result = $db->sql_query($sql))
	{
		$row = $db->sql_fetchrow($result);
		$version = $row['mysql_version'];

		if( preg_match("/^(3\.23|4\.|5\.)/", $version) )
		{
			$db_name = ( preg_match("/^(3\.23\.[6-9])|(3\.23\.[1-9][1-9])|(4\.)|(5\.)/", $version) ) ? "`$dbname`" : $dbname;

			$sql = 'SHOW TABLE STATUS 
				FROM ' . $db_name;
			if($result = $db->sql_query($sql))
			{
				$tabledata_ary = $db->sql_fetchrowset($result);

				$dbsize = 0;
				for($i = 0; $i < count($tabledata_ary); $i++)
				{
					if( $table_prefix != '' )
					{
						if( strstr($tabledata_ary[$i]['Name'], $table_prefix) )
						{
							$dbsize += $tabledata_ary[$i]['Data_length'] + $tabledata_ary[$i]['Index_length'];
						}
					}
					else
					{
						$dbsize += $tabledata_ary[$i]['Data_length'] + $tabledata_ary[$i]['Index_length'];
					}
				}
			} 
		}
		else
		{
			$dbsize = '不可用';
		}
	}
	else
	{
		$dbsize = '不可用';
	}

	if ( is_integer($dbsize) )
	{
		if( $dbsize >= 1048576 )
		{
			$dbsize = sprintf("%.2f MB", ( $dbsize / 1048576 ));
		}
		else if( $dbsize >= 1024 )
		{
			$dbsize = sprintf("%.2f KB", ( $dbsize / 1024 ));
		}
		else
		{
			$dbsize = sprintf("%.2f Bytes", $dbsize);
		}
	}

	$template->assign_vars(array(
		'NUMBER_OF_POSTS' 	=> $total_posts,
		'NUMBER_OF_TOPICS' 	=> $total_topics,
		'NUMBER_OF_USERS' 	=> $total_users,
		'START_DATE' 		=> $start_date,
		'POSTS_PER_DAY' 	=> $posts_per_day,
		'TOPICS_PER_DAY' 	=> $topics_per_day,
		'USERS_PER_DAY' 	=> $users_per_day,
		'AVATAR_DIR_SIZE' 	=> $avatar_dir_size,
		'DB_SIZE' 			=> $dbsize)
	);

	$sql = 'SELECT u.user_id, u.username, u.user_session_page, s.session_logged_in, s.session_ip, u.user_allow_viewonline  
		FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . ' s
		WHERE s.session_logged_in = ' . TRUE . ' 
			AND u.user_id = s.session_user_id 
			AND u.user_id <> ' . ANONYMOUS . ' 
			AND s.session_time >= ' . ( time() - $board_config['online_time'] ) . ' 
		ORDER BY u.user_session_time DESC';
	
	if(!$result = $db->sql_query($sql))
	{
		trigger_error('Couldn\'t obtain regd user/online information.', E_USER_WARNING);
	}
	
	$onlinerow_reg = $db->sql_fetchrowset($result);

	$sql = 'SELECT session_page, session_logged_in, session_ip   
		FROM ' . SESSIONS_TABLE . '
		WHERE session_logged_in = 0
			AND session_time >= ' . ( time() - $board_config['online_time'] ) . '
		ORDER BY session_time DESC';
		
	if(!$result = $db->sql_query($sql))
	{
		trigger_error('Couldn\'t obtain guest user/online information.', E_USER_WARNING);
	}
	
	$onlinerow_guest = $db->sql_fetchrowset($result);

	$sql = 'SELECT forum_name, forum_id
		FROM ' . FORUMS_TABLE;
		
	if($forums_result = $db->sql_query($sql))
	{
		while($forumsrow = $db->sql_fetchrow($forums_result))
		{
			$forum_data[$forumsrow['forum_id']] = $forumsrow['forum_name'];
		}
	}
	else
	{
		trigger_error('Couldn\'t obtain user/online forums information.', E_USER_WARNING);
	}

	$reg_userid_ary = array();

	if( count($onlinerow_reg) )
	{
		$registered_users = 0;

		for($i = 0; $i < count($onlinerow_reg); $i++)
		{
			if( !inarray($onlinerow_reg[$i]['user_id'], $reg_userid_ary) )
			{
				$reg_userid_ary[] = $onlinerow_reg[$i]['user_id'];

				$username = $onlinerow_reg[$i]['username'];

				if( $onlinerow_reg[$i]['user_allow_viewonline'] || $userdata['user_level'] == ADMIN )
				{
					$registered_users++;
					$hidden = FALSE;
				}
				else
				{
					$hidden_users++;
					$hidden = TRUE;
				}

				if( $onlinerow_reg[$i]['user_session_page'] < 1 )
				{
					switch($onlinerow_reg[$i]['user_session_page'])
					{
						case PAGE_INDEX:
							$location = '浏览网站首页';
							break;
						case PAGE_POSTING:
							$location = '发表帖子';
							break;
						case PAGE_LOGIN:
							$location = '登录网站';
							break;
						case PAGE_SEARCH:
							$location = '搜索';
							break;
						case PAGE_PROFILE:
							$location = '浏览个人中心';
							break;
						case PAGE_VIEWONLINE:
							$location = '浏览在线状态';
							break;
						case PAGE_VIEWMEMBERS:
							$location = '浏览网站注册会员信息';
							break;
						case PAGE_PRIVMSGS:
							$location = '浏览信箱';
							break;
						default:
							$location = '正在一个无人知晓的地方漫游着';
					}
				}
				else
				{
					$location_url = append_sid('admin_forums.php?mode=editforum&amp;' . POST_FORUM_URL . '=' . $onlinerow_reg[$i]['user_session_page']);
					$location = $forum_data[$onlinerow_reg[$i]['user_session_page']];
				}

				$row_class 	= ( $registered_users % 2 ) ? 'row1' : 'row2';
				$reg_ip 	= decode_ip($onlinerow_reg[$i]['session_ip']);

				$template->assign_block_vars('reg_user_row', array(
					'ROW_CLASS' 		=> $row_class,
					'USERNAME' 			=> $username, 
					'FORUM_LOCATION' 	=> $location,
					'IP_ADDRESS' 		=> $reg_ip, 
					'U_WHOIS_IP' 		=> 'http://www.ip138.com/ips138.asp?ip=' . $reg_ip, 
					'U_USER_PROFILE' 	=> append_sid('admin_users.php?mode=edit&amp;' . POST_USERS_URL . '=' . $onlinerow_reg[$i]['user_id']))
				);
			}
		}

	}

	if( count($onlinerow_guest) )
	{
		$guest_users = 0;

		for($i = 0; $i < count($onlinerow_guest); $i++)
		{
			$guest_userip_ary[] = $onlinerow_guest[$i]['session_ip'];
			$guest_users++;

			if( $onlinerow_guest[$i]['session_page'] < 1 )
			{
				switch( $onlinerow_guest[$i]['session_page'] )
				{
					case PAGE_INDEX:
						$location = '浏览网站首页';
						break;
					case PAGE_LOGIN:
						$location = '登录网站';
						break;
					case PAGE_SEARCH:
						$location = '搜索';
						break;
					case PAGE_VIEWONLINE:
						$location = '浏览在线状态';
						break;
					case PAGE_VIEWMEMBERS:
						$location = '浏览网站注册会员信息';
						break;
					default:
						$location = '正在一个无人知晓的地方漫游着';
				}
			}
			else
			{
				$location_url = append_sid('admin_forums.php?mode=editforum&amp;' . POST_FORUM_URL . '=' . $onlinerow_guest[$i]['session_page']);
				$location = $forum_data[$onlinerow_guest[$i]['session_page']];
			}

			$row_color = '';
			$row_class = ( $guest_users % 2 ) ? 'row1' : 'row2';

			$guest_ip = decode_ip($onlinerow_guest[$i]['session_ip']);

			$template->assign_block_vars('guest_user_row', array(
				'ROW_CLASS' 		=> $row_class,
				'USERNAME' 			=> '匿名',
				'FORUM_LOCATION'	=> $location,
				'IP_ADDRESS' 		=> $guest_ip, 

				'U_WHOIS_IP' 		=> 'http://www.ip138.com/ips138.asp?ip=' . $guest_ip)
			);
		}

	}

	$template->pparse('body');

	page_footer();

}
else
{
	page_header();
	$template->set_filenames(array(
		'body' => 'admin/index_frameset.tpl')
	);

	if ( $board_config['guide_progress']  != -1 )
	{
		$template->assign_block_vars('guide_progress', array());
	}
	
	$template->assign_vars(array(
		'S_FRAME_NAV' 		=> append_sid('index.php?pane=left'),
		'S_FRAME_MAIN' 		=> append_sid('index.php?pane=statistic'),
		'S_FRAME_GUIDE'		=> append_sid('guide.php'),
		'S_FRAME_HELP'		=> append_sid('help.php'),
		
		'IMG_FRAME_NAV'		=> make_style_image('frame_setup'),
		'IMG_FRAME_MAIN'	=> make_style_image('frame_total'),
		'IMG_FRAME_GUIDE'	=> make_style_image('frame_guide'),
		'IMG_FRAME_HELP'	=> make_style_image('frame_help'))
	);

	$template->pparse('body');
	page_footer();
}

?>