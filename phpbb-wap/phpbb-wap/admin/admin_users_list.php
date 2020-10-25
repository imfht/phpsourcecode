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

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['会员']['列表'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

$users_per_page = 25;
$start = get_pagination_start($users_per_page);

if( isset($_POST['sort']) )
{
	$sort_method = $_POST['sort'];
}
else if( isset($_GET['sort']) )
{
	$sort_method = $_GET['sort'];
}
else
{
	$sort_method = 'user_id';
}

if( isset($_POST['order']) )
{
	$sort_order = $_POST['order'];
}
else if( isset($_GET['order']) )
{
	$sort_order = $_GET['order'];
}
else
{
	$sort_order = '';
}


$template->set_filenames(array(
	'body' => 'admin/admin_users_list_body.tpl')
);

$sql = "SELECT count(user_id) as total FROM ".USERS_TABLE." WHERE user_id > 0";
if(!$result = $db->sql_query($sql))
{
	trigger_error("Could not count users", E_USER_WARNING);
}
$row = $db->sql_fetchrow($result);
$total_users = $row['total'];

$template->assign_vars(array(
	'U_LIST_ACTION' 			=> append_sid("admin_users_list.php"),
	'ID_SELECTED' 				=> ($sort_method == 'user_id') ? 'selected="selected"' : '',
	'USERNAME_SELECTED' 		=> ($sort_method == 'username') ? 'selected="selected"' : '',
	'POSTS_SELECTED' 			=> ($sort_method == 'user_posts') ? 'selected="selected"' : '',
	'LASTVISIT_SELECTED' 		=> ($sort_method == 'user_lastvisit') ? 'selected="selected"' : '',
	'ASC_SELECTED' 				=> ($sort_order != 'DESC') ? 'selected="selected"' : '',
	'DESC_SELECTED' 			=> ($sort_order == 'DESC') ? 'selected="selected"' : '',
	'TOTAL_USERS' 				=> $total_users)
);

$sql = "SELECT user_id, username, user_email, user_regdate, user_lastvisit, user_posts, user_active
		FROM ".USERS_TABLE."
		WHERE user_id > 0
		ORDER BY " . $sort_method . " " . $sort_order . "
		LIMIT ".$start.",".$users_per_page;
if(!$result = $db->sql_query($sql))
{
	trigger_error("Could not query Users information", E_USER_WARNING);
}

while( $row = $db->sql_fetchrow($result) )
{
	$userrow[] = $row;
}

for ($i = 0; $i < $users_per_page; $i++)
{
	if (empty($userrow[$i]))
	{
		break;
	}
	$number = $i + 1;
	$row_class = (($i % 2) == 0) ? 'row1' : 'row2';
	
	$template->assign_block_vars('userrow', array(
		'NUMBER'				=> $number,
		'ROW_CLASS' 			=> $row_class,
		'NUMBER' 				=> $userrow[$i]['user_id'],
		'USERNAME' 				=> $userrow[$i]['username'],
		'U_ADMIN_USER' 			=> append_sid('admin_users.php?mode=edit&amp;' . POST_USERS_URL . '=' . $userrow[$i]['user_id']),
		'U_ADMIN_USER_AUTH' 	=> append_sid('admin_ug_auth.php?mode=user&amp;' . POST_USERS_URL . '=' . $userrow[$i]['user_id']),
		'EMAIL' 				=> $userrow[$i]['user_email'],
		'JOINED'	 			=> create_date($userdata['user_dateformat'], $userrow[$i]['user_regdate'], $board_config['board_timezone']),
		'LAST_VISIT' 			=> (!$userrow[$i]['user_lastvisit']) ? '' : create_date($userdata['user_dateformat'], $userrow[$i]['user_lastvisit'], $board_config['board_timezone']),
		'POSTS' 				=> $userrow[$i]['user_posts'],
		'ACTIVE' 				=> ( $userrow[$i]['user_active'] ) ? '已激活' : '未激活'
		) 
	);
} 

$template->assign_vars(array(
	'PAGINATION' 	=> generate_pagination(append_sid("admin_users_list.php?sort=$sort_method&amp;order=$sort_order"), $total_users, $users_per_page, $start))
);

$template->pparse('body');

page_footer();

?>