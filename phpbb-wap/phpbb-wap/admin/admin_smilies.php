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
	$module['论坛']['表情'] = $filename;

	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');

$cancel = ( isset($_POST['cancel']) || isset($_POST['cancel']) ) ? true : false;
$no_page_header = $cancel;

if ((!empty($_GET['export_pack']) && $_GET['export_pack'] == 'send') || (!empty($_GET['export_pack']) && $_GET['export_pack'] == 'send'))
{
	$no_page_header = true;
}

require('./pagestart.php');

if ($cancel)
{
	redirect('admin/' . append_sid('admin_smilies.php', true));
}

if( isset($_POST['mode']) || isset($_GET['mode']) )
{
	$mode = ( isset($_POST['mode']) ) ? $_POST['mode'] : $_GET['mode'];
	$mode = htmlspecialchars($mode);
}
else
{
	$mode = '';
}

$start = get_pagination_start($board_config['topics_per_page']);

$delimeter  = '=+:';

$dir = @opendir(ROOT_PATH . $board_config['smilies_path']);

while($file = @readdir($dir))
{
	if( !@is_dir(phpbb_realpath(ROOT_PATH . $board_config['smilies_path'] . '/' . $file)) )
	{
		$img_size = @getimagesize(ROOT_PATH . $board_config['smilies_path'] . '/' . $file);

		if( $img_size[0] && $img_size[1] )
		{
			$smiley_images[] = $file;
		}
		else if(preg_match('/\.pak$/', $file))
		{	
			$smiley_paks[] = $file;
		}
	}
}

@closedir($dir);

if( isset($_GET['import_pack']) || isset($_POST['import_pack']) )
{

	$smile_pak 			= ( isset($_POST['smile_pak']) ) ? $_POST['smile_pak'] : '';
	$clear_current 		= ( isset($_POST['clear_current']) ) ? $_POST['clear_current'] : '';
	$replace_existing 	= ( isset($_POST['replace']) ) ? $_POST['replace'] : '';

	if ( !empty($smile_pak) )
	{

		if( !empty($clear_current)  )
		{
			$sql = 'DELETE 
				FROM ' . SMILIES_TABLE;
			if( !$db->sql_query($sql) )
			{
				trigger_error('Couldn\'t delete current smilies', E_USER_WARNING);
			}
		}
		else
		{
			$sql = 'SELECT code 
				FROM '. SMILIES_TABLE;
			if( !$result = $db->sql_query($sql) )
			{
				trigger_error('Couldn\'t get current smilies', E_USER_WARNING);
			}

			$cur_smilies = $db->sql_fetchrowset($result);

			for( $i = 0; $i < count($cur_smilies); $i++ )
			{
				$k = $cur_smilies[$i]['code'];
				$smiles[$k] = 1;
			}
		}

		$fcontents = @file(ROOT_PATH . $board_config['smilies_path'] . '/'. $smile_pak);

		if( empty($fcontents) )
		{
			trigger_error('Couldn\'t read smiley pak file', E_USER_WARNING);
		}

		for( $i = 0; $i < count($fcontents); $i++ )
		{
			$smile_data = explode($delimeter, trim(addslashes($fcontents[$i])));

			for( $j = 2; $j < count($smile_data); $j++)
			{

				$smile_data[$j] = str_replace('<', '&lt;', $smile_data[$j]);
				$smile_data[$j] = str_replace('>', '&gt;', $smile_data[$j]);
				$k = $smile_data[$j];

				if( $smiles[$k] == 1 )
				{
					if( !empty($replace_existing) )
					{
						$sql = 'UPDATE ' . SMILIES_TABLE . " 
							SET smile_url = '" . $db->sql_escape($smile_data[0]) . "', emoticon = '" . $db->sql_escape($smile_data[1]) . "' 
							WHERE code = '" . $db->sql_escape($smile_data[$j]) . "'";
					}
					else
					{
						$sql = '';
					}
				}
				else
				{
					$sql = 'INSERT INTO ' . SMILIES_TABLE . " (code, smile_url, emoticon)
						VALUES('" . $db->sql_escape($smile_data[$j]) . "', '" . $db->sql_escape($smile_data[0]) . "', '" . $db->sql_escape($smile_data[1]) . "')";
				}

				if( $sql != '' )
				{
					if (!$db->sql_query($sql))
					{
						trigger_error('Couldn\'t update smilies!', E_USER_WARNING);
					}
				}
			}
		}

		$message = '表情符号包已经成功导入<br />点击 <a href="' . append_sid('admin_smilies.php') . '">这里</a> 返回表情管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

		trigger_error($message);
		
	}
	else
	{

		$smile_paks_select = '<select name="smile_pak"><option value="">选择表情包</option>';
		if (isset($smiley_paks))
		{
			if (is_array($smiley_paks))
			{
				foreach($smiley_paks as $key => $value)
				{
					if ( !empty($value) ) 
					{
						$smile_paks_select .= "<option>" . $value . "</option>";
					}
				}
			}
		}
		$smile_paks_select .= "</select>";

		$hidden_vars = "<input type='hidden' name='mode' value='import'>";	

		$template->set_filenames(array(
			'body' => 'admin/smile_import_body.tpl')
		);

		$template->assign_vars(array(
			'SMILEY_PATH'		=> $board_config['smilies_path'],
			'U_SMILEY_ADMIN'	=> append_sid('admin_smilies.php'),
			'S_SMILEY_ACTION' 	=> append_sid('admin_smilies.php'),
			'S_SMILE_SELECT' 	=> $smile_paks_select,
			'S_HIDDEN_FIELDS' 	=> $hidden_vars)
		);

		$template->pparse('body');
	}
}
else if( isset($_POST['export_pack']) || isset($_GET['export_pack']) )
{

	if ( isset($_GET['export_pack']) )
	{	
		$sql = 'SELECT * 
			FROM ' . SMILIES_TABLE;
		if( !$result = $db->sql_query($sql) )
		{
			trigger_error('Could not get smiley list', E_USER_WARNING);
		}

		$resultset = $db->sql_fetchrowset($result);

		$smile_pak = '';
		for($i = 0; $i < count($resultset); $i++ )
		{
			$smile_pak .= $resultset[$i]['smile_url'] . $delimeter;
			$smile_pak .= $resultset[$i]['emoticon'] . $delimeter;
			$smile_pak .= $resultset[$i]['code'] . "\n";
		}

		header('Content-Type: text/x-delimtext; name="smiles.pak"');
		header('Content-disposition: attachment; filename=smiles.pak');

		echo $smile_pak;

		exit;
	}

	$message = '正在导出...<br />点击 <a href="' . append_sid('admin_smilies.php?export_pack', true) . '">这里</a> 下载文件<br />点击 <a href="' . append_sid('admin_smilies.php') . '">这里</a> 返回表情列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

	trigger_error($message);

}
else if( isset($_POST['add']) || isset($_GET['add']) )
{

	$template->set_filenames(array(
		'body' => 'admin/smile_edit_body.tpl')
	);

	$filename_list = '';
	for( $i = 0; $i < count($smiley_images); $i++ )
	{
		$filename_list .= '<option value="' . $smiley_images[$i] . '">' . $smiley_images[$i] . '</option>';
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="savenew" />';

	$template->assign_vars(array(
		'SMILEY_IMG' 			=> ROOT_PATH . $board_config['smilies_path'] . '/' . $smiley_images[0], 
		'S_SMILEY_ACTION' 		=> append_sid('admin_smilies.php'), 
		'S_HIDDEN_FIELDS' 		=> $s_hidden_fields, 
		'S_FILENAME_OPTIONS'	=> $filename_list, 
		'S_SMILEY_BASEDIR' 		=> ROOT_PATH . $board_config['smilies_path'])
	);

	$template->pparse('body');
}
else if ( $mode != '' )
{
	switch( $mode )
	{
		case 'delete':

			$smiley_id = ( !empty($_POST['id']) ) ? $_POST['id'] : $_GET['id'];
			$smiley_id = intval($smiley_id);

			$confirm = isset($_POST['confirm']);

			if( $confirm )
			{
				$sql = 'DELETE FROM ' . SMILIES_TABLE . '
					WHERE smilies_id = ' . $smiley_id;
				if (!$db->sql_query($sql))
				{
					trigger_error('Couldn\'t delete smiley', E_USER_WARNING);
				}
				$message = '表情符号已经成功删除<br />点击 <a href="' . append_sid('admin_smilies.php') . '">这里</a> 返回表情管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
				
				trigger_error($message);
			}
			else
			{
				$template->set_filenames(array(
					'body' => 'admin/confirm_body.tpl')
				);

				$hidden_fields = '<input type="hidden" name="mode" value="delete" /><input type="hidden" name="id" value="' . $smiley_id . '" />';

				$template->assign_vars(array(
					'MESSAGE_TITLE' 	=> '删除确认',
					'MESSAGE_TEXT' 		=> '请确认是否删除这个表情',
					'L_YES' 			=> '是',
					'L_NO' 				=> '否',
					'S_CONFIRM_ACTION' 	=> append_sid('admin_smilies.php'),
					'S_HIDDEN_FIELDS' 	=> $hidden_fields)
				);
				$template->pparse('body');
			}
			break;

		case 'edit':

			$smiley_id = ( !empty($_POST['id']) ) ? $_POST['id'] : $_GET['id'];
			$smiley_id = intval($smiley_id);

			$sql = 'SELECT *
				FROM ' . SMILIES_TABLE . '
				WHERE smilies_id = ' . $smiley_id;
			$result = $db->sql_query($sql);
			if( !$result )
			{
				trigger_error('Could not obtain emoticon information', E_USER_WARNING);
			}
			$smile_data = $db->sql_fetchrow($result);

			$filename_list = '';
			for( $i = 0; $i < count($smiley_images); $i++ )
			{
				if( $smiley_images[$i] == $smile_data['smile_url'] )
				{
					$smiley_selected = 'selected="selected"';
					$smiley_edit_img = $smiley_images[$i];
				}
				else
				{
					$smiley_selected = '';
				}

				$filename_list .= '<option value="' . $smiley_images[$i] . '"' . $smiley_selected . '>' . $smiley_images[$i] . '</option>';
			}

			$template->set_filenames(array(
				'body' => 'admin/smile_edit_body.tpl')
			);

			$s_hidden_fields = '<input type="hidden" name="mode" value="save" /><input type="hidden" name="smile_id" value="' . $smile_data['smilies_id'] . '" />';

			$template->assign_vars(array(
				'SMILEY_CODE' 		=> $smile_data['code'],
				'SMILEY_EMOTICON' 	=> $smile_data['emoticon'],
				'SMILEY_PATH'		=> $board_config['smilies_path'],

				'SMILEY_IMG' 		=> ROOT_PATH . $board_config['smilies_path'] . '/' . $smiley_edit_img, 
				
				'U_SMILEY_ADMIN'	=> append_sid('admin_smilies.php'),

				'S_SMILEY_ACTION' 	=> append_sid('admin_smilies.php'),
				'S_HIDDEN_FIELDS' 	=> $s_hidden_fields, 
				'S_FILENAME_OPTIONS' 	=> $filename_list, 
				'S_SMILEY_BASEDIR' 	=> ROOT_PATH . $board_config['smilies_path'])
			);

			$template->pparse('body');
			break;

		case 'save':

			$smile_code = ( isset($_POST['smile_code']) ) ? trim($_POST['smile_code']) : '';
			$smile_url = ( isset($_POST['smile_url']) ) ? trim($_POST['smile_url']) : '';
			$smile_url = phpbb_ltrim(basename($smile_url), "'");
			$smile_emotion = ( isset($_POST['smile_emotion']) ) ? htmlspecialchars(trim($_POST['smile_emotion'])) : '';
			$smile_id = ( isset($_POST['smile_id']) ) ? intval($_POST['smile_id']) : 0;
			$smile_code = trim($smile_code);
			$smile_url = trim($smile_url);

			if ($smile_code == '' || $smile_url == '')
			{
				trigger_error('表情代码和图片不能为空', E_USER_ERROR);
			}

			$smile_code = str_replace('<', '&lt;', $smile_code);
			$smile_code = str_replace('>', '&gt;', $smile_code);

			$sql = 'UPDATE ' . SMILIES_TABLE . "
				SET code = '" . $db->sql_escape($smile_code) . "', smile_url = '" . $db->sql_escape($smile_url) . "', emoticon = '" . $db->sql_escape($smile_emotion) . "'
				WHERE smilies_id = $smile_id";
			if( !($result = $db->sql_query($sql)) )
			{
				trigger_error('Couldn\'t update smilies info', E_USER_WARNING);
			}
	
			$message = '表情符号已经成功更新<br />点击 <a href="' . append_sid('admin_smilies.php') . '">这里</a> 返回表情管理页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
				
			trigger_error($message);
			break;

		case 'savenew':

			$smile_code 	= ( isset($_POST['smile_code']) ) ? $_POST['smile_code'] : '';
			$smile_url 		= ( isset($_POST['smile_url']) ) ? $_POST['smile_url'] : '';
			$smile_url 		= phpbb_ltrim(basename($smile_url), "'");
			$smile_emotion 	= ( isset($_POST['smile_emotion']) ) ? htmlspecialchars(trim($_POST['smile_emotion'])) : '';
			$smile_code 	= trim($smile_code);
			$smile_url 		= trim($smile_url);

			if ($smile_code == '' || $smile_url == '')
			{
				trigger_error('代码和图标选项不能为空', E_USER_ERROR);
			}

			$sql = 'INSERT INTO ' . SMILIES_TABLE . " (code, smile_url, emoticon)
				VALUES ('" . $db->sql_escape($smile_code) . "', '" . $db->sql_escape($smile_url) . "', '" . $db->sql_escape($smile_emotion) . "')";
			
			if (!$db->sql_query($sql))
			{
				trigger_error('Couldn\'t insert new smiley', E_USER_WARNING);
			}

			$message = '新增成功！<br />点击 <a href="' . append_sid('admin_smilies.php') . '">这里</a> 返回表情列表页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

			trigger_error($message);
			break;
	}
}
else
{

	$sql = 'SELECT *
		FROM ' . SMILIES_TABLE . "
		LIMIT $start, " . $board_config['topics_per_page'];
	if (!$result = $db->sql_query($sql))
	{
		trigger_error('Couldn\'t obtain smileys from database', E_USER_WARNING);
	}
	$smilies = $db->sql_fetchrowset($result);

	$template->set_filenames(array(
		'body' => 'admin/smile_list_body.tpl')
	);

	$sql = 'SELECT count(smilies_id) AS total FROM ' . SMILIES_TABLE;
	if(!$result = $db->sql_query($sql))
	{
		trigger_error('Could not count smiles', E_USER_WARNING);
	}
	$row = $db->sql_fetchrow($result);
	$total_all_smiles = $row['total'];

	$pagination = ( $total_all_smiles > $board_config['topics_per_page'] ) ? generate_pagination('admin_smilies.php?', $total_all_smiles, $board_config['topics_per_page'], $start) : '';

	$template->assign_vars(array(
		'PAGINATION' 		=> $pagination,
		'S_HIDDEN_FIELDS' 	=> '', 
		'S_SMILEY_ACTION'	=> append_sid('admin_smilies.php'))
	);

	for($i = 0; $i < count($smilies); $i++)
	{

		$smilies[$i]['code'] = str_replace('&lt;', '<', $smilies[$i]['code']);
		$smilies[$i]['code'] = str_replace('&gt;', '>', $smilies[$i]['code']);
		$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

		$template->assign_block_vars('smiles', array(
			'ROW_CLASS' 		=> $row_class,
			'SMILEY_IMG' 		=> ROOT_PATH . $board_config['smilies_path'] . '/' . $smilies[$i]['smile_url'], 
			'CODE' 				=> $smilies[$i]['code'],
			'EMOT' 				=> $smilies[$i]['emoticon'],
			'U_SMILEY_EDIT' 	=> append_sid('admin_smilies.php?mode=edit&amp;id=' . $smilies[$i]['smilies_id']), 
			'U_SMILEY_DELETE' 	=> append_sid('admin_smilies.php?mode=delete&amp;id=' . $smilies[$i]['smilies_id']))
		);
	}

	$template->pparse('body');
}

page_footer();

?>