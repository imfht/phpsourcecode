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

/*
* MOD名称: 虚拟商店
* MOD支持地址: http://phpbb-wap.com
* MOD描述: 系统虚拟商店
* MOD作者: Crazy
* MOD版本: v1.0
* MOD显示: on
*/

// 请先登录
if ( !$userdata['session_logged_in'] )
{
	login_back('loading.php?mod=sign');
}

page_header('虚拟商店');

require 'shop.conn.php';

$i = 0;
foreach ($service as $key => $value)
{
	$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	$template->assign_block_vars('service', array(
		'ROW_CLASS' => $row_class,
		'U_BUY' => append_sid('loading.php?mod=shop&load=buy&to=' . $key),
		'NAME' => $value)
	);
	$i++;
}

$template->assign_vars(array(
	'POINTS_NAME' 	=> $board_config['points_name'],
	'U_BACK'		=> append_sid('mods.php'))
);

$template->set_filenames(array(
	'body' => 'shop_body.tpl')
);

$template->pparse('body');

page_footer();
?>