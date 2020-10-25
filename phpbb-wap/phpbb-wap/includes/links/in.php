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

$link_id = get_var('id', 0);

$sql = 'UPDATE ' . LINKS_TABLE . '
	SET link_in = link_in + 1, link_last_visit = ' . time() . '
	WHERE link_id = ' . (int) $link_id;

if (!$db->sql_query($sql))
{
	trigger_error('无法更新链入数', E_USER_WARNING);
}

$template->assign_vars(array(
	'META' 		=> '<meta http-equiv="refresh" content="3;url=' . append_sid('links.php') . '">',
	'U_URL'		=> append_sid('index.php'))
);

page_header('正在跳转...');

$template->set_filenames(array(
	'jump' => 'links/link_jump.tpl')
);
$template->pparse('jump');
page_footer();		

?>