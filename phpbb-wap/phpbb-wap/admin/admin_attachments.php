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
// 待删除
// 'MAX_FILESIZE_PM'		=> $new_attach['max_filesize_pm'],
// 'MAX_ATTACHMENTS_PM'		=> $new_attach['max_attachments_pm'],

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['附件']['附件配置'] = $filename . '?mode=manage';
	$module['附件']['幽灵文件'] = $filename . '?mode=shadow';
	$module['扩展名']['特殊类别'] = $filename . '?mode=cats';
	$module['附件']['同步附件'] = $filename . '?mode=sync';
	$module['附件']['附件限额'] = $filename . '?mode=quota';
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('pagestart.php');

require(ROOT_PATH . 'includes/functions/admin.php');
require(ROOT_PATH . 'includes/attach/functions_attach.php');
require(ROOT_PATH . 'includes/attach/functions_selects.php');

if ( ($board_config['upload_dir'][0] == '/') || ( ($board_config['upload_dir'][0] != '/') && ($board_config['upload_dir'][1] == ':') ) )
{
		$upload_dir = $board_config['upload_dir'];
}
else
{
		$upload_dir = '../' . $board_config['upload_dir'];
}

require(ROOT_PATH . 'includes/functions/validate.php');
require(ROOT_PATH . 'includes/attach/functions_admin.php');
$error 		= false;
$error_msg	= '';
$mode 		= get_var('mode', '');
$mode 		= htmlspecialchars($mode);
$e_mode 	= get_var('e_mode', '');
$size 		= get_var('size', '');
$quota_size	= get_var('quota_size', '');

$submit 			= (isset($_POST['submit'])) ? true : false;
$check_upload 		= (isset($_POST['settings'])) ? true : false;
$check_image_cat 	= (isset($_POST['cat_settings'])) ? true : false;
$search_imagick 	= (isset($_POST['search_imagick'])) ? true : false;

$sql = 'SELECT * 
	FROM ' . CONFIG_TABLE;
	 
if (!$result = $db->sql_query($sql))
{
	trigger_error('Could not find Attachment Config Table', E_USER_WARNING);
}

while ($row = $db->sql_fetchrow($result))
{
	$config_name = $row['config_name'];
	$config_value = $row['config_value'];

	$new_attach[$config_name] = get_var($config_name, trim($board_config[$config_name]));

	if (!$size && !$submit && $config_name == 'max_filesize')
	{
		$size = ($board_config[$config_name] >= 1048576) ? 'mb' : (($board_config[$config_name] >= 1024) ? 'kb' : 'b');
	} 

	if (!$quota_size && !$submit && $config_name == 'attachment_quota')
	{
		$quota_size = ($board_config[$config_name] >= 1048576) ? 'mb' : (($board_config[$config_name] >= 1024) ? 'kb' : 'b');
	}

	if (!$submit && ($config_name == 'max_filesize' || $config_name == 'attachment_quota'))
	{
		if ($new_attach[$config_name] >= 1048576)
		{
			$new_attach[$config_name] = round($new_attach[$config_name] / 1048576 * 100) / 100;
		}
		else if ($new_attach[$config_name] >= 1024)
		{
			$new_attach[$config_name] = round($new_attach[$config_name] / 1024 * 100) / 100;
		}
	}

	if ($submit && ($mode == 'manage' || $mode == 'cats'))
	{
		if ($config_name == 'max_filesize')
		{
			$old = $new_attach[$config_name];
			$new_attach[$config_name] = ($size == 'kb') ? round($new_attach[$config_name] * 1024) : (($size == 'mb') ? round($new_attach[$config_name] * 1048576) : $new_attach[$config_name]);
		}
		
		if ($config_name == 'attachment_quota')
		{
			$old = $new_attach[$config_name];
			$new_attach[$config_name] = ( $quota_size == 'kb' ) ? round($new_attach[$config_name] * 1024) : ( ($quota_size == 'mb') ? round($new_attach[$config_name] * 1048576) : $new_attach[$config_name] );
		}

		if ($config_name == 'ftp_server' || $config_name == 'ftp_path' || $config_name == 'download_path')
		{
			$value = trim($new_attach[$config_name]);
			if ($value == '')
			{
				$new_attach[$config_name] = '';
			}
			else
			{
				if ($value[strlen($value)-1] == '/')
				{
					$value[strlen($value)-1] = ' ';
				}
			
				$new_attach[$config_name] = trim($value);
			}
		}
		
		if ($config_name == 'max_filesize')
		{
			$old_size = $board_config[$config_name];
			$new_size = $new_attach[$config_name];

			if ($old_size != $new_size)
			{
				$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . '
					SET max_filesize = ' . (int) $new_size . '
					WHERE max_filesize = ' . (int) $old_size;

				if (!($result_2 = $db->sql_query($sql)))
				{
					trigger_error('Could not update Extension Group informations', E_USER_WARNING);
				}
			}

			set_config($config_name, $new_attach[$config_name]);
		}
		else
		{
			set_config($config_name, $new_attach[$config_name]);
		}
	
		if ($config_name == 'max_filesize' || $config_name == 'attachment_quota')
		{
			$new_attach[$config_name] = $old;
		}
	}
}

$cache->clear('global_config');

$select_size_mode 		= size_select('size', $size);
$select_quota_size_mode = size_select('quota_size', $quota_size);

if ($search_imagick)
{
	$imagick = '';
	
	if (preg_match('/convert/', $imagick)) 
	{
		return true;
	} 
	else if ($imagick != 'none') 
	{
		if (!preg_match('/WIN/', PHP_OS)) 
		{
			$retval 	= @exec('whereis convert');
			$paths 		= explode(' ', $retval);

			if (is_array($paths)) 
			{
				for ($i = 0; $i < count($paths); $i++) 
				{
					$path = basename($paths[$i]);

					if ($path == 'convert') 
					{
						$imagick = $paths[$i];
					}
				}
			}
		}
		else if (preg_match('/WIN/', PHP_OS))
		{
			$path = 'c:/imagemagick/convert.exe';

			if (@file_exists(@phpbb_realpath($path)))
			{
				$imagick = $path;
			}
		}
	} 

	if (@file_exists(@phpbb_realpath(trim($imagick))))
	{
		$new_attach['img_imagick'] = trim($imagick);
	}
	else
	{
		$new_attach['img_imagick'] = '';
	}
}

if ($check_upload)
{
	$board_config = array();

	$sql = 'SELECT *
		FROM ' . CONFIG_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not find Attachment Config Table', E_USER_WARNING);
	}

	$row = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$board_config[$row[$i]['config_name']] = trim($row[$i]['config_value']);
	}

	if ($board_config['upload_dir'][0] == '/' || ($board_config['upload_dir'][0] != '/' && $board_config['upload_dir'][1] == ':'))
	{
		$upload_dir = $board_config['upload_dir'];
	}
	else
	{
		$upload_dir = ROOT_PATH . $board_config['upload_dir'];
	}

	if (intval($board_config['allow_ftp_upload']) == 0)
	{
		if (!@file_exists(@phpbb_realpath($upload_dir)))
		{
			$error = true;
			$error_msg = '<p>' . $board_config['upload_dir'] . '目录不存在</p>';
		}
	
		if (!$error && !is_dir($upload_dir))
		{
			$error = true;
			$error_msg = '<p>' . $board_config['upload_dir'] . '不是目录</p>';
		}
	
		if (!$error)
		{
			if ( !($fp = @fopen($upload_dir . '/0_000000.000', 'w')) )
			{
				$error = true;
				$error_msg =  $board_config['upload_dir'] . ' 目录是不可写入的，您将需要建立上传目录并变更属性为 777 (或变更拥有者为您 httpd-服务器的拥有者)，如果您只要完全的 ftp-存取 变更文件夹的 “属性” 为 rwxrwxrwx ';
			}
			else
			{
				@fclose($fp);
				unlink_attach($upload_dir . '/0_000000.000');
			}
		}
	}
	else
	{
		$server = ( empty($board_config['ftp_server']) ) ? 'localhost' : $board_config['ftp_server'];

		$conn_id = @ftp_connect($server);

		if (!$conn_id)
		{
			$error = TRUE;
			$error_msg = '无法链接FTP服务器::' . $server;
		}

		$login_result = @ftp_login($conn_id, $board_config['ftp_user'], $board_config['ftp_pass']);

		if ( (!$login_result) && (!$error) )
		{
			$error = TRUE;
			$error_msg = '<p>无法登入到 FTP 服务器，用户名::' . $board_config['ftp_user'] . '</p>';
		}
		
		if (!@ftp_pasv($conn_id, intval($board_config['ftp_pasv_mode'])))
		{
			$error = TRUE;
			$error_msg = '<p>无法开启或关闭FTP被动模式</p>';
		}

		if (!$error)
		{
			$tmpfname = @tempnam('/tmp', 't0000');

			@unlink($tmpfname);

			$fp = @fopen($tmpfname, 'w');

			@fwrite($fp, 'test');

			@fclose($fp);

			$result = @ftp_chdir($conn_id, $board_config['ftp_path']);

			if (!$result)
			{
				$error = TRUE;
				$error_msg = '<p>无法存取 FTP 文件夹::' . $board_config['ftp_path'] . '</p>';
			}
			else
			{
				$res = @ftp_put($conn_id, 't0000', $tmpfname, FTP_ASCII);
				
				if (!$res)
				{
					$error = TRUE;
					$error_msg = '<p>无法上传文件到 FTP 文件夹::' . $board_config['ftp_path'] . '</p>';
				}
				else
				{
					$res = @ftp_delete($conn_id, 't0000');

					if (!$res)
					{
						$error = TRUE;
						$error_msg = '<p>无法删除在 FTP 文件夹的文件::' . $board_config['ftp_path'] . '</p>';
					}
				}
			}

			@ftp_quit($conn_id);

			@unlink($tmpfname);
		}
	}
	
	if (!$error)
	{
		trigger_error('测试完成，看起来一切正常<br />点击 <a href="' . append_sid('admin_attachments.php?mode=manage') . '">这里</a> 返回附件配置<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板', E_USER_ERROR);
	}
}

if ($submit && $mode == 'manage')
{
	if (!$error)
	{
		trigger_error('附件配置更新完成！<br />点击 <a href="' . append_sid('admin_attachments.php?mode=manage') . '">这里</a> 返回附件功能配置！<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级管理面板首页', E_USER_ERROR);
	}
}

if ($mode == 'manage')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_manage_body.tpl')
	);

	$yes_no_switches = array('disable_mod', 'allow_ftp_upload', 'attachment_topic_review', 'display_order', 'download_mode', 'show_apcp', 'ftp_pasv_mode');

	for ($i = 0; $i < count($yes_no_switches); $i++)
	{
		eval("\$" . $yes_no_switches[$i] . "_yes = ( \$new_attach['" . $yes_no_switches[$i] . "'] != '0' ) ? 'checked=\"checked\"' : '';");
		eval("\$" . $yes_no_switches[$i] . "_no = ( \$new_attach['" . $yes_no_switches[$i] . "'] == '0' ) ? 'checked=\"checked\"' : '';");
	}

	if (!function_exists('ftp_connect'))
	{
		$template->assign_block_vars('switch_no_ftp', array());
	}
	else
	{
		$template->assign_block_vars('switch_ftp', array());
	}

	$template->assign_vars(array(

		'S_ATTACH_ACTION'		=> append_sid('admin_attachments.php?mode=manage'),
		'S_FILESIZE'			=> $select_size_mode,
		'S_FILESIZE_QUOTA'		=> $select_quota_size_mode,
		'S_DEFAULT_UPLOAD_LIMIT'=> default_quota_limit_select('default_upload_quota', intval(trim($new_attach['default_upload_quota']))),

		'UPLOAD_DIR'			=> $new_attach['upload_dir'],
		'ATTACHMENT_IMG_PATH'	=> $new_attach['upload_img'],
		'TOPIC_ICON'			=> $new_attach['topic_icon'],
		'MAX_FILESIZE'			=> $new_attach['max_filesize'],
		'ATTACHMENT_QUOTA'		=> $new_attach['attachment_quota'],
		'MAX_ATTACHMENTS'		=> $new_attach['max_attachments'],
		'FTP_SERVER'			=> $new_attach['ftp_server'],
		'FTP_PATH'				=> $new_attach['ftp_path'],
		'FTP_USER'				=> $new_attach['ftp_user'],
		'FTP_PASS'				=> $new_attach['ftp_pass'],
		'POINTS_NAME'			=> $new_attach['points_name'],
		'DOWNLOAD_CUT_POINTS'	=> $new_attach['download_cut_points'],
		'DOWNLOAD_ADD_POINTS'	=> $new_attach['download_add_points'],
		'DISABLE_MOD_YES'		=> $disable_mod_yes,
		'DISABLE_MOD_NO'		=> $disable_mod_no,
		'FTP_UPLOAD_YES'		=> $allow_ftp_upload_yes,
		'FTP_UPLOAD_NO'			=> $allow_ftp_upload_no,
		'FTP_PASV_MODE_YES'		=> $ftp_pasv_mode_yes,
		'FTP_PASV_MODE_NO'		=> $ftp_pasv_mode_no,
		'TOPIC_REVIEW_YES'		=> $attachment_topic_review_yes,
		'TOPIC_REVIEW_NO'		=> $attachment_topic_review_no,
		'DISPLAY_ORDER_ASC'		=> $display_order_yes,
		'DISPLAY_ORDER_DESC'	=> $display_order_no,
		'DOWNLOAD_MODE_YES'		=> $download_mode_yes,
		'DOWNLOAD_MODE_NO'		=> $download_mode_no,
		'SHOW_APCP_YES'			=> $show_apcp_yes,
		'SHOW_APCP_NO'			=> $show_apcp_no)
	);
}

if ($submit && $mode == 'shadow')
{
	$attach_file_list = get_var('attach_file_list', array(''));
	
	for ($i = 0; $i < count($attach_file_list); $i++)
	{
		unlink_attach($attach_file_list[$i]);
		unlink_attach($attach_file_list[$i], MODE_THUMBNAIL);
	}

	$attach_id_list = get_var('attach_id_list', array(0));

	$attach_id_sql = implode(', ', $attach_id_list);

	if ($attach_id_sql != '')
	{
		$sql = 'DELETE 
			FROM ' . ATTACHMENTS_DESC_TABLE . ' 
			WHERE attach_id IN (' . $attach_id_sql . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not delete attachment entries', E_USER_WARNING);
		}

		$sql = 'DELETE 
			FROM ' . ATTACHMENTS_TABLE . ' 
			WHERE attach_id IN (' . $attach_id_sql . ')';

		if (!$result = $db->sql_query($sql))
		{
			trigger_error('Could not delete attachment entries', E_USER_WARNING);
		}
	}

	$message = '幽灵附件删除成功！<br />点击 <a href="' . append_sid('admin_attachments.php?mode=shadow') . '">这里</a> 返回幽灵附件页面<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';

	trigger_error($message);
}

if ($mode == 'shadow')
{
	@set_time_limit(0);
	
	$shadow_attachments = array();
	$shadow_row 		= array();
	$table_attachments 	= array();
	$assign_attachments	= array();
	$file_attachments 	= array();
	
	$sql = 'SELECT attach_id, physical_filename 
		FROM ' . ATTACHMENTS_DESC_TABLE . '
		ORDER BY attach_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get attachment informations', E_USER_WARNING);
	}

	$i = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$table_attachments['attach_id'][$i] 		= (int) $row['attach_id'];
		$table_attachments['physical_filename'][$i] = basename($row['physical_filename']);
		$i++;
	}
	$db->sql_freeresult($result);
	
	
	$sql = 'SELECT attach_id
		FROM ' . ATTACHMENTS_TABLE . '
		GROUP BY attach_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get attachment informations', E_USER_WARNING);
	}

	$assign_attachments = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$assign_attachments[] = intval($row['attach_id']);
	}
	$db->sql_freeresult($result);

	$file_attachments = collect_attachments();

	$shadow_attachments = array();
	$shadow_row 		= array();	

	for ($i = 0; $i < count($file_attachments); $i++)
	{
		if (count($table_attachments['attach_id']) > 0)
		{
			if ($file_attachments[$i] != '')
			{
				if (!in_array(trim($file_attachments[$i]), $table_attachments['physical_filename']) )
				{	
					$shadow_attachments[] = trim($file_attachments[$i]);
					$file_attachments[$i] = '';
				}
			}
		}
		else
		{
			if ($file_attachments[$i] != '')
			{
				$shadow_attachments[] = trim($file_attachments[$i]);
				$file_attachments[$i] = '';
			}
		}
	}

	for ($i = 0; $i < count($assign_attachments); $i++)
	{
		if (!in_array($assign_attachments[$i], $table_attachments['attach_id']))
		{
			$shadow_row['attach_id'][] 			= $assign_attachments[$i];
			$shadow_row['physical_filename'][] 	= $assign_attachments[$i];
		}
	}

	for ($i = 0; $i < count($table_attachments['attach_id']); $i++)
	{
		if ($table_attachments['physical_filename'][$i] != '')
		{
			if ( !in_array(trim($table_attachments['physical_filename'][$i]), $file_attachments))
			{	
				$shadow_row['attach_id'][] 					= $table_attachments['attach_id'][$i];
				$shadow_row['physical_filename'][] 			= trim($table_attachments['physical_filename'][$i]);

				$table_attachments['attach_id'][$i] 		= 0;
				$table_attachments['physical_filename'][$i] = '';
			}
		}
	}

	for ($i = 0; $i < count($table_attachments['attach_id']); $i++)
	{
		if ($table_attachments['attach_id'][$i])
		{
			if (!entry_exists($table_attachments['attach_id'][$i]))
			{
				$shadow_row['attach_id'][] = $table_attachments['attach_id'][$i];
				$shadow_row['physical_filename'][] = trim($table_attachments['physical_filename'][$i]);
			}
		}
	}
	
	for ($i = 0; $i < count($shadow_attachments); $i++)
	{
		$row_class = (($i % 2) == 0) ? 'row1' : 'row_2';
		
		$template->assign_block_vars('file_shadow_row', array(
			'ROW_CLASS'			=> $row_class,
			'ATTACH_ID'			=> $shadow_attachments[$i],
			'ATTACH_FILENAME'	=> $shadow_attachments[$i],
			'U_ATTACHMENT'		=> $upload_dir . '/' . basename($shadow_attachments[$i]))
		);
	}

	for ($i = 0; $i < count($shadow_row['attach_id']); $i++)
	{
		$row_class = (($i % 2) == 0) ? 'row1' : 'row_2';
		$template->assign_block_vars('table_shadow_row', array(
			'ROW_CLASS'			=> $row_class,
			'ATTACH_ID'			=> $shadow_row['attach_id'][$i],
			'ATTACH_FILENAME'	=> basename($shadow_row['physical_filename'][$i]))
		);
	}
	
	$template->set_filenames(array(
		'body' => 'admin/attach_shadow.tpl')
	);
	
	$template->assign_vars(array(
		'S_ATTACH_ACTION'	=> append_sid('admin_attachments.php' . '?mode=shadow'))
	);
	
}

if ($submit && $mode == 'cats')
{
	if (!$error)
	{
		trigger_error('更新成功<br />点击 <a href="' . append_sid('admin_attachments.php?mode=cats') . '">这里</a> 返回附件特殊类型<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板', E_USER_ERROR);
	}
}

if ($mode == 'cats')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_cat_body.tpl')
	);

	$s_assigned_group_images 	= '无';
	$s_assigned_group_streams 	= '无';
	$s_assigned_group_flash 	= '无';
	
	$sql = 'SELECT group_name, cat_id
		FROM ' . EXTENSION_GROUPS_TABLE . '
		WHERE cat_id > 0
		ORDER BY cat_id';

	$s_assigned_group_images = array();
	$s_assigned_group_streams = array();
	$s_assigned_group_flash = array();

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get Group Names from ' . EXTENSION_GROUPS_TABLE, E_USER_WARNING);
	}

	$row = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < count($row); $i++)
	{
		if ($row[$i]['cat_id'] == IMAGE_CAT)
		{
			$s_assigned_group_images[] = $row[$i]['group_name'];
		}
		else if ($row[$i]['cat_id'] == STREAM_CAT)
		{
			$s_assigned_group_streams[] = $row[$i]['group_name'];
		}
		else if ($row[$i]['cat_id'] == SWF_CAT)
		{
			$s_assigned_group_flash[] = $row[$i]['group_name'];
		}
	}

	$display_inlined_yes = ( $new_attach['img_display_inlined'] != '0' ) ? 'checked="checked"' : '';
	$display_inlined_no = ( $new_attach['img_display_inlined'] == '0' ) ? 'checked="checked"' : '';

	$create_thumbnail_yes = ( $new_attach['img_create_thumbnail'] != '0' ) ? 'checked="checked"' : '';
	$create_thumbnail_no = ( $new_attach['img_create_thumbnail'] == '0' ) ? 'checked="checked"' : '';

	$use_gd2_yes = ( $new_attach['use_gd2'] != '0' ) ? 'checked="checked"' : '';
	$use_gd2_no = ( $new_attach['use_gd2'] == '0' ) ? 'checked="checked"' : '';

	require(ROOT_PATH . 'includes/attach/functions_thumbs.php');
	
	if (!is_imagick() && !@extension_loaded('gd'))
	{
		$new_attach['img_create_thumbnail'] = '0';
	}
	else
	{
		$template->assign_block_vars('switch_thumbnail_support', array());
	}

	$template->assign_vars(array(

		'IMAGE_MAX_HEIGHT'			=> $new_attach['img_max_height'],
		'IMAGE_MAX_WIDTH'			=> $new_attach['img_max_width'],
		
		'IMAGE_LINK_HEIGHT'			=> $new_attach['img_link_height'],
		'IMAGE_LINK_WIDTH'			=> $new_attach['img_link_width'],
		'IMAGE_MIN_THUMB_FILESIZE'	=> $new_attach['img_min_thumb_filesize'],
		'IMAGE_IMAGICK_PATH'		=> $new_attach['img_imagick'],

		'DISPLAY_INLINED_YES'		=> $display_inlined_yes,
		'DISPLAY_INLINED_NO'		=> $display_inlined_no,
		
		'CREATE_THUMBNAIL_YES'		=> $create_thumbnail_yes,
		'CREATE_THUMBNAIL_NO'		=> $create_thumbnail_no,

		'USE_GD2_YES'				=> $use_gd2_yes,
		'USE_GD2_NO'				=> $use_gd2_no,

		'S_ASSIGNED_GROUP_IMAGES'	=> implode(', ', $s_assigned_group_images),
		'S_ATTACH_ACTION'			=> append_sid('admin_attachments.php' . '?mode=cats'))
	);
}

if ($check_image_cat)
{
	$board_config = array();

	$sql = 'SELECT *
		FROM ' . CONFIG_TABLE;

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not find Attachment Config Table', E_USER_WARNING);
	}

	$row = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$board_config[$row[$i]['config_name']] = trim($row[$i]['config_value']);
	}

	if ($board_config['upload_dir'][0] == '/' || ($board_config['upload_dir'][0] != '/' && $board_config['upload_dir'][1] == ':'))
	{
		$upload_dir = $board_config['upload_dir'];
	}
	else
	{
		$upload_dir = ROOT_PATH . $board_config['upload_dir'];
	}
	
	$upload_dir = $upload_dir . '/' . THUMB_DIR;

	$error = false;

	if (intval($board_config['allow_ftp_upload']) == 0 && intval($board_config['img_create_thumbnail']) == 1)
	{
		if (!@file_exists(@phpbb_realpath($upload_dir)))
		{
			@mkdir($upload_dir, 0755);
			@chmod($upload_dir, 0777);
		
			if (!@file_exists(@phpbb_realpath($upload_dir)))
			{
				$error = TRUE;
				$error_msg = '文件夹 ' . $upload_dir . ' 不存在或找不到';
			}

		}
	
		if (!$error && !is_dir($upload_dir))
		{
			$error = TRUE;
			$error_msg = $upload_dir . ' 不是文件夹';
		}
	
		if (!$error)
		{
			if ( !($fp = @fopen($upload_dir . '/0_000000.000', 'w')) )
			{
				$error = TRUE;
				$error_msg = '文件夹 ' . $upload_dir . ' 没有写入权限';
			}
			else
			{
				@fclose($fp);
				@unlink($upload_dir . '/0_000000.000');
			}
		}
	}
	else if (intval($board_config['allow_ftp_upload']) && intval($board_config['img_create_thumbnail']))
	{
		$server = ( empty($board_config['ftp_server']) ) ? 'localhost' : $board_config['ftp_server'];

		$conn_id = @ftp_connect($server);

		if (!$conn_id)
		{
			$error = TRUE;
			$error_msg = '无法与服务器 ' . $server . ' 建立链接';
		}

		$login_result = @ftp_login($conn_id, $board_config['ftp_user'], $board_config['ftp_pass']);

		if (!$login_result && !$error)
		{
			$error = TRUE;
			$error_msg = 'FTP登录失败::' . $board_config['ftp_user'];
		}
		
		if (!@ftp_pasv($conn_id, intval($board_config['ftp_pasv_mode'])))
		{
			$error = TRUE;
			$error_msg = 'FTP被动模式错误';
		}

		if (!$error)
		{
			$tmpfname = @tempnam('/tmp', 't0000');

			@unlink($tmpfname); // unlink for safety on php4.0.3+

			$fp = @fopen($tmpfname, 'w');

			@fwrite($fp, 'test');

			@fclose($fp);

			$result = @ftp_chdir($conn_id, $board_config['ftp_path'] . '/' . THUMB_DIR);
			
			if (!$result)
			{
				@ftp_mkdir($conn_id, $board_config['ftp_path'] . '/' . THUMB_DIR);
			}
			
			$result = @ftp_chdir($conn_id, $board_config['ftp_path'] . '/' . THUMB_DIR);

			if (!$result)
			{
				$error = TRUE;
				$error_msg = 'FTP路径出错::' . $board_config['ftp_path'] . '/' . THUMB_DIR;
			}
			else
			{
				$res = @ftp_put($conn_id, 't0000', $tmpfname, FTP_ASCII);
				
				if (!$res)
				{
					$error = TRUE;
					$error_msg = '无法上传文件到 FTP 文件夹::' . $board_config['ftp_path'] . '/' . THUMB_DIR;
				}
				else
				{
					$res = @ftp_delete($conn_id, 't0000');

					if (!$res)
					{
						$error = TRUE;
						$error_msg = '无法删除在 FTP 文件夹的文件::' . $board_config['ftp_path'] . '/' . THUMB_DIR;
					}
				}
			}

			@ftp_quit($conn_id);

			@unlink($tmpfname);
		}
	}
	
	if (!$error)
	{
		trigger_error('测试完成，看起来什么问题<br />点击 <a href="' . append_sid('admin_attachments.php?mode=cats') . '">这里</a>返回附件特殊类型<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板', E_USER_ERROR);
	}
}

if ($mode == 'sync')
{
	
	$message_title = '附件缓存同步';
	
	$info = '';
	
	@set_time_limit(0);

	$message = '<p>正在进行主题同步</p>';
	
	$sql = 'SELECT topic_id	
		FROM ' . TOPICS_TABLE;
	
	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get topic ID', E_USER_WARNING);
	}
	while ($row = $db->sql_fetchrow($result))
	{
		@flush();
		attachment_sync_topic($row['topic_id']);
	}
	$db->sql_freeresult($result);

	$message .= '<p>主题同步成功</p>';
	
	$message .= '<p>正在进行帖子的同步</p>';
	
	$sql = 'SELECT a.attach_id, a.post_id, a.user_id_1, p.poster_id 
		FROM ' . ATTACHMENTS_TABLE . ' a, ' . POSTS_TABLE . ' p 
		WHERE a.user_id_2 = 0 
			AND p.post_id = a.post_id 
			AND a.user_id_1 <> p.poster_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get post ID', E_USER_WARNING);
	}

	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$sql = 'UPDATE ' . ATTACHMENTS_TABLE . ' SET user_id_1 = ' . intval($rows[$i]['poster_id']) . ' 
			WHERE attach_id = ' . intval($rows[$i]['attach_id']) . ' AND post_id = ' . intval($rows[$i]['post_id']);

		$db->sql_query($sql);

		@flush();
		
	}

	$message .= '<p>正帖子同步成功</p>';
	$message .= '<p>正在进行缩略图缓存同步</p>';
	
	$sql = 'SELECT attach_id, physical_filename, thumbnail 
		FROM ' . ATTACHMENTS_DESC_TABLE . ' 
		WHERE thumbnail = 1';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get thumbnail informations', E_USER_WARNING);
	}

	$i = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		@flush();

		if (!thumbnail_exists(basename($row['physical_filename'])))
		{
			$message .= '<p>缩略图已重设给附件: ' . $row['physical_filename'] . ' </p>';
			
			$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . ' 
				SET thumbnail = 0 
				WHERE attach_id = ' . (int) $row['attach_id'];
			if (!$db->sql_query($sql))
			{
				$error = $db->sql_error();
				die('Could not update thumbnail informations -> ' . $error['message'] . ' -> ' . $sql);
			}
		}
		$i++;
	}
	$db->sql_freeresult($result);

	$sql = 'SELECT attach_id, physical_filename, thumbnail 
		FROM ' . ATTACHMENTS_DESC_TABLE . ' WHERE thumbnail = 0';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get thumbnail informations', E_USER_WARNING);
	}

	while ($row = $db->sql_fetchrow($result))
	{
		@flush();

		if (thumbnail_exists(basename($row['physical_filename'])))
		{
			$message .= '<p>缩略图已重设给附件: ' . $row['physical_filename'] . ' </p>';
			unlink_attach(basename($row['physical_filename']), MODE_THUMBNAIL);
		}

	}
	$db->sql_freeresult($result);

	@flush();
	$message .= '<p>附件缓存同步完成！</p>';
	
	$message .= '<p>点击 <a href="' . append_sid('index.php?pane=left') . '">这里</a> 返回上级</p>';	
	
	trigger_error($message);

}

if ($submit && $mode == 'quota')
{
	$quota_change_list = get_var('quota_change_list', array(0));
	$quota_desc_list = get_var('quota_desc_list', array(''));
	$filesize_list = get_var('max_filesize_list', array(0));
	$size_select_list = get_var('size_select_list', array(''));

	$allowed_list = array();

	for ($i = 0; $i < count($quota_change_list); $i++)
	{
		$filesize_list[$i] = ($size_select_list[$i] == 'kb') ? round($filesize_list[$i] * 1024) : ( ($size_select_list[$i] == 'mb') ? round($filesize_list[$i] * 1048576) : $filesize_list[$i] );

		$sql = 'UPDATE ' . QUOTA_LIMITS_TABLE . " 
			SET quota_desc = '" . $db->sql_escape($quota_desc_list[$i]) . "', quota_limit = " . (int) $filesize_list[$i] . "
			WHERE quota_limit_id = " . (int) $quota_change_list[$i];
		
		if (!($db->sql_query($sql)))
		{
			trigger_error('Couldn\'t update Quota Limits', E_USER_WARNING);
		}
	}

	$quota_id_list = get_var('quota_id_list', array(0));

	$quota_id_sql = implode(', ', $quota_id_list);

	if ($quota_id_sql != '')
	{
		$sql = 'DELETE 
			FROM ' . QUOTA_LIMITS_TABLE . ' 
			WHERE quota_limit_id IN (' . $quota_id_sql . ')';

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not delete Quota Limits', E_USER_WARNING);
		}

		$sql = 'DELETE 
			FROM ' . QUOTA_TABLE . ' 
			WHERE quota_limit_id IN (' . $quota_id_sql . ')';

		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not delete Quotas', E_USER_WARNING);
		}
	}

	$quota_desc = get_var('quota_description', '');
	$filesize = get_var('add_max_filesize', 0);
	$size_select = get_var('add_size_select', '');
	$add = ( isset($_POST['add_quota_check']) ) ? TRUE : FALSE;

	if ($quota_desc != '' && $add)
	{
		$sql = 'SELECT quota_desc
			FROM ' . QUOTA_LIMITS_TABLE;
	
		if (!($result = $db->sql_query($sql)))
		{
			trigger_error('Could not query Quota Limits Table', E_USER_WARNING);
		}
			
		$row = $db->sql_fetchrowset($result);
		$num_rows = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ($num_rows > 0)
		{
			for ($i = 0; $i < $num_rows; $i++)
			{
				if ($row[$i]['quota_desc'] == $quota_desc)
				{
					$error = TRUE;
					$error_msg .= '<p>限制 ' . $quota_desc . ' 已经存在</p>';
				}
			}
		}
			
		if (!$error)
		{
			$filesize = ($size_select == 'kb' ) ? round($filesize * 1024) : ( ($size_select == 'mb') ? round($filesize * 1048576) : $filesize );
		
			$sql = 'INSERT INTO ' . QUOTA_LIMITS_TABLE . " (quota_desc, quota_limit) 
			VALUES ('" . $db->sql_escape($quota_desc) . "', " . (int) $filesize . ")";
	
			if (!($db->sql_query($sql)))
			{
				trigger_error('Could not add Quota Limit', E_USER_WARNING);
			}
		}

	}

	if (!$error)
	{
		$message = '成功更新附件限制<br />点击 <a href="' . append_sid('admin_attachments.php?mode=quota') . '">这里</a> 返回附件限制管理<br />点击 <a href="' . append_sid('index.php') . '">这里</a> 返回超级面板';
		trigger_error($message);
	}

}

if ($mode == 'quota')
{
	$template->set_filenames(array(
		'body' => 'admin/attach_quota_body.tpl')
	);

	$max_add_filesize = $board_config['max_filesize'];
	$size = ($max_add_filesize >= 1048576) ? 'mb' : ( ($max_add_filesize >= 1024) ? 'kb' : 'b' );

	if ($max_add_filesize >= 1048576)
	{
		$max_add_filesize = round($max_add_filesize / 1048576 * 100) / 100;
	}
	else if ( $max_add_filesize >= 1024)
	{
		$max_add_filesize = round($max_add_filesize / 1024 * 100) / 100;
	}

	$template->assign_vars(array(
		'MAX_FILESIZE'				=> $max_add_filesize,
		'S_FILESIZE'			=> size_select('add_size_select', $size),
		'S_ATTACH_ACTION'		=> append_sid('admin_attachments.php?mode=quota'))
	);

	$sql = 'SELECT * 
		FROM ' . QUOTA_LIMITS_TABLE . ' 
		ORDER BY quota_limit DESC';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get quota limits', E_USER_WARNING);
	}
	
	$rows = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < count($rows); $i++)
	{
		$size_format = ($rows[$i]['quota_limit'] >= 1048576) ? 'mb' : ( ($rows[$i]['quota_limit'] >= 1024) ? 'kb' : 'b' );

		if ($rows[$i]['quota_limit'] >= 1048576)
		{
			$rows[$i]['quota_limit'] = round($rows[$i]['quota_limit'] / 1048576 * 100) / 100;
		}
		else if($rows[$i]['quota_limit'] >= 1024)
		{
			$rows[$i]['quota_limit'] = round($rows[$i]['quota_limit'] / 1024 * 100) / 100;
		}
		$row_class = (($i % 2) == 0) ? 'row1' : 'row_2';
		
		$template->assign_block_vars('limit_row', array(
			'ROW_CLASS'			=> $row_class,
			'QUOTA_NAME'		=> $rows[$i]['quota_desc'],
			'QUOTA_ID'			=> $rows[$i]['quota_limit_id'],
			'S_FILESIZE'		=> size_select('size_select_list[]', $size_format),
			'U_VIEW'			=> append_sid('admin_attachments.php?mode=' . $mode . '&amp;e_mode=view_quota&amp;quota_id=' . $rows[$i]['quota_limit_id']),
			'MAX_FILESIZE'		=> $rows[$i]['quota_limit'])
		);
	}
}

if ($mode == 'quota' && $e_mode == 'view_quota')
{
	$quota_id = get_var('quota_id', 0);
	
	if (!$quota_id)
	{
		trigger_error('Invalid Call', E_USER_ERROR);
	}

	$template->assign_block_vars('switch_quota_limit_desc', array());

	$sql = 'SELECT * 
		FROM ' . QUOTA_LIMITS_TABLE . ' 
		WHERE quota_limit_id = ' . (int) $quota_id . ' 
		LIMIT 1';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get quota limits', E_USER_WARNING);
	}
	
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'L_QUOTA_LIMIT_DESC'	=> $row['quota_desc'])
	);
	
	$sql = 'SELECT q.user_id, u.username, q.quota_type 
		FROM ' . QUOTA_TABLE . ' q, ' . USERS_TABLE . ' u
		WHERE q.quota_limit_id = ' . (int) $quota_id . ' 
			AND q.user_id <> 0 
			AND q.user_id = u.user_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get quota limits', E_USER_WARNING);
	}
	
	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$template->assign_block_vars('users_upload_row', array(
			'USER_ID'		=> $rows[$i]['user_id'],
			'USERNAME'		=> $rows[$i]['username'])
		);
	}

	$sql = 'SELECT q.group_id, g.group_name, q.quota_type 
		FROM ' . QUOTA_TABLE . ' q, ' . GROUPS_TABLE . ' g
		WHERE q.quota_limit_id = ' . (int) $quota_id . '
			AND q.group_id <> 0 
			AND q.group_id = g.group_id';

	if (!($result = $db->sql_query($sql)))
	{
		trigger_error('Could not get quota limits', E_USER_WARNING);
	}
	
	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$template->assign_block_vars('groups_upload_row', array(
			'GROUP_ID'		=> $rows[$i]['group_id'],
			'GROUPNAME'		=> $rows[$i]['group_name'])
		);
	}
}


if ($error)
{
	error_box('ERROR_BOX', $error_msg);
}

$template->pparse('body');

page_footer();

?>