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

if (!defined('IN_PHPBB')) exit;

if (!$userdata['session_logged_in'])
{
	trigger_error('添加网站必须登录，请不要嫌麻烦，方便日后管理您的网站<br />点击 <a href="' . login_back('link.php?mode=join', true) . '">这里</a> 登录' . back_link(append_sid('links.php')), E_USER_ERROR);
}

$submit = isset($_POST['submit']) ? true : false;

$error = false;
$error_message = '';

if ($submit)
{
	$link_title = get_var('title', '');
	$link_name = get_var('name', '');
	$link_url = get_var('url', '');
	$link_cat = get_var('cat', 0);
	$link_desc = get_var('desc', '');

	if ($link_title == '' || mb_strlen($link_title, 'UTF-8') > 8)
	{
		$error = true;
		$error_message .= '<p>网站的名称必须在1~8个字符以内</p>';
	}

	if ($link_title == '' || mb_strlen($link_name, 'UTF-8') > 2)
	{
		$error = true;
		$error_message .= '<p>网站的简称不能大于两个字符</p>';
	}

	if (!filter_var($link_url, FILTER_VALIDATE_URL))
	{
		$error = true;
		$error_message .= '<p>网站地址不合法</p>';
	}

	$sql = 'SELECT linkclass_id
		FROM ' . LINKCLASS_TABLE . '
		WHERE linkclass_id = ' . $link_cat;
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询友链分类信息', E_USER_WARNING);
	}

	if (!$row = $db->sql_fetchrow($result))
	{
		$error = true;
		$error_message = '<p>网站类型指定不正确/p>';				
	}

	$link_url = str_replace("'", '', $link_url);

	$sql = 'SELECT link_url
		FROM ' . LINKS_TABLE . "
		WHERE link_url LIKE '$link_url'";

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询URL', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		$message = '网站已经存在，您可以使用您申请该友链的网站帐号登录本站来修改友链信息';
		$message .= '<br />点击 <a href="' . append_sid('links.php?mode=manage') . '">这里</a> 修改友链信息';
		trigger_error($message . back_link(append_sid('links.php')), E_USER_ERROR);
	}

	if ($error)
	{
		error_box('ERROR_BOX', $error_message);
	}
	else
	{
		$sql = 'SELECT MAX(link_id) as link_id 
			FROM ' . LINKS_TABLE;

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法取得友链的最大ID', E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);

		$new_link_id = $row['link_id'] + 1;

		$sql_arr = array(
			'link_id'			=> $new_link_id,
			'link_class_id' 	=> $link_cat,
			'link_title'		=> $link_title,
			'link_name'			=> $link_name,
			'link_url'			=> $link_url,
			'link_desc'			=> $link_desc,
			'link_join_time'	=> time(),
			'link_last_visit'	=> time(),
			'link_show'			=> 0,
			'link_admin_user'	=> $userdata['user_id']
		);

		$sql = 'INSERT INTO ' . LINKS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_arr);

		if (!$db->sql_query($sql))
		{
			trigger_error('无法添加友链', E_USER_WARNING);
		}

		page_header('申请成功');

		$server_protocol 	= ($board_config['cookie_secure']) ? 'https://' : 'http://';
		$server_name 		= preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['server_name']));
		$server_port 		= ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
		$script_name 		= preg_replace('/^\/?(.*?)\/?$/', '/\1', trim($board_config['script_path']));

		$script_name_array = str_split($script_name, 1);

		if ($script_name_array[strlen($script_name) - 1] != '/')
		{
			$script_name .= '/';
		}

		$siteurl = $server_protocol . $server_name . $server_port . $script_name . 'links.php?mode=in&id=' . $new_link_id;
		$manage_url = $server_protocol . $server_name . $server_port . $script_name . 'links.php?mode=manage';

		$template->assign_vars(array(
			'LINK_SITENAME' 	=> $board_config['sitename'],
			'LINK_SITEURL'		=> $siteurl,
			'LINK_MANAGE'		=> $manage_url,
			'U_BACK'			=> append_sid('links.php'))
		);

		$template->set_filenames(array('body' => 'links/link_prompt.tpl'));
		$template->pparse('body');
		page_footer();
	}
}

page_header('申请加入');

$sql = 'SELECT linkclass_id, linkclass_name
	FROM ' . LINKCLASS_TABLE;
if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获得友链分类信息', E_USER_WARNING);
}

$select_cat = '<select name="cat">';
while ($row = $db->sql_fetchrow($result))
{
	$select_cat .= '<option value="' . $row['linkclass_id'] . '">' . $row['linkclass_name'] .'</option>';
}
$select_cat .= '</select>';

$template->assign_vars(array(
	'SELECT_CAT'=> $select_cat,
	'S_ACTION' 	=> append_sid('links.php?mode=join'),
	'U_BACK'	=> append_sid('links.php'))
);

$template->set_filenames(array(
	'join' => 'links/link_join.tpl')
);

$template->pparse('join');

page_footer();
?>