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
require ROOT_PATH . 'common.php';

$userdata = $session->start($user_ip, PAGE_MODS);
init_userprefs($userdata);

$per = 5;
$start = get_pagination_start($per);

$page_title = 'MODS中心';

page_header($page_title);

$sql = "SELECT SQL_CALC_FOUND_ROWS mod_name, mod_desc, mod_dir
	FROM " . MODS_TABLE . "
	WHERE mod_power = 1 
		AND mod_show = 1 
	LIMIT $start , $per";

if ( !$result = $db->sql_query($sql) )
{
	trigger_error('查询 mods 表失败！', E_USER_WARNING);
}
$total_mod = $db->sql_numrows($result);

$mod_row = array();
while ($row = $db->sql_fetchrow($result))
{
	$mod_row[] = $row;
}

if ( !$total_mod )
{
	$template->assign_block_vars('empty_mods', array());
}

for ($i = 0; $i < $total_mod; $i++)
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('mods_row', array(
		'ROW_CLASS'			=> $row_class,
		'MOD_NAME'			=> $mod_row[$i]['mod_name'],
		'MOD_DESC'			=> $mod_row[$i]['mod_desc'],
		'U_LOADING'			=> append_sid('loading.php?mod=' . $mod_row[$i]['mod_dir'] . '&load=' . $mod_row[$i]['mod_dir']))
	);
}


$sql = "SELECT found_rows() AS rowcount"; 

if ( !$result = $db->sql_query($sql) )
{
	trigger_error('查询 mods 表失败！', E_USER_WARNING);
}

if ( !$total_mods = $db->sql_fetchrow($result) )
{
	trigger_error('目前还没有安装任何MOD' . back_link(append_sid('mods.php')), E_USER_ERROR);
}
$total_all_mods = $total_mods['rowcount'];

$pagination = generate_pagination("mods.php?", $total_all_mods, $per, $start);

$template->set_filenames(array(
	'body' => 'mods_body.tpl')
);

$template->assign_vars(array(
	'PAGINATION' 	=> $pagination)
);

$template->pparse('body');

page_footer();
?>