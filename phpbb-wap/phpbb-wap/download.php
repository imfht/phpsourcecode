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

if (defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

define('IN_PHPBB', true);
define('ROOT_PATH', './');

require(ROOT_PATH . 'common.php');

$download_id	= get_var('id', 0);
$thumbnail 		= get_var('thumb', 0);

$userdata = $session->start($user_ip, PAGE_DOWNLOAD);
init_userprefs($userdata);

if (!$download_id)
{
	trigger_error('请指定您要下载的附件' . back_link(append_sid('index.php')), E_USER_ERROR);
}

if ($board_config['disable_mod'] && $userdata['user_level'] != ADMIN)
{
	trigger_error('本站没有开启下载功能，如有疑问请联系超级管理员！' . back_link(append_sid('index.php')), E_USER_ERROR);
}

$sql = 'SELECT attach_id, physical_filename, real_filename, download_count, filetime, extension, filesize, mimetype, comment
	FROM ' . ATTACHMENTS_DESC_TABLE . '
	WHERE attach_id = ' . (int) $download_id;

if (!($result = $db->sql_query($sql)))
{
	trigger_error('无法查询附件信息', E_USER_WARNING);
}

if (!($attachment = $db->sql_fetchrow($result)))
{
	trigger_error('您下载的附件已删除或不存在！' . back_link(append_sid('index.php')), E_USER_ERROR);
}

$attachment['physical_filename'] = basename($attachment['physical_filename']);

$db->sql_freeresult($result);

$authorised 	= false;

$sql = 'SELECT post_id, user_id_1, user_id_2
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE attach_id = ' . (int) $attachment['attach_id'];

if (!($result = $db->sql_query($sql)))
{
	trigger_error('无法取得附件信息', E_USER_WARNING);
}

$auth_pages 	= $db->sql_fetchrowset($result);
$num_auth_pages = $db->sql_numrows($result);

for ($i = 0; $i < $num_auth_pages && $authorised == false; $i++)
{
	$auth_pages[$i]['post_id'] = intval($auth_pages[$i]['post_id']);

	if ($auth_pages[$i]['post_id'] != 0)
	{
		$sql = 'SELECT forum_id
			FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . (int) $auth_pages[$i]['post_id'];

		if ( !($result = $db->sql_query($sql)) )
		{
			trigger_error('无法取得附件信息', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		$forum_id = $row['forum_id'];

		$is_auth = array();
		$is_auth = auth(AUTH_ALL, $forum_id, $userdata); 

		if ($is_auth['auth_download'])
		{
			$authorised = TRUE;
		}
	}
	else
	{
		if (( ($userdata['user_id'] == $auth_pages[$i]['user_id_2']) || ($userdata['user_id'] == $auth_pages[$i]['user_id_1']) ) || ($userdata['user_level'] == ADMIN) )
		{
			$authorised = TRUE;
		}
	}
}


if (!$authorised)
{
	trigger_error('对不起，您没有权限下载这个附件' . back_link(append_sid('index.php')), E_USER_ERROR);
}

$sql = 'SELECT e.extension, g.download_mode
	FROM ' . EXTENSION_GROUPS_TABLE . ' g, ' . EXTENSIONS_TABLE . ' e
	WHERE (g.allow_group = 1) 
		AND (g.group_id = e.group_id)';

if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('取法查询扩展名组信息', E_USER_WARNING);
}

$rows 		= $db->sql_fetchrowset($result);
$num_rows 	= $db->sql_numrows($result);

for ($i = 0; $i < $num_rows; $i++)
{
	$extension = strtolower(trim($rows[$i]['extension']));
	$allowed_extensions[] = $extension;
}

if (!in_array($attachment['extension'], $allowed_extensions) && $userdata['user_level'] != ADMIN)
{
	trigger_error('文件扩展名“ ' . $attachment['extension'] . ' ”已被管理员禁用，因此这个附件是不被显示的' . back_link(append_sid('index.php')), E_USER_ERROR);
}

$sql = 'SELECT post_id, user_id_1
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE attach_id = ' . (int) $download_id;

if (!($result = $db->sql_query($sql)))
{
	trigger_error('无法查询附件信息', E_USER_WARNING);
}

$attachdata = $db->sql_fetchrow($result);

// 清除过期的下载记录
$sql = 'DELETE FROM ' . DOWNLOADS_TABLE . '
	WHERE download_time < ' . (time() - 3600);

if (!$db->sql_query($sql))
{
	trigger_error('无法删除过期的记录', E_USER_WARNING);
}

$error 			= false;
$error_message 	= '';

if(isset($_GET['true']))
{

	$sql = 'SELECT download_user, download_attach
		FROM ' . DOWNLOADS_TABLE . '
		WHERE download_user = ' . $userdata['user_id'] . '
			AND download_attach = ' . (int)$download_id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询用户的下载信息', E_USER_WARNING);
	}

	// 如果用户在一小时内没有下载过该附件
	if (!$db->sql_numrows($result))
	{
		// 感觉 true 比 false 更合适，这里是防止扣币没有成功，反而增加了上传者的金币
		// 如果不这样做容易被恶作剧
		$is_cut = true;

		// 如果开启下载扣币
		if (!empty($board_config['download_cut_points']))
		{
			// 先验证用户有没有足够的金币
			if ($userdata['user_points'] < $board_config['download_cut_points'])
			{
				$is_cut 		= false;
				$error 			= true;
				$error_message .= '<p>您没有足够的金币</p>'; 
			}
			// 有足够的金币，扣取用户的金币
			else
			{
				$is_cut = true;

				$download_cut_points = intval($userdata['user_points']) - intval($board_config['download_cut_points']);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_points = ' . $download_cut_points . ' 
					WHERE user_id = ' . $userdata['user_id'];

				if (!$db->sql_query($sql))
				{
					trigger_error('无法更新 ' . USERS_TABLE . ' 表', E_USER_WARNING);
				}
				
				$sql = 'INSERT INTO ' . DOWNLOADS_TABLE . ' (download_user, download_attach, download_time)
					VALUES (' . $userdata['user_id'] . ', ' . (int)$download_id . ', ' . time() . ')';

				if (!$db->sql_query($sql))
				{
					trigger_error('无法添加下载记录', E_USER_WARNING);
				}
			}
		}

		// 检查是否需要给附件作者增加金币
		if (!empty($board_config['download_add_points']) && $is_cut)
		{
			$sql = 'SELECT user_points
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . $attachdata['user_id_1'];

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('无法取得上传者的金币信息', E_USER_WARNING);
			}

			if ($db->sql_numrows($result))
			{
				$row = $db->sql_fetchrow($result);
				$download_add_points = intval($row['user_points']) + intval($board_config['download_add_points']);
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_points = ' . $download_add_points . '
					WHERE user_id = ' . $attachdata['user_id_1'];

				if (!$db->sql_query($sql))
				{
					trigger_error('无法更新 ' . USERS_TABLE . ' 表', E_USER_WARNING);
				}
			}
		}	
	}

	if (!$error)
	{
		// 增加下载次数
		$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
			SET download_count = download_count + 1 
			WHERE attach_id = ' . (int) $download_id;

		if (!$db->sql_query($sql))
		{
			trigger_error('无法更新下载次数', E_USER_WARNING);
		}

		$db->sql_close();

		$url 			= $board_config['upload_dir'] . '/' . create_date('Ym', $attachment['filetime'], 0) . '/' .$attachment['physical_filename'];
		$real_filename 	= htmlspecialchars($attachment['real_filename']);
		$user_agent 	= $_SERVER["HTTP_USER_AGENT"];
		$filesize 		= intval ( sprintf ('%u', filesize ( $url ) ) );

		if ($board_config['download_mode'])
		{
			header('Content-Type: application/octet-stream');
			//header('Content-Length: ' . $filesize);
			header('Content-Disposition: attachment; filename="' . $real_filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
	 	header('Expires: 0');

			readfile($url);
		}
		else
		{
			$server_protocol 	= ($board_config['cookie_secure']) ? 'https://' : 'http://';
			$server_name 		= preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['server_name']));
			$server_port 		= ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
			$script_name 		= preg_replace('/^\/?(.*?)\/?$/', '/\1', trim($board_config['script_path']));
			$script_name_array 	= str_split($script_name, 1);

			if ($script_name_array[strlen($script_name) - 1] != '/')
			{
				$script_name .= '/';
			}

			header('Location: ' . $server_protocol . $server_name . $server_port . $script_name . $url);
		}

		exit;
	}
}

$page_title = $attachment['real_filename'] . '_正在下载';

page_header($page_title);

$template->set_filenames(array(
	'download' => 'download_body.tpl')
);

if ( $error )
{
	error_box('ERROR_BOX', $error_message);
}

$filesize = $attachment['filesize'];

if ($filesize >= 1048576)
{
	$filesize = (round((round($filesize / 1048576 * 100) / 100), 2));
}
else if ($filesize >= 1024)
{
	$filesize = (round((round($filesize / 1024 * 100) / 100), 2));
}

$sizelang = ($attachment['filesize'] >= 1048576) ? 'MB' : ( ($attachment['filesize'] >= 1024) ? 'KB' : 'Bytes');

if (!empty($board_config['download_cut_points']))
{
	$template->assign_block_vars('information', array(
		'USER_POINTS'			=> $userdata['user_points'],
		'POINT_NAME' 			=> $board_config['points_name'],
		'DOWNLOAD_CUT_POINTS' 	=> $board_config['download_cut_points'])
	);
}

$template->assign_vars(array(
	'FILENAME'				=> $attachment['real_filename'],
	'FILESIZE'				=> $filesize,
	'COUNT'					=> $attachment['download_count'],
	'SIZELANG'				=> $sizelang,
	'COMMENT'				=> (empty($attachment['comment'])) ? '附件的作者没有对这个文件进行描述' : $attachment['comment'],
	'U_DOWNLOAD'			=> append_sid('download.php?id=' . $download_id . '&true'),
	'U_BACKPOST'			=> append_sid('viewtopic.php?' . POST_POST_URL . '=' . $attachdata['post_id'] . '#' . $attachdata['post_id']),
	'IMG_BACKPOST'			=> make_style_image('back'),
	'IMG_DOWNLOAD'			=> make_style_image('download'))
);

$template->pparse('download');

page_footer();

?>