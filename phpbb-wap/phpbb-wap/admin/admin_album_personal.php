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
	$module['相册']['个人相册'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

if( !isset($_POST['submit']) )
{
	$template->set_filenames(array(
		'body' => 'admin/album_personal_body.tpl')
	);

	$sql = "SELECT group_id, group_name
			FROM " . GROUPS_TABLE . "
			WHERE group_single_user <> " . TRUE ."
			ORDER BY group_name ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't get group list", "", __LINE__, __FILE__, $sql);
	}

	$groupdata = array();
	while( $row = $db->sql_fetchrow($result) )
	{
		$groupdata[] = $row;
	}

	$sql = "SELECT *
			FROM ". ALBUM_CONFIG_TABLE ."
			WHERE config_name = 'personal_gallery_private'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't get Album info", "", __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);
	$private_groups = explode(',', $row['config_value']);

	for($i = 0; $i < count($groupdata); $i++)
	{
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
		$template->assign_block_vars('grouprow', array(
			'ROW_CLASS' => $row_class,
			'GROUP_ID' => $groupdata[$i]['group_id'],
			'GROUP_NAME' => $groupdata[$i]['group_name'],
			'PRIVATE_CHECKED' => (in_array($groupdata[$i]['group_id'], $private_groups)) ? 'checked="checked"' : ''
			)
		);
	}

	$template->assign_vars(array(
		'S_ALBUM_ACTION' => append_sid('admin_album_personal.php')
		)
	);

	$template->pparse('body');

	page_footer();
}
else
{
	$private_groups = @implode(',', $_POST['private']);

	$sql = "UPDATE ". ALBUM_CONFIG_TABLE ."
			SET config_value = '$private_groups'
			WHERE config_name = 'personal_gallery_private'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update Album config table', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Album_personal_successfully'] . '<br /><br />' . sprintf($lang['Click_return_album_personal'], '<a href="' . append_sid("admin_album_personal.$phpEx") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');

	message_die(GENERAL_MESSAGE, $message);
}

?>