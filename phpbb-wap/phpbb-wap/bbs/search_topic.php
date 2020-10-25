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

require(ROOT_PATH . 'common.php');

//session
$userdata = $session->start($user_ip, PAGE_INDEX);
init_userprefs($userdata);

// 为了减轻数据库的负担，建议开启登陆后搜索
//if (!$userdata['session_logged_in'])
//{
//	redirect(append_sid('login.php', true));
//}

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

$is_auth = array();
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_row);

if ( !$is_auth['auth_read'] || !$is_auth['auth_view'] )
{
	if ( !$userdata['session_logged_in'] )
	{
		redirect(append_sid('login.php', true));
	}

	$message = ( !$is_auth['auth_view'] ) ? '您没有权限查看这个论坛' : '对不起，仅 ' . $is_auth['auth_read_type'] . ' 可以阅读主题，所以不能在这个论坛搜索';

	trigger_error($message, E_USER_ERROR);;
}

$keyword = get_var('k', '');

if (empty($keyword))
{
	trigger_error('关键词不能为空' . back_link(append_sid(ROOT_PATH . 'viewforum.php?' . POST_FORUM_URL . '=' . $forum_id)), E_USER_ERROR);
}

$per = $board_config['posts_per_page'];

$start = get_pagination_start($per);

$keyword = @mysql_real_escape_string($keyword);

page_header('搜索帖子');

$search_time_start = start_runtime();

$sql = "SELECT topic_id, topic_title
	FROM " . TOPICS_TABLE . "
	WHERE forum_id = $forum_id
		AND topic_title LIKE '%$keyword%'
	ORDER BY topic_time DESC
	LIMIT $start, $per";

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法搜索帖子', E_USER_WARNING);
}

if (!$db->sql_numrows($result))
{
	$template->assign_block_vars('not_result', array());
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('topicrow', array(
		'ROW_CLASS'		=> $row_class,
		'TOPIC_TITLE' 	=> $row['topic_title'],
		'U_TOPIC'		=> append_sid(ROOT_PATH . 'viewtopic.php?' . POST_TOPIC_URL . '=' . $row['topic_id'])
		)
	);
	$i++;
}

$sql = "SELECT COUNT(topic_id) AS total
	FROM " . TOPICS_TABLE . "
	WHERE forum_id = $forum_id
		AND topic_title LIKE '%$keyword%'";

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计搜索结果', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

$search_time = spent_runtime($search_time_start);

$base_url = ROOT_PATH . 'bbs/search_topic.php?' . POST_FORUM_URL . "=$forum_id&k=$keyword";
$template->set_filenames(array(
	'body' => 'bbs/search_topic.tpl')
);

$template->assign_vars(array(
	'PAGINATION' 	=> generate_pagination($base_url, $row['total'], $per, $start),
	'SEARCH_TOTAL' 	=> $row['total'],
	'SEARCH_TIME' 	=> $search_time,
	'FORUM_NAME' 	=> $forum_row['forum_name'],
	'U_BACK'		=> append_sid(ROOT_PATH . 'viewforum.php?' . POST_FORUM_URL . '=' . $forum_id))
);

$template->pparse('body');

page_footer();

?>