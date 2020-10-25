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
	exit;
}

if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	trigger_error('没有此用户', E_USER_ERROR);
}
$user_id = intval($_GET[POST_USERS_URL]);

if ($board_config['disable_mod'])
{
	trigger_error('附件功能未启用', E_USER_ERROR);
}

$real_filename 			= 'real_filename';
$default_sort_method 	= 'downloads';
$default_sort_order 	= 'DESC';

$start = get_pagination_start($board_config['topics_per_page']);

if(isset($_POST['order']))
{
	$sort_order = ($_POST['order'] == 'ASC') ? 'ASC' : 'DESC';
}
else if(isset($_GET['order']))
{
	$sort_order = ($_GET['order'] == 'ASC') ? 'ASC' : 'DESC';
}
else
{
	$sort_order = $default_sort_order;
}

if(isset($_GET['mode_sort']) || isset($_POST['mode_sort']))
{
	$mode = (isset($_POST['mode_sort'])) ? $_POST['mode_sort'] : $_GET['mode_sort'];
}
else
{
	$mode = $default_sort_method;
}

$mode_types_text 	= array('文件名', '文件大小', '下载次数', '上传时间');
$mode_types 		= array('filename', 'filesize', 'downloads', 'post_time');

$select_sort_mode = '<select name="mode">';
for($i = 0; $i < count($mode_types_text); $i++)
{
	$selected = ( $mode == $mode_types[$i] ) ? ' selected="selected"' : '';
	$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
}
$select_sort_mode .= '</select>';

$select_sort_order = '<select name="order">';
if($sort_order == 'ASC')
{
	$select_sort_order .= '<option value="ASC" selected="selected">升序</option><option value="DESC">降序</option>';
}
else
{
	$select_sort_order .= '<option value="ASC">升序</option><option value="DESC" selected="selected">降序</option>';
}
$select_sort_order .= '</select>';

switch ($mode)
{
	case 'filename':
		$order_by = '' . $real_filename . ' ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'filesize':
		$order_by = 'filesize ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'downloads':
		$order_by = 'download_count ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'post_time':
		$order_by = 'filetime ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	default:
		trigger_error('请看看在 attachments.php 文件并定义有效的排序顺序默认值。', E_USER_ERROR);
		break;
}

$sql = "SELECT c.cat_title, c.cat_id, f.forum_name, f.forum_id  
	FROM " . CATEGORIES_TABLE . " c, " . FORUMS_TABLE . " f
	WHERE f.cat_id = c.cat_id 
	ORDER BY c.cat_id, f.forum_order";
	
if ( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain forum_name/forum_id', E_USER_WARNING);
}

$is_auth_ary 			= auth(AUTH_READ, AUTH_LIST_ALL, $userdata);
$is_download_auth_ary 	= auth(AUTH_DOWNLOAD, AUTH_LIST_ALL, $userdata);

$forum_ids = array();
$select_forums = '';
while( $row = $db->sql_fetchrow($result) )
{
	if ( ( $is_auth_ary[$row['forum_id']]['auth_read'] ) && ( $is_download_auth_ary[$row['forum_id']]['auth_download'] ) )
	{
		$select_forums = true;
		$forum_ids[] = $row['forum_id'];
	}
}

if ( $select_forums == '' )
{
	trigger_error('您不能查看这附件！', E_USER_ERROR);
}

page_header($page_title); 

$template->set_filenames(array(
	'body' => 'ucp/view_attachments.tpl')
);

$template->assign_vars(array(
	'S_MODE_SELECT' 	=> $select_sort_mode,
	'S_ORDER_SELECT' 	=> $select_sort_order,
	'S_MODE_ACTION' 	=> append_sid("ucp.php?mode=viewfiles&amp;" . POST_USERS_URL ."=$user_id"))
);

$sql = "SELECT a.post_id, t.topic_title, d.*
	FROM " . ATTACHMENTS_TABLE . " a, " . ATTACHMENTS_DESC_TABLE . " d, "  . POSTS_TABLE . " p, " . TOPICS_TABLE . " t
	WHERE (a.post_id = p.post_id) AND a.user_id_1 = $user_id 
		AND (p.forum_id IN (" . implode(', ', $forum_ids) . ")) 
		AND (p.topic_id = t.topic_id) 
		AND (a.attach_id = d.attach_id)
	ORDER BY $order_by";
if (!($result = $db->sql_query($sql)))
{ 
	trigger_error('Couldn\'t query attachments', E_USER_WARNING);
}
	
if ( !($attachments = $db->sql_fetchrowset($result)) )
{
	trigger_error('附件不存在', E_USER_ERROR);
}
$num_attachments = $db->sql_numrows($result);

for ($i = 0; $i < $num_attachments; $i++) 
{ 
	$class = ( !($i % 2) ) ? 'row1' : 'row2';
	$post_title = $attachments[$i]['topic_title'];

	$view_topic = append_sid('viewtopic.php?' . POST_POST_URL . '=' . $attachments[$i]['post_id'] . '#' . $attachments[$i]['post_id']);
	$post_title = '<a href="' . $view_topic . '">' . $post_title . '</a>';
	$filename = $attachments[$i][$real_filename];

	$view_attachment = append_sid(ROOT_PATH . 'download.php?id=' . intval($attachments[$i]['attach_id']));
	$filename_link = '<a href="' . $view_attachment . '">' . $filename . '</a>';

	$filesize = $attachments[$i]['filesize'];
	$size_lang = ($filesize >= 1048576) ? 'MB' : ( ($filesize >= 1024) ? 'KB' : 'Bytes');

	if ($filesize >= 1048576)
	{
		$filesize = (round((round($filesize / 1048576 * 100) / 100), 2));
	}
	else if ($filesize >= 1024)
	{
		$filesize = (round((round($filesize / 1024 * 100) / 100), 2));
	}

	$template->assign_block_vars('attachrow', array(
		'ROW_NUMBER' 		=> $i + $start + 1,
		'ROW_CLASS' 		=> $class,

		'FILENAME' 			=> $filename,
		'SIZE' 				=> $filesize,
		'SIZE_LANG' 		=> $size_lang,
		'DOWNLOAD_COUNT' 	=> $attachments[$i]['download_count'],
		'ATTACH_ID' 		=> $attachments[$i]['attach_id'],
		'POST_TIME' 		=> create_date($board_config['default_dateformat'], $attachments[$i]['filetime'], $board_config['board_timezone']),
		'POST_TITLE' 		=> $post_title,

		'VIEW_ATTACHMENT' 	=> $filename_link)
	);
}

$sql = "SELECT count(*) AS total
	FROM " . ATTACHMENTS_TABLE . " a, " . POSTS_TABLE . " p
	WHERE a.user_id_1 = $user_id 
		AND (a.post_id = p.post_id) 
		AND (p.forum_id IN (" . implode(', ', $forum_ids) . "))";
if (!($result = $db->sql_query($sql))) 
{
	trigger_error('Error getting total users', E_USER_WARNING);
}

if ( $total = $db->sql_fetchrow($result) )
{
	$total = $total['total'];

	$pagination = generate_pagination("ucp.php?mode=viewfiles&amp;" . POST_USERS_URL ."=$user_id&amp;mode_sort=$mode&amp;order=$sort_order", $total, $board_config['topics_per_page'], $start);
}

$template->assign_vars(array(
	'U_UCP'			=> append_sid('ucp.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $_GET[POST_USERS_URL]),
	'PAGINATION' 	=> $pagination)
);

$template->pparse('body');

page_footer(); 

?>