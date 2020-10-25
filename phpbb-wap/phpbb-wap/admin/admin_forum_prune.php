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

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['论坛']['清理'] = $filename;

	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');

require('./pagestart.php');
require(ROOT_PATH . 'includes/functions/prune.php');
require(ROOT_PATH . 'includes/functions/admin.php'); 

if( isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL]) )
{
	$forum_id = ( isset($_POST[POST_FORUM_URL]) ) ? $_POST[POST_FORUM_URL] : $_GET[POST_FORUM_URL];

	if( $forum_id == -1 )
	{
		$forum_sql = '';
	}
	else
	{
		$forum_id = intval($forum_id);
		$forum_sql = "AND forum_id = $forum_id";
	}
}
else
{
	$forum_id = '';
	$forum_sql = '';
}

$sql = 'SELECT f.*
	FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . " c
	WHERE c.cat_id = f.cat_id
	$forum_sql
	ORDER BY c.cat_order ASC, f.forum_order ASC";
if( !($result = $db->sql_query($sql)) )
{
	trigger_error('Could not obtain list of forums for pruning', E_USER_WARNING);
}

$forum_rows = array();
while( $row = $db->sql_fetchrow($result) )
{
	$forum_rows[] = $row;
}

if( isset($_POST['doprune']) )
{
	$prunedays = ( isset($_POST['prunedays']) ) ? intval($_POST['prunedays']) : 0;

	$prunedate = time() - ( $prunedays * 86400 );

	$template->set_filenames(array(
		'body' => 'admin/forum_prune_result_body.tpl')
	);

	for($i = 0; $i < count($forum_rows); $i++)
	{
		$p_result = prune($forum_rows[$i]['forum_id'], $prunedate);
		sync('forum', $forum_rows[$i]['forum_id']);

		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
	
		$template->assign_block_vars('prune_results', array(
			'ROW_CLASS' 		=> $row_class, 
			'FORUM_NAME' 		=> $forum_rows[$i]['forum_name'],
			'FORUM_TOPICS' 		=> $p_result['topics'],
			'FORUM_POSTS' 		=> $p_result['posts'])
		);
	}

	$template->assign_vars(array(
		'U_SELECT_FORUM' => append_sid('admin_forum_prune.php'))
	);
}
else
{

	if( empty($_POST[POST_FORUM_URL]) )
	{
		$template->set_filenames(array(
			'body' => 'admin/forum_prune_select_body.tpl')
		);

		$select_list = '<select name="' . POST_FORUM_URL . '">';
		$select_list .= '<option value="-1">所有论坛</option>';

		for($i = 0; $i < count($forum_rows); $i++)
		{
			$select_list .= '<option value="' . $forum_rows[$i]['forum_id'] . '">' . $forum_rows[$i]['forum_name'] . '</option>';
		}
		$select_list .= '</select>';

		$template->assign_vars(array(
			'S_FORUMPRUNE_ACTION'	=> append_sid('admin_forum_prune.php'),
			'S_FORUMS_SELECT' 		=> $select_list)
		);
	}
	else
	{
		$forum_id = intval($_POST[POST_FORUM_URL]);

		$template->set_filenames(array(
			'body' => 'admin/forum_prune_body.tpl')
		);

		$forum_name 	= ( $forum_id == -1 ) ? '所有论坛' : $forum_rows[0]['forum_name'];

		$prune_data 	= '<input type="text" name="prunedays" size="4">';

		$hidden_input 	= '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

		$template->assign_vars(array(
			'FORUM_NAME' 			=> $forum_name,
			'U_SELECT_FORUM' 		=> append_sid('admin_forum_prune.php'),
			'S_FORUMPRUNE_ACTION' 	=> append_sid('admin_forum_prune.php'),
			'S_PRUNE_DATA' 			=> $prune_data,
			'S_HIDDEN_VARS' 		=> $hidden_input)
		);
	}
}

$template->pparse('body');

page_footer();

?>