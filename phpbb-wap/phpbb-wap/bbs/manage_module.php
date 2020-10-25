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
define('ROOT_PATH', './../');
include(ROOT_PATH . 'common.php');

if ( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
	$forum_id = ( isset($_GET[POST_FORUM_URL]) ) ? intval($_GET[POST_FORUM_URL]) : intval($_POST[POST_FORUM_URL]);
}
else if ( isset($_GET['forum']))
{
	$forum_id = intval($_GET['forum']);
}
else
{
	$forum_id = '';
}

if ( !empty($forum_id) )
{
	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		WHERE forum_id = $forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		trigger_error('Could not obtain forums information', E_USER_WARNING);
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

$userdata = $session->start($user_ip, $forum_id);
init_userprefs($userdata);

if (!$userdata['session_logged_in'])
{
	redirect(append_sid(ROOT_PATH . 'login.php', true));
}

$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_row);

if ( !$is_auth['auth_mod'] )
{
	trigger_error('您不是该论坛的版主' . back_link(append_sid(ROOT_PATH . 'forumcp.php?' . POST_FORUM_URL . '=' . $forum_id)), E_USER_ERROR);
}

require_once ROOT_PATH . 'includes/class/forum_module.php';

$forum_module = new Forum_module($forum_id);

$sql = 'SELECT module_id, module_top, module_bottom
	FROM ' . FORUM_MODULE_TABLE . '
	WHERE module_forum = ' . $forum_id;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法获取论坛模块的数据', E_USER_WARNING);
}

if (!$db->sql_numrows($result))
{
	$forum_module->create_module();
}

$forum_module->obtain_module();

$submit = (isset($_POST['submit'])) ? true : false;

$module_top = get_var('top', '');
$module_bottom = get_var('bottom', '');

$module_top = htmlspecialchars($module_top, ENT_QUOTES);
$module_bottom = htmlspecialchars($module_bottom, ENT_QUOTES);

if ($submit)
{
	$forum_module->update_module($module_top, $module_bottom);

	trigger_error('保存成功' . back_link(append_sid(ROOT_PATH . 'bbs/manage_module.php?' . POST_FORUM_URL . '=' . $forum_id)), E_USER_ERROR);
}

page_header('修改论坛的顶部和底部');

$template->assign_vars(array(
	'MODULE_TOP' 	=> $forum_module->module_top,
	'MODULE_BOTTOM'	=> $forum_module->module_bottom,
	'U_BACK'		=> append_sid(ROOT_PATH . 'forumcp.php?' . POST_FORUM_URL . '=' . $forum_id),
	'S_ACTION'		=> append_sid(ROOT_PATH . 'bbs/manage_module.php?' . POST_FORUM_URL . '=' . $forum_id))
);

$template->set_filenames(array(
	'body' => 'bbs/manage_module.tpl')
);

$template->pparse('body');

page_footer();
?>