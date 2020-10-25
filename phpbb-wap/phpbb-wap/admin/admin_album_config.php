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
	$module['相册']['参数配置'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

$sql = "SELECT * FROM " . ALBUM_CONFIG_TABLE;

if(!$result = $db->sql_query($sql))
{
	message_die(CRITICAL_ERROR, "Could not query Album config information", "", __LINE__, __FILE__, $sql);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($_POST['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($_POST[$config_name]) ) ? $_POST[$config_name] : $default_config[$config_name];

		if ($config_name == 'cols_per_page') $new['cols_per_page'] = '1';

		if( isset($_POST['submit']) )
		{
			$sql = "UPDATE " . ALBUM_CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update Album configuration for $config_name", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	if( isset($_POST['submit']) )
	{
		$message = "配置参数已更新<br />点击 <a href=\"" . append_sid("admin_album_config.php") . "\">这里</a> 返回相册配置页面<br />点击 <a href=\"" . append_sid("index.php") . "\">这里</a> 返回超级面板";

		trigger_error($message);
	}
}

$template->set_filenames(array(
	"body" => "admin/album_config_body.tpl")
);

$template->assign_vars(array(
	'S_ALBUM_CONFIG_ACTION' => append_sid('admin_album_config.php'),

	'MAX_PICS' => $new['max_pics'],
	'MAX_FILE_SIZE' => $new['max_file_size'],
	'MAX_WIDTH' => $new['max_width'],
	'MAX_HEIGHT' => $new['max_height'],
	'ROWS_PER_PAGE' => $new['rows_per_page'],
	'COLS_PER_PAGE' => $new['cols_per_page'],
	'THUMBNAIL_QUALITY' => $new['thumbnail_quality'],
	'THUMBNAIL_SIZE' => $new['thumbnail_size'],
	'PERSONAL_GALLERY_LIMIT' => $new['personal_gallery_limit'],

	'USER_PICS_LIMIT' => $new['user_pics_limit'],
	'MOD_PICS_LIMIT' => $new['mod_pics_limit'],

	'THUMBNAIL_CACHE_ENABLED' => ($new['thumbnail_cache'] == 1) ? 'checked="checked"' : '',
	'THUMBNAIL_CACHE_DISABLED' => ($new['thumbnail_cache'] == 0) ? 'checked="checked"' : '',

	'JPG_ENABLED' => ($new['jpg_allowed'] == 1) ? 'checked="checked"' : '',
	'JPG_DISABLED' => ($new['jpg_allowed'] == 0) ? 'checked="checked"' : '',

	'PNG_ENABLED' => ($new['png_allowed'] == 1) ? 'checked="checked"' : '',
	'PNG_DISABLED' => ($new['png_allowed'] == 0) ? 'checked="checked"' : '',

	'GIF_ENABLED' => ($new['gif_allowed'] == 1) ? 'checked="checked"' : '',
	'GIF_DISABLED' => ($new['gif_allowed'] == 0) ? 'checked="checked"' : '',

	'PIC_DESC_MAX_LENGTH' => $new['desc_length'],

	'HOTLINK_PREVENT_ENABLED' => ($new['hotlink_prevent'] == 1) ? 'checked="checked"' : '',
	'HOTLINK_PREVENT_DISABLED' => ($new['hotlink_prevent'] == 0) ? 'checked="checked"' : '',

	'HOTLINK_ALLOWED' => $new['hotlink_allowed'],

	'PERSONAL_GALLERY_USER' => ($new['personal_gallery'] == ALBUM_USER) ? 'checked="checked"' : '',
	'PERSONAL_GALLERY_PRIVATE' => ($new['personal_gallery'] == ALBUM_PRIVATE) ? 'checked="checked"' : '',
	'PERSONAL_GALLERY_ADMIN' => ($new['personal_gallery'] == ALBUM_ADMIN) ? 'checked="checked"' : '',

	'PERSONAL_GALLERY_VIEW_ALL' => ($new['personal_gallery_view'] == ALBUM_GUEST) ? 'checked="checked"' : '',
	'PERSONAL_GALLERY_VIEW_REG' => ($new['personal_gallery_view'] == ALBUM_USER) ? 'checked="checked"' : '',
	'PERSONAL_GALLERY_VIEW_PRIVATE' => ($new['personal_gallery_view'] == ALBUM_PRIVATE) ? 'checked="checked"' : '',

	'RATE_ENABLED' => ($new['rate'] == 1) ? 'checked="checked"' : '',
	'RATE_DISABLED' => ($new['rate'] == 0) ? 'checked="checked"' : '',

	'RATE_SCALE' => $new['rate_scale'],

	'COMMENT_ENABLED' => ($new['comment'] == 1) ? 'checked="checked"' : '',
	'COMMENT_DISABLED' => ($new['comment'] == 0) ? 'checked="checked"' : '',

	'NO_GD' => ($new['gd_version'] == 0) ? 'checked="checked"' : '',
	'GD_V1' => ($new['gd_version'] == 1) ? 'checked="checked"' : '',
	'GD_V2' => ($new['gd_version'] == 2) ? 'checked="checked"' : '',

	'SORT_TIME' => ($new['sort_method'] == 'pic_time') ? 'selected="selected"' : '',
	'SORT_PIC_TITLE' => ($new['sort_method'] == 'pic_title') ? 'selected="selected"' : '',
	'SORT_USERNAME' => ($new['sort_method'] == 'pic_user_id') ? 'selected="selected"' : '',
	'SORT_VIEW' => ($new['sort_method'] == 'pic_view_count') ? 'selected="selected"' : '',
	'SORT_RATING' => ($new['sort_method'] == 'rating') ? 'selected="selected"' : '',
	'SORT_COMMENTS' => ($new['sort_method'] == 'comments') ? 'selected="selected"' : '',
	'SORT_NEW_COMMENT' => ($new['sort_method'] == 'new_comment') ? 'selected="selected"' : '',

	'SORT_ASC' => ($new['sort_order'] == 'ASC') ? 'selected="selected"' : '',
	'SORT_DESC' => ($new['sort_order'] == 'DESC') ? 'selected="selected"' : '',

	'FULLPIC_POPUP_ENABLED' => ($new['fullpic_popup'] == 1) ? 'checked="checked"' : '',
	'FULLPIC_POPUP_DISABLED' => ($new['fullpic_popup'] == 0) ? 'checked="checked"' : '',

	'S_GUEST' => ALBUM_GUEST,
	'S_USER' => ALBUM_USER,
	'S_PRIVATE' => ALBUM_PRIVATE,
	'S_MOD' => ALBUM_MOD,
	'S_ADMIN' => ALBUM_ADMIN)
);

$template->pparse("body");

page_footer();

?>