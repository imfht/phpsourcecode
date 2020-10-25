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
	$module['相册']['清除缓存'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

if( !isset($_POST['confirm']) )
{

	if( isset($_POST['cancel']) )
	{
		redirect(append_sid("admin/index.php?pane=left", true));
	}

	$template->set_filenames(array(
		'body' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' => '确认',

		'MESSAGE_TEXT' => '是否清除相册缓存？',

		'L_NO' => '否',
		'L_YES' => '是',

		'S_CONFIRM_ACTION' => append_sid("admin_album_clearcache.php"),
		)
	);

	$template->pparse('body');

	page_footer();
}
else
{
	$cache_dir = @opendir('../' . ALBUM_CACHE_PATH);

	while( $cache_file = @readdir($cache_dir) )
	{
		if( preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $cache_file) )
		{
			@unlink('../' . ALBUM_CACHE_PATH . $cache_file);
		}
	}

	@closedir($cache_dir);

	trigger_error('缓存已清除' . back_link());
}

?>