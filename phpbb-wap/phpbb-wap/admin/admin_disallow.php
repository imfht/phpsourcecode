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
	$module['会员']['敏感用户名'] = $filename;
	
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

if( isset($_POST['add_name']) )
{
	require(ROOT_PATH . 'includes/functions/validate.php');

	$disallowed_user = ( isset($_POST['disallowed_user']) ) ? trim($_POST['disallowed_user']) : trim($_GET['disallowed_user']);

	if ($disallowed_user == '')
	{
		trigger_error('敏感用户名选项不能为空', E_USER_ERROR);
	}
	if( !validate_username($disallowed_user) )
	{
		$message = '无法禁止使用您所输入的用户名，该用户名可能已在禁用列表内或已被注册使用';
	}
	else
	{
		$sql = 'INSERT INTO ' . DISALLOW_TABLE . " (disallow_username) 
			VALUES('" . $db->sql_escape($disallowed_user) . "')";
		if (!$db->sql_query($sql))
		{
			trigger_error('Could not add disallowed user.', E_USER_WARNING);
		}
		
		$message = '成功添加';
	}

	$message .= '<br />点击 <a href="' . append_sid('admin_disallow.php') . '">这里</a> 返回敏感用户名页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

	trigger_error($message);
}
else if( isset($_POST['delete_name']) )
{
	$disallowed_id = ( isset($_POST['disallowed_id']) ) ? intval( $_POST['disallowed_id'] ) : intval( $_GET['disallowed_id'] );
	
	$sql = 'DELETE FROM ' . DISALLOW_TABLE . ' 
		WHERE disallow_id = ' . $disallowed_id;
	if( !$db->sql_query($sql) )
	{
		trigger_error('Couldn\'t removed disallowed user.', E_USER_WARNING);
	}
	
	$message = '成功删除<br />点击 <a href="' . append_sid('admin_disallow.php') . '">这里</a> 返回敏感用户名页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

	trigger_error($message);
}

$sql = 'SELECT * 
	FROM ' . DISALLOW_TABLE;
if( !$result = $db->sql_query($sql) )
{
	trigger_error('Couldn\'t get disallowed users.', E_USER_WARNING);
}

$disallowed = $db->sql_fetchrowset($result);

$disallow_select = '';
if( !$db->sql_numrows($result) )
{
	$disallow_select .= '没有禁止使用的用户名';
}
else 
{
	$template->assign_block_vars('not_disallowed', array());
	
	$disallow_select = '<select name="disallowed_id">';
	$user = array();
	for( $i = 0; $i < count($disallowed); $i++ )
	{
		$disallow_select .= '<option value="' . $disallowed[$i]['disallow_id'] . '">' . $disallowed[$i]['disallow_username'] . '</option>';
	}
	$disallow_select .= '</select>';
}

$template->set_filenames(array(
	'body' => 'admin/disallow_body.tpl')
);

$template->assign_vars(array(
	'S_DISALLOW_SELECT' 		=> $disallow_select,
	'S_FORM_ACTION' 			=> append_sid('admin_disallow.php'))
);

$template->pparse('body');

page_footer();

?>