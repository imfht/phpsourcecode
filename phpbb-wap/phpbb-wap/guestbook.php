<?php
/**
* @package phpBB-WAP
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

//session
$userdata = $session->start($user_ip, PAGE_INDEX);
init_userprefs($userdata);

$mode = get_var('m', '');

if ($mode == 'new')
{
	$gb_title = get_var('title', '');
	$gb_password = get_var('password', '');
	$gb_text = get_var('message', '');
	$gb_username = get_var('username', '');
	$gb_code = get_var('code', '');
	$error = false;
	$error_message = '';

	if ($gb_code !== $board_config['server_name'])
	{
		trigger_error('请输入正确的留言问题');
	}

	if (empty($gb_title) || strlen($gb_username) > 255)
	{
		$error = true;
		$error_message .= '<p>留言标题不合法</p>';
	}

	if (empty($gb_text))
	{
		$error = true;
		$error_message .= '<p>留言内容不能留空</p>';
	}

	if (empty($gb_username) || strlen($gb_username) > 12)
	{
		$error = true;
		$error_message .= '<p>姓名不合法</p>';
	}
	
	if (!$error)
	{
		$gb_username 	= magic_quotes($gb_username);
		$gb_password 	= ($gb_password == '') ? '' : md5($gb_password);
		$gb_title 		= magic_quotes($gb_title);
		$gb_text		= magic_quotes($gb_text);

		$sql = 'INSERT INTO ' . GUESTBOOK_TABLE . " (gb_time, gb_ip, gb_username, gb_password, gb_title, gb_text, gb_reply)
			VALUES (" . time() .", '" . $user_ip . "', '$gb_username', '$gb_password', '$gb_title', '$gb_text', '')";

		if (!$db->sql_query($sql))
		{
			trigger_error('无法插入新留言', E_USER_WARNING);
		}

		trigger_error('留言成功' . back_link(append_sid('guestbook.php')), E_USER_ERROR);
	}

	error_box('ERROR_BOX', $error_message);
}
else if ($mode == 'view')
{
	$gb_id = get_var('i', '');
	
	if (empty($gb_id))
	{
		trigger_error('您没有指定留言' . back_link(append_sid('guestbook.php')), E_USER_ERROR);
	}

	$sql = 'SELECT gb_time, gb_ip, gb_username, gb_password, gb_title, gb_text, gb_reply
		FROM ' . GUESTBOOK_TABLE . '
		WHERE gb_id = ' . (int) $gb_id;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询留言信息', E_USER_WARNING);
	}

	if (!$row = $db->sql_fetchrow($result))
	{
		trigger_error('您指定的留言不存在' . back_link(append_sid('guestbook.php')), E_USER_ERROR);
	}

	if ($row['gb_password'] !== '')
	{
		if ($userdata['user_level'] != ADMIN)
		{
			if (isset($_POST['password']))
			{
				if (md5($_POST['password']) !== $row['gb_password'])
				{
					trigger_error('您输入的密码错误' . back_link(append_sid('guestbook.php?m=view&i=' . $gb_id)), E_USER_ERROR);
				}
			}
			else
			{
				page_header('请输入查看密码');

				$template->set_filenames(array('enter_password' => 'guestbook_enter_password.tpl'));

				$template->assign_vars(array(
					'S_ACTION' 	=> append_sid('guestbook.php?m=view&i=' . $gb_id),
					'U_BACK'	=> append_sid('guestbook.php'))
				);

				$template->pparse('enter_password');

				page_footer();
			}
		}
	}

	if ($userdata['user_level'] == ADMIN && isset($_POST['reply']))
	{
		$gb_reply = get_var('reply', '');

		$gb_reply = magic_quotes($gb_reply);

		$sql = 'UPDATE ' . GUESTBOOK_TABLE . " 
			SET gb_reply = '$gb_reply'
			WHERE gb_id = " . (int)$gb_id;

		if (!$db->sql_query($sql))
		{
			trigger_error('无法回复留言', E_USER_WARNING);
		}

		trigger_error('留言成功' . back_link(append_sid('guestbook.php?m=view&i=' . $gb_id)), E_USER_ERROR);
	}

	page_header($row['gb_title']);

	if ($userdata['user_level'] == ADMIN)
	{
		$template->assign_block_vars('delete', array());
		if ($row['gb_reply'] == '')
		{
			$template->assign_block_vars('reply', array());
		}	
	}

	$template->set_filenames(array('body' => 'guestbook_view.tpl'));

	$template->assign_vars(array(
		'GB_TITLE' 		=> decode_char($row['gb_title']),
		'GB_TEXT'		=> decode_char($row['gb_text']),
		'GB_TIME'		=> create_date('Y年m月d日 H:i', $row['gb_time'], $board_config['board_timezone']),
		'GB_IP'			=> decode_ip($row['gb_ip']),
		'GB_USERNAME'	=> decode_char($row['gb_username']),
		'GB_REPLY'		=> (decode_char($row['gb_reply']) == '') ? '管理员没有回复' : decode_char($row['gb_reply']),
		'U_BACK'		=> append_sid('guestbook.php'),
		'U_GB_DELETE'	=> append_sid('guestbook.php?m=delete&i=' . $gb_id))
	);

	$template->pparse('body');

	page_footer();
}
elseif ($mode == 'delete')
{
	$gb_id = get_var('i', '');
	if ($gb_id == '')
	{
		trigger_error('请指定要删除的留言' . back_link(append_sid('guestbook.php')), E_USER_ERROR);
	}

	$sql = 'DELETE FROM ' . GUESTBOOK_TABLE . '
		WHERE gb_id = ' . (int)$gb_id;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法删除留言', E_USER_WARNING);
	}

	trigger_error('留言删除成功' . back_link(append_sid('guestbook.php')), E_USER_ERROR);
}

page_header('留言板');

$per = 15;
$start = get_pagination_start($per);

$sql = 'SELECT gb_id, gb_time, gb_title, gb_reply
	FROM ' . GUESTBOOK_TABLE . "
	ORDER BY gb_time DESC
	LIMIT $start , $per";

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询留言板信息', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$gb_reply = ($row['gb_reply'] == '') ? '未回复' : '<font color="red">已回复</font>'; 
	$template->assign_block_vars('guestbook_row', array(
		'ROW_CLASS'		=> $row_class,
		'GB_NUMBER'		=> $start + $i + 1,
		'GB_REPLY'		=> $gb_reply,
		'GB_TIME'		=> create_date('m月d日 H:i', $row['gb_time'], $board_config['board_timezone']),
		'GB_TITLE' 		=> decode_char($row['gb_title']),
		'U_GB'			=> append_sid(ROOT_PATH . 'guestbook.php?m=view&i=' . $row['gb_id']))
	);
	$i++;
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_guestbook', array());
}
else
{
	$sql = 'SELECT COUNT(gb_id) AS total_gb
		FROM ' . GUESTBOOK_TABLE;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法统计留言板记录', E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);
}

$pagination = generate_pagination('guestbook.php?', $row['total_gb'], $per, $start);

$template->set_filenames(array(
	'body' => 'guestbook_body.tpl')
);

$template->assign_vars(array(
	'L_SERVER_NAME'	=> $board_config['server_name'],
	'PAGINATION' 	=> $pagination,
	'S_ACTION'		=> append_sid('guestbook.php?m=new'))
);

$template->pparse('body');

page_footer();

?>