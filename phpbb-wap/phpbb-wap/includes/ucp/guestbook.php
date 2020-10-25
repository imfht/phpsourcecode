<?php

if ( !defined('IN_PHPBB') )
{
	exit;
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('您选择的是游客或用户不存在', E_USER_ERROR);
}

if (!$profiledata = get_userdata($_GET[POST_USERS_URL]))
{
	trigger_error('无法取得用户数据！', E_USER_ERROR);
}

if (isset($_POST['message']))
{

	if ($userdata['user_id'] == $profiledata['user_id'])
	{
		if (!$board_config['gb_quick'])
		{
			trigger_error('您不可以给自己留言哦');
		}	
	}

	if (!$board_config['allow_guests_gb'])
	{
		if (!$userdata['session_login_in'])
		{
			trigger_error('网站已禁止匿名用户留言功能！' . back_link(append_sid('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id'])));
		}
	}

	if ($userdata['user_id'] != $profiledata['user_id'])
	{
		if ($userdata['user_level'] != ADMIN)
		{
			if (!$profiledata['user_can_gb'])
			{
				trigger_error('该用户不允许其他用户进行留言' . back_link(append_sid('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id'])));
			}
		}	
	}

	$master_look = isset($_POST['look']) ? 1 : 0;

	$message = $_POST['message'];

	if (empty($message))
	{
		trigger_error('留言内容不能为空' . back_link(append_sid('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id'])));
	}

	$sql = 'INSERT INTO ' . PROFILE_GUESTBOOK_TABLE . " (user_id, poster_id, gb_time, master_look, message) 
		VALUES ({$profiledata['user_id']}, {$userdata['user_id']}, " . time() . ", $master_look,'" . $db->sql_escape($message) . "')";

	if (!$db->sql_query($sql))
	{
		trigger_error('无法插入留言数据', E_USER_WARNING);
	}

	trigger_error('留言成功！' . back_link(append_sid('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id'])));
}

page_header('留言板');

$per = $board_config['gb_posts'];
$start = get_pagination_start($per);

$sql = 'SELECT pg.poster_id, pg.gb_time, pg.message, pg.master_look, u.username
	FROM ' . PROFILE_GUESTBOOK_TABLE . ' pg, ' . USERS_TABLE . ' u
	WHERE pg.user_id = ' . $profiledata['user_id'] . '
		AND pg.poster_id = u.user_id
	ORDER BY gb_time DESC
	LIMIT ' . $start . ', ' . $per;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法读取用户留言', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

	if ($row['master_look'])
	{
		if ($userdata['user_level'] == ADMIN || $userdata['user_id'] == $profiledata['user_id'])
		{
			$message = $row['message'];
		}
		else
		{
			$message = '这条留言仅主人可见';
		}
	}
	else
	{
		$message = $row['message'];
	}

	$template->assign_block_vars('guestbook',array(
		'ROW_CLASS' => $row_class,
		'MESSAGE' => $message,
		'TIME' => create_date($userdata['user_dateformat'], $row['gb_time'], $userdata['user_timezone']),
		'USERNAME' => $row['username'],
		'U_UCP' => append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $row['poster_id']))
	);
	$i++;
}

$sql = 'SELECT COUNT(gb_id) AS total
	FROM ' . PROFILE_GUESTBOOK_TABLE . '
	WHERE user_id = ' . $profiledata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法读取用户留言', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

if (!$row['total'])
{
	$template->assign_block_vars('empty_guestbook', array());
}

$template->set_filenames(array(
	'body' => 'ucp/ucp_guestbook.tpl')
);

$pagination = generate_pagination('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id'], $row['total'], $per, $start);

$template->assign_vars(array(
	'PAGINATION' 			=> $pagination,
	'GUESTBOOK_TOTAL'		=> $row['total'],
	'U_UCP_MAIN'			=> append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_VIEWPROFILE'			=> append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_GUESTBOOK'			=> append_sid('ucp.php?mode=guestbook&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'U_ALBUM'				=> append_sid('album.php'),
	'U_UCP_MANAGE'			=> append_sid('ucp.php?mode=manage&' . POST_USERS_URL . '=' . $profiledata['user_id']))
);

$template->pparse('body');

page_footer();


?>