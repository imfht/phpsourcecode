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
	$module['系统']['敏感词'] = $file;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');

$cancel 		= ( isset($_POST['cancel']) ) ? true : false;
$no_page_header = $cancel;

require('./pagestart.php');

if ($cancel)
{
	redirect('admin/' . append_sid('admin_words.php', true));
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
		$word_id = ( isset($_GET['id']) ) ? intval($_GET['id']) : 0;

		$template->set_filenames(array(
			'body' => 'admin/words_edit_body.tpl')
		);

		$word_info = array('word' => '', 'replacement' => '');
		$s_hidden_fields = '';

		if( $mode == 'edit' )
		{
			if( $word_id )
			{
				$sql = 'SELECT * 
					FROM ' . WORDS_TABLE . ' 
					WHERE word_id = ' . $word_id;
				if(!$result = $db->sql_query($sql))
				{
					trigger_error('Could not query words table', E_USER_WARNING);
				}

				$word_info = $db->sql_fetchrow($result);
				$s_hidden_fields .= '<input type="hidden" name="id" value="' . $word_id . '" />';
			}
			else
			{
				trigger_error('您没有选择任何敏感词汇', E_USER_ERROR);
			}
		}

		$template->assign_vars(array(
			'L_WORDS_TITLE'		=> ($mode == 'add') ? '新增词汇' : '修改词汇',
			
			'WORD' 				=> $word_info['word'],
			'REPLACEMENT' 		=> $word_info['replacement'],
			
			'U_WORDS_LISTS'		=> append_sid('admin_words.php'),

			'S_WORDS_ACTION' 	=> append_sid('admin_words.php'),
			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
		);

		$template->pparse('body');

		page_footer();
	}
	else if( $mode == 'save' )
	{
		$word_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : 0;
		$word = ( isset($_POST['word']) ) ? trim($_POST['word']) : '';
		$replacement = ( isset($_POST['replacement']) ) ? trim($_POST['replacement']) : '';

		if($word == '' || $replacement == '')
		{
			trigger_error('您必须输入要过滤的文字及其替换文字', E_USER_ERROR);
		}

		if( $word_id )
		{
			$sql = 'UPDATE ' . WORDS_TABLE . " 
				SET word = '" . $db->sql_escape($word) . "', replacement = '" . $db->sql_escape($replacement) . "' 
				WHERE word_id = " . $word_id;
			$message = '您所选择的敏感词汇已经成功更新';
		}
		else
		{
			$sql = 'INSERT INTO ' . WORDS_TABLE . " (word, replacement) 
				VALUES ('" . $db->sql_escape($word) . "', '" . $db->sql_escape($replacement) . "')";
			$message = '已成功新增敏感词汇';
		}

		if(!$result = $db->sql_query($sql))
		{
			trigger_error('Could not insert data into words table', E_USER_WARNING);
		}

		$message .= '<br />点击 <a href="' . append_sid('admin_words.php') . '">这里</a>返回敏感词汇列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板首页';

		trigger_error($message);
	}
	else if( $mode == 'delete' )
	{
		if( isset($_POST['id']) ||  isset($_GET['id']) )
		{
			$word_id = ( isset($_POST['id']) ) ? $_POST['id'] : $_GET['id'];
			$word_id = intval($word_id);
		}
		else
		{
			$word_id = 0;
		}

		$confirm = isset($_POST['confirm']);

		if( $word_id && $confirm )
		{
			$sql = 'DELETE FROM ' . WORDS_TABLE . ' 
				WHERE word_id = ' . $word_id;

			if(!$result = $db->sql_query($sql))
			{
				trigger_error('Could not remove data from words table', E_USER_WARNING);
			}

			$message = '删除成功！<br />点击 <a href="' . append_sid('admin_words.php') . '">这里</a> 返回敏感词汇列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			
		}
		elseif( $word_id && !$confirm)
		{
			$template->set_filenames(array(
				'body' => 'admin/confirm_body.tpl')
			);

			$hidden_fields = '<input type="hidden" name="mode" value="delete" /><input type="hidden" name="id" value="' . $word_id . '" />';

			$template->assign_vars(array(
				'MESSAGE_TITLE' 	=> '删除确认',
				'MESSAGE_TEXT' 		=> '您要删除该敏感词汇？',

				'L_YES' 			=> '是',
				'L_NO' 				=> '否',

				'S_CONFIRM_ACTION' 	=> append_sid('admin_words.php'),
				'S_HIDDEN_FIELDS' 	=> $hidden_fields)
			);
		}
		else
		{
			trigger_error('您没有选择任何词汇', E_USER_ERROR);
		}
	}
}
else
{
	$template->set_filenames(array(
		'body' => 'admin/words_list_body.tpl')
	);

	$sql = 'SELECT * 
		FROM ' . WORDS_TABLE . ' 
		ORDER BY word';
	if( !$result = $db->sql_query($sql) )
	{
		trigger_error('Could not query words table', E_USER_WARNING);
	}

	$word_rows = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);
	$word_count = count($word_rows);

	$template->assign_vars(array(
		'S_WORDS_ACTION' 	=> append_sid('admin_words.php'),
		'S_HIDDEN_FIELDS' 	=> '')
	);

	if ( $word_count <= 1)
	{
		$template->assign_block_vars('empty_words', array());
	}
	
	for($i = 0; $i < $word_count; $i++)
	{
		$word = $word_rows[$i]['word'];
		$replacement = $word_rows[$i]['replacement'];
		$word_id = $word_rows[$i]['word_id'];

		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

		$template->assign_block_vars('words', array(
			'ROW_CLASS' 		=> $row_class,
			'WORD' 				=> $word,
			'REPLACEMENT' 		=> $replacement,

			'U_WORD_EDIT' 		=> append_sid('admin_words.php?mode=edit&amp;id=' . $word_id),
			'U_WORD_DELETE' 	=> append_sid('admin_words.php?mode=delete&amp;id=' . $word_id))
		);
	}
}

$template->pparse('body');

page_footer();

?>