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
	
if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('您选择的是游客或用户不存在', E_USER_ERROR);
}

if (!$profiledata = get_userdata($_GET[POST_USERS_URL]))
{
	trigger_error('无法取得用户数据！', E_USER_ERROR);
}

function main_header($page_title, $page_header)
{
	global $template;

	ob_start();

	$template->assign_vars(array(
		'MAIN_TITLE' => $page_title,
		'MAIN_HEADER' => $page_header)
	);

	header('Content-type: text/html; charset=UTF-8');
	header('Cache-Control: private, no-cache="set-cookie"');
	header('Expires: 0');
	header('Pragma: no-cache');

	$template->set_filenames(array(
		'main_header' => 'ucp/main_header.tpl') 
	);

	$template->pparse('main_header');

}

$sql = 'SELECT um_name, um_header, um_body
	FROM ' . UCP_MAIN_TABLE . '
	WHERE um_user = ' . $profiledata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获取您的空间模块数据', E_USER_WARNING);
}

if ($row = $db->sql_fetchrow($result))
{
	$ucp_page_title = decode_char($row['um_name']);
	$ucp_page_head = decode_char($row['um_header']);
	$ucp_page_body = decode_char($row['um_body']);

	if (isset($_GET['admin']))
	{
		if ($profiledata['user_id'] == $userdata['user_id'] || $userdata['user_level'] == ADMIN)
		{

			$submit = (isset($_POST['submit'])) ? true : false;

			if ($submit)
			{

				$name = isset($_POST['name']) ? $_POST['name'] : '';
				$head = isset($_POST['head']) ? $_POST['head'] : '';
				$body = isset($_POST['body']) ? $_POST['body'] : '';

				$up_main_name = magic_quotes($name);
				$up_main_head = magic_quotes($head);
				$up_main_body = magic_quotes($body);

				$sql = "UPDATE " . UCP_MAIN_TABLE . " 
					SET um_name = '" . $db->sql_escape($up_main_name) . "', um_header = '" . $db->sql_escape($up_main_head) . "', um_body = '" . $db->sql_escape($up_main_body) . "'
					WHERE um_user = " . $profiledata['user_id'];

				if (!$db->sql_query($sql))
				{
					trigger_error('无法更新您的空间主页数据', E_USER_WARNING);
				}

				trigger_error('修改成功！<br />点击 <a href="' . append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']) . '">这里</a> 查看效果<br />点击 <a href="' . append_sid('ucp.php?admin&mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']) . '">这里</a> 继续修改');
			}

			page_header('空间主页管理');

			$template->set_filenames(array(
				'body' => 'ucp/main_admin.tpl')
			);

			$template->assign_vars(array(
				'U_BACK' => append_sid('ucp.php?mode=manage&' . POST_USERS_URL . '=' . $profiledata['user_id']),
				'S_MAIN_TITLE' => $ucp_page_title,
				'S_MAIN_HEAD' => $ucp_page_head,
				'S_MAIN_BODY' => $ucp_page_body,
				'S_ACTION' => append_sid('ucp.php?admin&mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']))
			);
			
			$template->pparse('body');

			page_footer();
		}
	}
}
else
{

	if ($profiledata['user_id'] == $userdata['user_id'])
	{

		if ( isset($_POST['cancel']) )
		{
			redirect(append_sid('ucp.php?mode=viewprofile&u=' . $profiledata['user_id'], true));
		}

		$confirm = ( isset($_POST['confirm']) ) ? ( ( $_POST['confirm'] ) ? true : false ) : false;

		if ($confirm)
		{
			// 新用户的空间名称
			$new_main_name = $profiledata['username'];

			// 新用户的空间页面头部
			$new_main_header = '<link href="./template/theme/style.css" rel="stylesheet" type="text/css" media="screen, projection"/> 
					<link rel="shortcut icon" href=" /favicon.ico" />
					<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0" />
					<meta name="apple-mobile-web-app-capable" content="yes" />
					<meta name="apple-mobile-web-app-status-bar-style" content="black" />';

			// 新用户的空间页面
			$new_main_body = '<div id="wrap"><header><a href="./"/><img src="./images/logo.png" alt="." title="Logo"/></a></header><div id="main"><div class="module box">欢迎使用用户空间主页功能，当看到这个页面时表示您已经开通了空间主页功能！</div></div><footer><div class="index_bottom">版权所有 (c) ' . $profiledata['username'] . '</div></footer></div>';

			$new_main_name = magic_quotes($new_main_name);

			$new_main_header = magic_quotes($new_main_header);

			$new_main_body = magic_quotes($new_main_body);


			$sql = 'INSERT INTO ' . UCP_MAIN_TABLE . " (um_user, um_name, um_header, um_body) 
				VALUES ({$profiledata['user_id']}, '" . $db->sql_escape($new_main_name) . "', '" . $db->sql_escape($new_main_header) .  "', '" . $db->sql_escape($new_main_body) .  "')";

			if (!$db->sql_query($sql))
			{
				trigger_error('无法为您开通空间主页功能', E_USER_WARNING);
			}

			trigger_error('您的空间主页功能已开通<br />点击 <a href="' . append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']) . '">这里</a> 可以查看你的空间主页');
		}

		confirm_box(
			'开通我的空间',
			'开通我的空间',
			'是否要开通空间主页功能，本功能完全免费！',
			append_sid('ucp.php?mode=main&' . POST_USERS_URL . '=' . $profiledata['user_id']),
			''
		);
	}
	else
	{
		trigger_error('该用户没有开通空间主页功能' . back_link(append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $profiledata['user_id'])));
	}
}

main_header($ucp_page_title, $ucp_page_head);

$template->set_filenames(array(
	'body' => 'ucp/ucp_main.tpl')
);

$template->assign_var('MAIN_BODY', $ucp_page_body);

$template->pparse('body');

if (!empty($db))
{
	$db->sql_close();
}

?>