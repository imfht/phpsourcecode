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
	$file = basename(__FILE__);
	$module['会员']['等级'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');

$cancel = ( isset($_POST['cancel']) || isset($_POST['cancel']) ) ? true : false;
$no_page_header = $cancel;

require('./pagestart.php');


if ($cancel)
{
	redirect('admin/' . append_sid('admin_ranks.php', true));
}

if( isset($_GET['mode']) || isset($_POST['mode']) )
{
	$mode = (isset($_GET['mode'])) ? $_GET['mode'] : $_POST['mode'];
	$mode = htmlspecialchars($mode);
}
else 
{

	if( isset($_POST['add']) )
	{
		$mode = 'add';
	}
	else if( isset($_POST['save']) )
	{
		$mode = 'save';
	}
	else
	{
		$mode = '';
	}
}

$mode = ( in_array($mode, array('add', 'edit', 'save', 'delete')) ) ? $mode : '';

if( $mode != '' )
{
	if( $mode == 'edit' || $mode == 'add' )
	{

		$rank_id = ( isset($_GET['id']) ) ? intval($_GET['id']) : 0;
		
		$s_hidden_fields = '';
		
		if( $mode == 'edit' )
		{
			if( empty($rank_id) )
			{
				trigger_error('您必须选择要编辑的等级', E_USER_ERROR);
			}

			$sql = 'SELECT * 
				FROM ' . RANKS_TABLE . '
				WHERE rank_id = ' . $rank_id;
				
			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Couldn\'t obtain rank data', E_USER_WARNING);
			}
			
			$rank_info = $db->sql_fetchrow($result);
			
			$s_hidden_fields .= '<input type="hidden" name="id" value="' . $rank_id . '" />';

		}
		else
		{
			$rank_info['rank_title'] 	= '';
			$rank_info['rank_special'] 	= 0;
			$rank_info['rank_min']		= '';
			$rank_info['rank_image']	= '';
		}

		$s_hidden_fields 		.= '<input type="hidden" name="mode" value="save" />';

		$rank_is_special 		= ( $rank_info['rank_special'] ) ? 'checked="checked"' : '';
		$rank_is_not_special 	= ( !$rank_info['rank_special'] ) ? 'checked="checked"' : '';
		
		$template->set_filenames(array(
			'body' => 'admin/ranks_edit_body.tpl')
		);

		$template->assign_vars(array(	
			'L_TITLE'				=> ($mode == 'edit') ? '编辑等级' : '新建等级',
			'RANK' 					=> $rank_info['rank_title'],
			'SPECIAL_RANK' 			=> $rank_is_special,
			'NOT_SPECIAL_RANK' 		=> $rank_is_not_special,
			'MINIMUM' 				=> $rank_info['rank_min'],
			'IMAGE' 				=> ( $rank_info['rank_image'] != '' ) ? $rank_info['rank_image'] : '',
			'IMAGE_DISPLAY' 		=> ( $rank_info['rank_image'] != '' ) ? '<img src="../' . $rank_info['rank_image'] . '" />' : '',
			
			'U_ADMIN_RANKS'			=> append_sid('admin_ranks.php'),
			
			'S_RANK_ACTION' 		=> append_sid('admin_ranks.php'),
			'S_HIDDEN_FIELDS' 		=> $s_hidden_fields)
		);
		
	}
	else if( $mode == 'save' )
	{

		$rank_id 		= ( isset($_POST['id']) ) ? intval($_POST['id']) : 0;
		$rank_title 	= ( isset($_POST['title']) ) ? trim($_POST['title']) : '';
		$special_rank 	= ( $_POST['special_rank'] == 1 ) ? TRUE : 0;
		$min_posts 		= ( isset($_POST['min_posts']) ) ? intval($_POST['min_posts']) : -1;
		$rank_image 	= ( (isset($_POST['rank_image'])) ) ? trim($_POST['rank_image']) : '';

		if( $rank_title == '' )
		{
			trigger_error('等级的名称是不能为空的', E_USER_ERROR);
		}

		if( $special_rank == 1 )
		{
			$max_posts = -1;
			$min_posts = -1;
		}

		if($rank_image != '')
		{
			if ( !preg_match("/(\.gif|\.png|\.jpg)$/is", $rank_image))
			{
				$rank_image = '';
			}
		}

		if ($rank_id)
		{
			if (!$special_rank)
			{
				$sql = 'UPDATE ' . USERS_TABLE . " 
					SET user_rank = 0 
					WHERE user_rank = $rank_id";

				if( !$result = $db->sql_query($sql) ) 
				{
					trigger_error('等级名称已经被成功删除，尽管如此，使用该等级的用户帐号没有获得更新，您需要手动复置那些使用过该等级的用户帐号', E_USER_WARNING);
				}
			}
			$sql = 'UPDATE ' . RANKS_TABLE . "
				SET rank_title = '" . $db->sql_escape($rank_title) . "', rank_special = $special_rank, rank_min = $min_posts, rank_image = '" . $db->sql_escape($rank_image) . "'
				WHERE rank_id = $rank_id";

			$message = '修改成功！';
		}
		else
		{
			$sql = 'INSERT INTO ' . RANKS_TABLE . " (rank_title, rank_special, rank_min, rank_image)
				VALUES ('" . $db->sql_escape($rank_title) . "', $special_rank, $min_posts, '" . $db->sql_escape($rank_image) . "')";

			$message = '成功添加';
		}
		
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Couldn\'t update/insert into ranks table', E_USER_WARNING);
		}

		$message .= '<br />点击 <a href="' . append_sid('admin_ranks.php') . '">这里</a> 返回等级列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

		trigger_error($message);

	}
	else if( $mode == 'delete' )
	{
	
		if( isset($_POST['id']) || isset($_GET['id']) )
		{
			$rank_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : intval($_GET['id']);
		}
		else
		{
			$rank_id = 0;
		}

		$confirm = isset($_POST['confirm']);
		
		if( $rank_id && $confirm )
		{
			$sql = 'DELETE FROM ' . RANKS_TABLE . "
				WHERE rank_id = $rank_id";
			
			if( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t delete rank data', E_USER_WARNING);
			}
			
			$sql = 'UPDATE ' . USERS_TABLE . " 
				SET user_rank = 0 
				WHERE user_rank = $rank_id";

			if( !$db->sql_query($sql) ) 
			{
				trigger_error('Couldn\'t delete user_rank data', E_USER_WARNING);
			}

			$message = '删除成功！<br />点击 <a href="' . append_sid('admin_ranks.php') . '">这里</a> 返回等级列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);

		}
		elseif( $rank_id && !$confirm)
		{
			$template->set_filenames(array(
				'body' => 'admin/confirm_body.tpl')
			);

			$hidden_fields = '<input type="hidden" name="mode" value="delete" /><input type="hidden" name="id" value="' . $rank_id . '" />';

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '删除',
				'MESSAGE_TEXT' 		=> '您真的要删除这个等级？',

				'L_YES' 			=> '是',
				'L_NO' 				=> '否',

				'S_CONFIRM_ACTION' 	=> append_sid('admin_ranks.php'),
				'S_HIDDEN_FIELDS' 	=> $hidden_fields)
			);
		}
		else
		{
			trigger_error('您必须指定要删除的等级', E_USER_ERROR);
		}
	}

	$template->pparse('body');

	page_footer();
}

$template->set_filenames(array(
	'body' => 'admin/ranks_list_body.tpl')
);

$sql = 'SELECT * 
	FROM ' . RANKS_TABLE . '
	ORDER BY rank_min ASC, rank_special ASC';
if( !$result = $db->sql_query($sql) )
{
	trigger_error('Couldn\'t obtain ranks data', E_USER_WARNING);
}
$rank_count = $db->sql_numrows($result);

$rank_rows = $db->sql_fetchrowset($result);

$template->assign_vars(array(
	
	'S_RANKS_ACTION' 	=> append_sid('admin_ranks.php'))
);

for($i = 0; $i < $rank_count; $i++)
{
	$rank				= $rank_rows[$i]['rank_title'];
	$special_rank 		= $rank_rows[$i]['rank_special'];
	$rank_id 			= $rank_rows[$i]['rank_id'];
	$rank_min 			= $rank_rows[$i]['rank_min'];
	$rank_min 			= ( $special_rank == 1 ) ? '-' : $rank_rows[$i]['rank_min'];
	$number 			= $i + 1;
	$row_class 			= ( !($i % 2) ) ? 'row1' : 'row2';
	$rank_is_special 	= ( $special_rank ) ? '是' : '否';
	
	$template->assign_block_vars('ranks', array(
		'L_NUMBER'			=> $number,
		'ROW_CLASS' 		=> $row_class,
		'RANK' 				=> $rank,
		'SPECIAL_RANK' 		=> $rank_is_special,
		'RANK_MIN' 			=> $rank_min,

		'U_RANK_EDIT' 		=> append_sid("admin_ranks.php?mode=edit&amp;id=$rank_id"),
		'U_RANK_DELETE' 	=> append_sid("admin_ranks.php?mode=delete&amp;id=$rank_id"))
	);
}

$template->pparse('body');

page_footer();

?>