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
	$module['扩展名']['扩展名列表'] = $filename . '?mode=extensions';
	$module['扩展名']['扩展名分组管理'] = $filename . '?mode=groups';
	$module['扩展名']['禁止扩展名'] = $filename . '?mode=forbidden';
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

if (!intval($board_config['allow_ftp_upload']))
{
	if ( ($board_config['upload_dir'][0] == '/') || ( ($board_config['upload_dir'][0] != '/') && ($board_config['upload_dir'][1] == ':') ) )
	{
		$upload_dir = $board_config['upload_dir'];
	}
	else
	{
		$upload_dir = ROOT_PATH . $board_config['upload_dir'];
	}
}
else
{
	$upload_dir = $board_config['download_path'];
}

require(ROOT_PATH . 'includes/attach/functions_selects.php');
require(ROOT_PATH . 'includes/attach/functions_admin.php');

$types_download = array(INLINE_LINK, PHYSICAL_LINK);
$modes_download = array('inline', 'physical');
$types_category = array(IMAGE_CAT, STREAM_CAT, SWF_CAT);
$modes_category = array('图片', '流媒体', 'Flash');

$size 			= get_var('size', '');
$mode 			= get_var('mode', '');
$mode 			= htmlspecialchars($mode);
$e_mode 		= get_var('e_mode', '');

$submit 		= (isset($_POST['submit'])) ? TRUE : FALSE;

$board_config 	= array();

$error 			= false;
$error_msg		= '';

$sql = 'SELECT * 
	FROM ' . CONFIG_TABLE;
	 
if (!($result = $db->sql_query($sql)))
{
	trigger_error('Could not query attachment information', E_USER_WARNING);
}

while ($row = $db->sql_fetchrow($result))
{
	$board_config[$row['config_name']] = trim($row['config_value']);
}
$db->sql_freeresult($result);

if ($submit && $mode == 'extensions')
{
	$extension_change_list = get_var('extension_change_list', array(0));
	$extension_explain_list = get_var('extension_explain_list', array(''));
	$group_select_list = get_var('group_select', array(0));

	$extensions = array();

	for ($i = 0; $i < count($extension_change_list); $i++)
	{
		$extensions['_' . $extension_change_list[$i]]['comment'] = $extension_explain_list[$i];
		$extensions['_' . $extension_change_list[$i]]['group_id'] = intval($group_select_list[$i]);
	}

	$sql = 'SELECT *
		FROM ' . EXTENSIONS_TABLE . '
		ORDER BY ext_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t get Extension Informations.', E_USER_WARNING);
	}

	$num_rows = $db->sql_numrows($result);
	$extension_row = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($num_rows > 0)
	{
		for ($i = 0; $i < count($extension_row); $i++)
		{
			if ($extension_row[$i]['comment'] != $extensions['_' . $extension_row[$i]['ext_id']]['comment'] || intval($extension_row[$i]['group_id']) != intval($extensions['_' . $extension_row[$i]['ext_id']]['group_id']))
			{
				$sql_ary = array(
					'comment'		=> (string) $extensions['_' . $extension_row[$i]['ext_id']]['comment'],
					'group_id'		=> (int) $extensions['_' . $extension_row[$i]['ext_id']]['group_id']
				);

				$sql = 'UPDATE ' . EXTENSIONS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE ext_id = ' . (int) $extension_row[$i]['ext_id'];
				
				if (!$db->sql_query($sql))
				{
					trigger_error('Couldn\'t update Extension Informations', E_USER_WARNING);
				}
			}
		}
	}

	$extension_id_list = get_var('extension_id_list', array(0));

	$extension_id_sql = implode(', ', $extension_id_list);

	if ($extension_id_sql != '')
	{
		$sql = 'DELETE 
			FROM ' . EXTENSIONS_TABLE . ' 
			WHERE ext_id IN (' . $extension_id_sql . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not delete Extensions', E_USER_WARNING);
		}
	}
	$extension 				= get_var('add_extension', '');
	$extension_explain 		= get_var('add_extension_explain', '');
	$extension_group 		= get_var('add_group_select', 0);
	$add 					= ( isset($_POST['add_extension_check']) ) ? TRUE : FALSE;

	if ($extension != '' && $add)
	{
		$template->assign_vars(array(
			'ADD_EXTENSION'			=> $extension,
			'ADD_EXTENSION_EXPLAIN'	=> $extension_explain)
		);
	
		if (!$error)
		{
			$sql = 'SELECT extension 
				FROM ' . EXTENSIONS_TABLE;
	
			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not query Extensions', E_USER_WARNING);
			}
			
			$row = $db->sql_fetchrowset($result);
			$num_rows = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			if ($num_rows > 0)
			{
				for ($i = 0; $i < $num_rows; $i++)
				{
					if (strtolower(trim($row[$i]['extension'])) == strtolower(trim($extension)))
					{
						$error = TRUE;
						if( isset($error_msg) )
						{
							$error_msg .= '<br />';
						}
						$error_msg .= '扩展名' . strtolower(trim($extension)) . '已经存在';
					}
				}
			}

			if (!$error)
			{
				$sql = 'SELECT extension 
					FROM ' . FORBIDDEN_EXTENSIONS_TABLE;
	
				if (!($result = $db->sql_query($sql)))
				{
					trigger_error('Could not query Extensions', E_USER_WARNING);
				}
			
				$row = $db->sql_fetchrowset($result);
				$num_rows = $db->sql_numrows($result);
				$db->sql_freeresult($result);

				if ($num_rows > 0)
				{
					for ($i = 0; $i < $num_rows; $i++)
					{
						if (strtolower(trim($row[$i]['extension'])) == strtolower(trim($extension)))
						{
							$error = TRUE;
							if( isset($error_msg) )
							{
								$error_msg .= '<br />';
							}
							$error_msg .= '扩展名 ' . strtolower(trim($extension)) . ' 是禁止上传的，你没有将它加入已允许的扩展名群组里';
						}
					}
				}
		
			}

			if (!$error)
			{
				$sql_ary = array(
					'group_id'		=> (int) $extension_group,
					'extension'		=> (string) strtolower($extension),
					'comment'		=> (string) $extension_explain
				);

				$sql = 'INSERT INTO ' . EXTENSIONS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
	
				if (!$db->sql_query($sql))
				{
					trigger_error('Could not add Extension', E_USER_WARNING);
				}

			}
		}
	}

	if (!$error)
	{
		$message = '附件设定更新完毕<br />点击 <a href="' . append_sid('admin_extensions.php?mode=extensions') . '">这里</a> 返回扩展名列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

		trigger_error($message);
	}
}

if ($mode == 'extensions')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_extensions.tpl')
	);

	$template->assign_vars(array(
		'S_CANCEL_ACTION'			=> append_sid('admin_extensions.php?mode=extensions'),
		'S_ATTACH_ACTION'			=> append_sid('admin_extensions.php?mode=extensions'))
	);

	if ($submit)
	{
		$template->assign_vars(array(
			'S_ADD_GROUP_SELECT' => group_select('add_group_select', $extension_group))
		);
	}
	else
	{
		$template->assign_vars(array(
			'S_ADD_GROUP_SELECT' => group_select('add_group_select'))
		);
	}

	$sql = 'SELECT * 
		FROM ' . EXTENSIONS_TABLE . '
		ORDER BY group_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t get Extension informations', E_USER_WARNING);
	}

	$extension_row = $db->sql_fetchrowset($result);
	$num_extension_row = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_extension_row > 0)
	{
		$extension_row = sort_multi_array($extension_row, 'extension', 'ASC');
		
		for ($i = 0; $i < $num_extension_row; $i++)
		{
			if ($submit)
			{
				$template->assign_block_vars('extension_row', array(
					'EXT_ID'			=> $extension_row[$i]['ext_id'],
					'EXTENSION'			=> $extension_row[$i]['extension'],
					'EXTENSION_EXPLAIN'	=> $extension_explain_list[$i], 
					'S_GROUP_SELECT'	=> group_select('group_select[]', $group_select_list[$i]))
				);
			}
			else
			{
				$template->assign_block_vars('extension_row', array(
					'EXT_ID'			=> $extension_row[$i]['ext_id'],
					'EXTENSION'			=> $extension_row[$i]['extension'],
					'EXTENSION_EXPLAIN'	=> $extension_row[$i]['comment'],
					'S_GROUP_SELECT'	=> group_select('group_select[]', $extension_row[$i]['group_id']))
				);
			}
		}
	}

}

if ($submit && $mode == 'groups')
{
	$group_change_list 		= get_var('group_change_list', array(0));
	$extension_group_list 	= get_var('extension_group_list', array(''));
	$group_allowed_list 	= get_var('allowed_list', array(0));
	$download_mode_list 	= get_var('download_mode_list', array(0));
	$category_list 			= get_var('category_list', array(0));
	$upload_icon_list 		= get_var('upload_icon_list', array(''));
	$filesize_list 			= get_var('max_filesize_list', array(0));
	$size_select_list 		= get_var('size_select_list', array(''));

	$allowed_list = array();

	for ($i = 0; $i < count($group_allowed_list); $i++)
	{
		for ($j = 0; $j < count($group_change_list); $j++)
		{
			if ($group_allowed_list[$i] == $group_change_list[$j])
			{
				$allowed_list[$j] = 1;
			}
		}
	}

	for ($i = 0; $i < count($group_change_list); $i++)
	{
		$allowed = (isset($allowed_list[$i])) ? 1 : 0;
		
		$filesize_list[$i] = ($size_select_list[$i] == 'kb') ? round($filesize_list[$i] * 1024) : ( ($size_select_list[$i] == 'mb') ? round($filesize_list[$i] * 1048576) : $filesize_list[$i] );

		$sql_ary = array(
			'group_name'		=> (string) $extension_group_list[$i],
			'cat_id'			=> (int) $category_list[$i],
			'allow_group'		=> (int) $allowed,
			'download_mode'		=> (int) $download_mode_list[$i],
			'upload_icon'		=> (string) $upload_icon_list[$i],
			'max_filesize'		=> (int) $filesize_list[$i]
		);

		$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE group_id = ' . (int) $group_change_list[$i];
		
		if (!($db->sql_query($sql)))
		{
			trigger_error('Couldn\'t update Extension Groups Informations', E_USER_WARNING);
		}
	}
	$group_id_list = get_var('group_id_list', array(0));

	$group_id_sql = implode(', ', $group_id_list);

	if ($group_id_sql != '')
	{
		$sql = 'DELETE 
			FROM ' . EXTENSION_GROUPS_TABLE . ' 
			WHERE group_id IN (' . $group_id_sql . ')';

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not delete Extension Groups', E_USER_WARNING);
		}

		$sql = 'UPDATE ' . EXTENSIONS_TABLE . '
			SET group_id = 0
			WHERE group_id IN (' . $group_id_sql . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not assign Extensions to Pending Group.', E_USER_WARNING);
		}
	}

	$extension_group = get_var('add_extension_group', '');
	$download_mode = get_var('add_download_mode', 0);
	$cat_id = get_var('add_category', 0);
	$upload_icon = get_var('add_upload_icon', '');
	$filesize = get_var('add_max_filesize', 0);
	$size_select = get_var('add_size_select', '');

	$is_allowed = (isset($_POST['add_allowed'])) ? 1 : 0;
	$add = ( isset($_POST['add_extension_group_check']) ) ? TRUE : FALSE;

	if ($extension_group != '' && $add)
	{
		$sql = 'SELECT group_name 
			FROM ' . EXTENSION_GROUPS_TABLE;
	
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not query Extension Groups Table', E_USER_WARNING);
		}
			
		$row = $db->sql_fetchrowset($result);
		$num_rows = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ($num_rows > 0)
		{
			for ($i = 0; $i < $num_rows; $i++)
			{
				if ($row[$i]['group_name'] == $extension_group)
				{
					$error = TRUE;
					if( isset($error_msg) )
					{
						$error_msg .= '<br />';
					}
					$error_msg .= '扩展名小组 ' . $extension_group . '已经存在';
				}
			}
		}
			
		if (!$error)
		{
			$filesize = ($size_select == 'kb') ? round($filesize * 1024) : ( ($size_select == 'mb') ? round($filesize * 1048576) : $filesize );
		
			$sql_ary = array(
				'group_name'		=> (string) $extension_group,
				'cat_id'			=> (int) $cat_id,
				'allow_group'		=> (int) $is_allowed,
				'download_mode'		=> (int) $download_mode,
				'upload_icon'		=> (string) $upload_icon,
				'max_filesize'		=> (int) $filesize,
				'forum_permissions'	=> ''
			);

			$sql = 'INSERT INTO ' . EXTENSION_GROUPS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
	
			if (!($db->sql_query($sql)))
			{
				trigger_error('Could not add Extension Group', E_USER_WARNING);
			}
		}
	}

	if (!$error)
	{
		$message = '成功更新！<br />点击 <a href="' . append_sid('admin_extensions.php?mode=groups') . '">这里</a> 返回扩展名小组<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
		trigger_error($message);
	}
}

if ($mode == 'groups')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_extension_groups.tpl')
	);

	if (!$size && !$submit)
	{
		$max_add_filesize = $board_config['max_filesize'];
		
		$size = ($max_add_filesize >= 1048576) ? 'mb' : ( ($max_add_filesize >= 1024) ? 'kb' : 'b' );
	} 

	if ($max_add_filesize >= 1048576)
	{
		$max_add_filesize = round($max_add_filesize / 1048576 * 100) / 100;
	}
	else if ( $max_add_filesize >= 1024)
	{
		$max_add_filesize = round($max_add_filesize / 1024 * 100) / 100;
	}

	$viewgroup = get_var(POST_GROUPS_URL, 0);

	$template->assign_vars(array(
		'ADD_GROUP_NAME'				=> ($submit) ? $extension_group : '',
		'MAX_FILESIZE'					=> $max_add_filesize,
		'S_FILESIZE'					=> size_select('add_size_select', $size),
		'S_ADD_DOWNLOAD_MODE'			=> download_select('add_download_mode'),
		'S_SELECT_CAT'					=> category_select('add_category'),
		'S_CANCEL_ACTION'				=> append_sid('admin_extensions.php?mode=groups'),
		'S_ATTACH_ACTION'				=> append_sid('admin_extensions.php?mode=groups'))
	);

	$sql = 'SELECT * 
		FROM ' . EXTENSION_GROUPS_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Couldn\'t get Extension Group informations', E_USER_WARNING);
	}

	$extension_group = $db->sql_fetchrowset($result);
	$num_extension_group = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_extension_group; $i++)
	{
		if (!$extension_group[$i]['max_filesize'])
		{
			$extension_group[$i]['max_filesize'] = $board_config['max_filesize'];
		}

		$size_format = ($extension_group[$i]['max_filesize'] >= 1048576) ? 'mb' : ( ($extension_group[$i]['max_filesize'] >= 1024) ? 'kb' : 'b' );

		if ($extension_group[$i]['max_filesize'] >= 1048576)
		{
			$extension_group[$i]['max_filesize'] = round($extension_group[$i]['max_filesize'] / 1048576 * 100) / 100;
		}
		else if ($extension_group[$i]['max_filesize'] >= 1024)
		{
			$extension_group[$i]['max_filesize'] = round($extension_group[$i]['max_filesize'] / 1024 * 100) / 100;
		}

		$s_allowed = ($extension_group[$i]['allow_group'] == 1) ? 'checked="checked"' : '';
			
		$template->assign_block_vars('grouprow', array(
			'GROUP_ID'			=> $extension_group[$i]['group_id'],
			'EXTENSION_GROUP'	=> $extension_group[$i]['group_name'],
			'UPLOAD_ICON'		=> $extension_group[$i]['upload_icon'],

			'S_ALLOW_SELECTED'	=> $s_allowed,
			'S_SELECT_CAT'		=> category_select('category_list[]', $extension_group[$i]['group_id']),
			'S_DOWNLOAD_MODE'	=> download_select('download_mode_list[]', $extension_group[$i]['group_id']),
			'S_FILESIZE'		=> size_select('size_select_list[]', $size_format),
				
			'MAX_FILESIZE'		=> $extension_group[$i]['max_filesize'],
			'CAT_BOX'			=> ($viewgroup == $extension_group[$i]['group_id']) ? '+' : '-',
			'U_VIEWGROUP'		=> ($viewgroup == $extension_group[$i]['group_id']) ? append_sid('admin_extensions.php?mode=groups') : append_sid('admin_extensions.php?mode=groups&' . POST_GROUPS_URL . '=' . $extension_group[$i]['group_id']),
			'U_FORUM_PERMISSIONS'	=> append_sid('admin_extensions.php?mode=$mode&amp;e_mode=perm&amp;e_group=' . $extension_group[$i]['group_id']))
		);

		if ($viewgroup && $viewgroup == $extension_group[$i]['group_id'])
		{
			$sql = 'SELECT comment, extension 
				FROM ' . EXTENSIONS_TABLE . '
				WHERE group_id = ' . (int) $viewgroup;

			if (!$result = $db->sql_query($sql))
			{
				trigger_error('Couldn\'t get Extension informations', E_USER_WARNING);
			}

			$extension = $db->sql_fetchrowset($result);
			$num_extension = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			for ($j = 0; $j < $num_extension; $j++)
			{
				$template->assign_block_vars('grouprow.extensionrow', array(
					'EXPLANATION'	=> $extension[$j]['comment'],
					'EXTENSION'		=> $extension[$j]['extension'])
				);
			}
		}
	}
}

if ($submit && $mode == 'forbidden')
{
	$extension = get_var('extension_id_list', array(0));

	$extension_id_sql = implode(', ', $extension);

	if ($extension_id_sql != '')
	{
		$sql = 'DELETE 
			FROM ' . FORBIDDEN_EXTENSIONS_TABLE . ' 
			WHERE ext_id IN (' . $extension_id_sql . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not delete forbidden extensions', E_USER_WARNING);
		}
	}
		
	$extension = get_var('add_extension', '');
	$add = (isset($_POST['add_extension_check'])) ? TRUE : FALSE;
		
	if ($extension != '' && $add)
	{
		$sql = 'SELECT extension 
			FROM ' . FORBIDDEN_EXTENSIONS_TABLE;

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not query forbidden extensions', E_USER_WARNING);
		}

		$row = $db->sql_fetchrowset($result);
		$num_rows = $db->sql_numrows($result);	
		$db->sql_freeresult($result);
	
		if ($num_rows > 0)
		{
			for ($i = 0; $i < $num_rows; $i++)
			{
				if ($row[$i]['extension'] == $extension)
				{
					$error = TRUE;
					if (isset($error_msg))
					{
						$error_msg .= '<br />';
					}
					$error_msg .= '你要禁止的扩展名' . $extension . '已经存在';
				}
			}
		}

		if (!$error)
		{
			$sql = 'SELECT extension 
				FROM ' . EXTENSIONS_TABLE;

			if (!($result = $db->sql_query($sql)))
			{
				trigger_error('Could not query extensions', E_USER_WARNING);
			}

			$row = $db->sql_fetchrowset($result);
			$num_rows = $db->sql_numrows($result);	
			$db->sql_freeresult($result);
	
			if ($num_rows > 0)
			{
				for ($i = 0; $i < $num_rows; $i++)
				{
					if (strtolower(trim($row[$i]['extension'])) == strtolower(trim($extension)))
					{
						$error = TRUE;
						if( isset($error_msg) )
						{
							$error_msg .= '<br />';
						}
						$error_msg .= '扩展名 ' . $extension . ' 是已定义在你已允许的扩展名，在你在这里加入它之前请先删除';
					}
				}
			}
		}

		if (!$error)
		{
			$sql = 'INSERT INTO ' . FORBIDDEN_EXTENSIONS_TABLE . " (extension)
				VALUES ('" . $db->sql_escape(strtolower($extension)) . "')";

			if (!($db->sql_query($sql)))
			{
				trigger_error('Could not add forbidden extension', E_USER_WARNING);
			}
		
		}
	}

	if (!$error)
	{
		$message = '更新完成<br />点击 <a href="' . append_sid('admin_extensions.php?mode=forbidden') . '">这里</a> 返回禁止的扩展名列表<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

		trigger_error($message);
	}

}

if ($mode == 'forbidden')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_forbidden_extensions.tpl')
	);

	$template->assign_vars(array(
		'S_ATTACH_ACTION'		=> append_sid('admin_extensions.php' . '?mode=forbidden'))
	);

	$sql = 'SELECT *
		FROM ' . FORBIDDEN_EXTENSIONS_TABLE . '
		ORDER BY extension';
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get forbidden extension informations', E_USER_WARNING);
	}

	$extensionrow = $db->sql_fetchrowset($result);
	$num_extensionrow = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_extensionrow > 0)
	{
		for ($i = 0; $i < $num_extensionrow; $i++)
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
			$template->assign_block_vars('extensionrow', array(
				'ROW_CLASS'			=> $row_class,
				'EXTENSION_ID'		=> $extensionrow[$i]['ext_id'],
				'EXTENSION_NAME'	=> $extensionrow[$i]['extension'])
			);
		}
	}
}

if ($e_mode == 'perm')
{
	$group		 	= get_var('e_group', 0);

	$add_forum 		= (isset($_POST['add_forum'])) ? TRUE : FALSE;
	$delete_forum 	= (isset($_POST['del_forum'])) ? TRUE : FALSE;

	if (isset($_POST['close_perm']))
	{
		$e_mode 	= '';
	}
}
else
{
	$add_forum 		= false;
	$delete_forum 	= false;
}

if ($add_forum && $e_mode == 'perm' && $group)
{
	$add_forums_list = get_var('entries', array(0));
	$add_all_forums = FALSE;

	for ($i = 0; $i < count($add_forums_list); $i++)
	{
		if ($add_forums_list[$i] == GPERM_ALL)
		{
			$add_all_forums = TRUE;
		}
	}

	if ($add_all_forums)
	{
		$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . " 
			SET forum_permissions = '' W
			HERE group_id = " . (int) $group;
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not update Permissions', E_USER_WARNING);
		}
	}

	if (!$add_all_forums)
	{
		$sql = 'SELECT forum_permissions
			FROM ' . EXTENSION_GROUPS_TABLE . '
			WHERE group_id = ' . intval($group) . '
			LIMIT 1';
	
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not get Group Permissions from ' . EXTENSION_GROUPS_TABLE, E_USER_WARNING);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (trim($row['forum_permissions']) == '')
		{
			$auth_p = array();
		}
		else
		{
			$auth_p = auth_unpack($row['forum_permissions']);
		}

		for ($i = 0; $i < count($add_forums_list); $i++)
		{
			if (!in_array($add_forums_list[$i], $auth_p))
			{
				$auth_p[] = $add_forums_list[$i];
			}
		}

		$auth_bitstream = auth_pack($auth_p);

		$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . " SET forum_permissions = '" . $db->sql_escape($auth_bitstream) . "' WHERE group_id = " . (int) $group;

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not update Permissions', E_USER_WARNING);
		}
	}

}

if ($delete_forum && $e_mode == 'perm' && $group)
{
	$delete_forums_list = get_var('entries', array(0));

	$sql = 'SELECT forum_permissions
		FROM ' . EXTENSION_GROUPS_TABLE . '
		WHERE group_id = ' . intval($group) . '
		LIMIT 1';
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get Group Permissions from ' . EXTENSION_GROUPS_TABLE, E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$auth_p2 = auth_unpack(trim($row['forum_permissions']));
	$auth_p = array();

	for ($i = 0; $i < count($auth_p2); $i++)
	{
		if (!in_array($auth_p2[$i], $delete_forums_list))
		{
			$auth_p[] = $auth_p2[$i];
		}
	}

	$auth_bitstream = (count($auth_p) > 0) ? auth_pack($auth_p) : '';

	$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . " SET forum_permissions = '" . $db->sql_escape($auth_bitstream) . "' WHERE group_id = " . (int) $group;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not update Permissions', E_USER_WARNING);
	}
}

if ($e_mode == 'perm' && $group)
{
	$template->set_filenames(array(
		'perm_box' => 'admin/extension_groups_permissions.tpl')
	);
	
	$sql = 'SELECT group_name, forum_permissions
		FROM ' . EXTENSION_GROUPS_TABLE . '
		WHERE group_id = ' . intval($group) . '
		LIMIT 1';
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get Group Name from ' . EXTENSION_GROUPS_TABLE, E_USER_WARNING);
	}

	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$group_name = $row['group_name'];
	$allowed_forums = trim($row['forum_permissions']);

	$forum_perm = array();

	if ($allowed_forums == '')
	{
		$forum_perm[0]['forum_id'] = 0;
		$forum_perm[0]['forum_name'] = '所有论坛';
	}
	else
	{
		$forum_p = array();
		$act_id = 0;
		$forum_p = auth_unpack($allowed_forums);
	
		$sql = 'SELECT forum_id, forum_name 
			FROM ' . FORUMS_TABLE . ' 
			WHERE forum_id IN (' . implode(', ', $forum_p) . ')';
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not get Forum Names', E_USER_WARNING);
		}

		while ($row = $db->sql_fetchrow($result))
		{
			$forum_perm[$act_id]['forum_id'] = $row['forum_id'];
			$forum_perm[$act_id]['forum_name'] = $row['forum_name'];
			$act_id++;
		}
	}

	for ($i = 0; $i < count($forum_perm); $i++)
	{
		$template->assign_block_vars('allow_option_values', array(
			'VALUE'		=> $forum_perm[$i]['forum_id'],
			'OPTION'	=> $forum_perm[$i]['forum_name'])
		);
	}

	$template->assign_vars(array(
		'L_GROUP_PERMISSIONS_TITLE'		=> trim($group_name) . '的扩展名小组权限',
		'A_PERM_ACTION'					=> append_sid('admin_extensions.php?mode=groups&amp;e_mode=perm&amp;e_group=' . $group))
	);

	$forum_option_values = array(GPERM_ALL => '所有论坛');

	$sql = 'SELECT forum_id, forum_name 
		FROM ' . FORUMS_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get Forums', E_USER_WARNING);
	}

	while ($row = $db->sql_fetchrow($result))
	{
		$forum_option_values[intval($row['forum_id'])] = $row['forum_name'];
	}
	$db->sql_freeresult($result);

	foreach ($forum_option_values as $value => $option)
	{
		$template->assign_block_vars('forum_option_values', array(
			'VALUE'		=> $value,
			'OPTION'	=> $option)
		);
	}

	$template->assign_var_from_handle('GROUP_PERMISSIONS_BOX', 'perm_box');

	$empty_perm_forums = array();


	$sql = 'SELECT forum_id, forum_name 
		FROM ' . FORUMS_TABLE . ' 
		WHERE auth_attachments < ' . AUTH_ADMIN;

	if (!($f_result = $db->sql_query($sql))) 
	{ 
		trigger_error('Could not get Forums.', E_USER_WARNING); 
	} 
	
	while ($row = $db->sql_fetchrow($f_result))
	{
		$forum_id = $row['forum_id'];

		$sql = 'SELECT forum_permissions
			FROM ' . EXTENSION_GROUPS_TABLE . ' 
			WHERE allow_group = 1 
			ORDER BY group_name ASC';

		if ( !($result = $db->sql_query($sql)) ) 
		{ 
			trigger_error('Could not query Extension Groups.', E_USER_WARNING);
		} 

		$rows = $db->sql_fetchrowset($result); 
		$num_rows = $db->sql_numrows($result); 
		$db->sql_freeresult($result);

		$found_forum = FALSE;

		for ($i = 0; $i < $num_rows; $i++)
		{
			$allowed_forums = auth_unpack(trim($rows[$i]['forum_permissions']));
			if (in_array($forum_id, $allowed_forums) || trim($rows[$i]['forum_permissions']) == '')
			{
				$found_forum = TRUE;
				break;
			}
		}

		if (!$found_forum)
		{
			$empty_perm_forums[$forum_id] = $row['forum_name'];
		}
	}
	$db->sql_freeresult($f_result);

	$message = '';
	
	foreach ($empty_perm_forums as $forum_id => $forum_name)
	{
		$message .= ( $message == '' ) ? $forum_name : '<br />' . $forum_name;
	}

	if (count($empty_perm_forums) > 0)
	{
		error_box('PERM_ERROR_BOX', '注意:<br />使用在下面列表的论坛，使用者通常是被允许添加附件，但是自从没有扩展名群组在那里被允许去附加的，你的使用者是无法附加任何文件的。如果他们曾经尝试附加文件，他们将会接收到错误讯息。可能你想要设定权限\'可附加的文件\' 来管理在这个论坛的附件。<br />' . $message);
	}
}

if ($error)
{
	error_box('ERROR_BOX', $error_msg);
}

$template->pparse('body');

page_footer();

?>