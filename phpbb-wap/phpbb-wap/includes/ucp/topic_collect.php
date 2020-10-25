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

if ($userdata['user_id'] != $profiledata['user_id'])
{
	if ($userdata['user_level'] != ADMIN)
	{
		trigger_error('您没有权限');
	}
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add')
{
	$add_topic = (int) get_var('tc', '');

	$sql = 'SELECT topic_title
		FROM ' . TOPICS_TABLE . '
		WHERE topic_id = ' . $add_topic;

	if (!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询帖子数据', E_USER_WARNING);
	}

	if ($row = $db->sql_fetchrow($result))
	{
		
		$sql = 'SELECT tc_id
			FROM ' . TOPIC_COLLECT_TABLE . '
			WHERE tc_topic = ' . $add_topic;

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('无法查询帖子收藏数据', E_USER_WARNING);
		}

		if ($db->sql_fetchrow($result))
		{
			trigger_error('该帖子您已经收藏' . back_link());
		}

		$sql = 'INSERT INTO ' . TOPIC_COLLECT_TABLE . " (tc_topic, tc_user, tc_title) 
			VALUES ($add_topic, {$userdata['user_id']}, '" . $db->sql_escape($row['topic_title']) . "')";

		if (!$db->sql_query($sql))
		{
			trigger_error('无法添加收藏', E_USER_WARNING);
		}

		trigger_error('收藏成功' . back_link('viewtopic.php?' . POST_TOPIC_URL . '=' . $add_topic));
	}
	else
	{
		trigger_error('没有这个帖子' . back_link());
	}
}
elseif ($action == 'delete')
{
	$delete_id = get_var('tc', '');

	$delete_user = ($userdata['user_level'] == ADMIN) ? $profiledata['user_id'] : $userdata['user_id'];

	$sql = 'DELETE FROM ' . TOPIC_COLLECT_TABLE . ' 
		WHERE tc_user = ' . $delete_user . '
		  AND tc_id = ' . $delete_id;

	if (!$db->sql_query($sql))
	{
		trigger_error('无法删除收藏的帖子', E_USER_WARNING);
	}

	redirect(append_sid("ucp.php?mode=topic_collect&" . POST_USERS_URL . "=" . $profiledata['user_id'], true));
}

$per = $profiledata['user_topics_per_page'];
$start = get_pagination_start($per);

$sql = 'SELECT tc_id, tc_topic, tc_title
	FROM ' . TOPIC_COLLECT_TABLE . '
	WHERE tc_user = ' . $profiledata['user_id'] . '
	ORDER BY tc_id DESC
	LIMIT ' . $start . ', ' . $per;

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法查询帖子收藏数据', E_USER_WARNING);
}

$i = 0;
while ($row = $db->sql_fetchrow($result))
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('topic_collect', array(
		'NUMBER' => $i + $start + 1,
		'ROW_CLASS' => $row_class, 
		'TITLE' => $row['tc_title'],
		'U_TOPIC' => append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $row['tc_topic']),
		'U_DELETE' => append_sid('ucp.php?mode=topic_collect&action=delete&' . POST_USERS_URL .'=' . $profiledata['user_id'] . '&tc=' . $row['tc_id']))
	);
	$i++;
}

$sql = 'SELECT count(tc_id) AS total
	FROM ' . TOPIC_COLLECT_TABLE . '
	WHERE tc_user = ' . $profiledata['user_id'];

if (!$result = $db->sql_query($sql))
{
	trigger_error('无法统计用户收藏的帖子', E_USER_WARNING);
}

$row = $db->sql_fetchrow($result);

if (!$row['total'])
{
	$template->assign_block_vars('not_topic_collect', array());
}

$pagination = generate_pagination("ucp.php?mode=topic_collect&" . POST_USERS_URL . "=" . $profiledata['user_id'], $row['total'], $per, $start);

page_header($profiledata['username'] . '的帖子收藏');

$template->set_filenames(array(
	'body' => 'ucp/topic_collect.tpl')
);

$template->assign_vars(array(
	'U_BACK' => append_sid('ucp.php?mode=viewprofile&' . POST_USERS_URL . '=' . $profiledata['user_id']),
	'ALL_TOPIC_COLLECT' => $row['total'],
	'PAGINATION' 	=> $pagination)
);

$template->pparse('body');

page_footer();

?>