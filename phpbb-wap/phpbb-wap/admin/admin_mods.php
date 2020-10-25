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
	$module['系统']['MODS'] = $filename;	
	return;
}
define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');
require (ROOT_PATH . 'includes/functions/mods.php');

$mode = get_var('mode', '');

if ($mode == 'install')
{
	$install = get_var('install', '');
	
	if (empty($install))
	{
		trigger_error('请指定要安装的MOD', E_USER_ERROR);
	}
	
	if( @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $install . '/install.php')) && @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $install . '/uninstall.php')) && @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $install . '/' . $install . '.php')) )
	{
		
		require (ROOT_PATH . 'includes/functions/sql_parse.php');
		require(ROOT_PATH. 'mods/' . $install . '/install.php');
		
		if ($finish)
		{
			
			$modinfo = get_mod_data(ROOT_PATH . 'mods/' . $install . '/' . $install . '.php');
			
			$install_mod_name 			= trim($modinfo['mod_name']);
			$install_mod_support		= add_http($modinfo['mod_support']);
			$install_mod_version		= $modinfo['mod_version'];
			$install_mod_description	= $modinfo['mod_description'];
			$install_mod_author			= (!empty($modinfo['mod_author'])) ? $modinfo['mod_author'] : '热心网友';
			$install_mod_show			= get_mod_show($modinfo['mod_show']);
			
			$sql = 'INSERT INTO ' . MODS_TABLE . " (mod_name, mod_dir, mod_desc, mod_author, mod_support, mod_version, mod_show, mod_power) 
				VALUES ('" . $db->sql_escape($install_mod_name) . "', '" . $db->sql_escape($install) . "', '" . $db->sql_escape($install_mod_description) . "', '" . $db->sql_escape($install_mod_author) . "', '" . $db->sql_escape($install_mod_support) . "', '" . $db->sql_escape($install_mod_version) . "', '$install_mod_show', 1);";
			
			if ( !$db->sql_query($sql) )
			{
				trigger_error('无法插入数据到 mods 表', E_USER_WARNING);
			}
			
			trigger_error('安装成功！点击 <a href="' . append_sid('admin_mods.php') . '">这里</a> 返回MODS列表', E_USER_ERROR);
		}
		else
		{
			trigger_error('安装失败', E_USER_ERROR);
		}
	}
	else
	{
		trigger_error('您指定的是一个不合法的MOD', E_USER_ERROR);
	}
}
// 删除
else if ($mode == 'delete')
{
	$delete = get_var('delete', '');
	
	if (empty($delete))
	{
		trigger_error('请指定要删除的MOD', E_USER_ERROR);
	}
	
	if (@file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $delete . '/uninstall.php')))
	{
		
		$sql = 'SELECT mod_id 
			FROM ' . MODS_TABLE . " 
			WHERE mod_dir = '" . $db->sql_escape($delete) . "'";
		
		if ( !$result = $db->sql_query($sql) )
		{
			trigger_error('无法删除 mods 表', E_USER_WARNING);
		}	
		
		if ($db->sql_numrows($result))
		{
			require (ROOT_PATH . 'includes/functions/sql_parse.php');
			
			require(ROOT_PATH. 'mods/' . $delete . '/uninstall.php');
			
			if ($finish)
			{
				$sql = 'DELETE FROM ' . MODS_TABLE . " WHERE mod_dir = '" . $db->sql_escape($delete) . "'";
				
				if ( !$db->sql_query($sql) )
				{
					trigger_error('无法删除 mods 表', E_USER_WARNING);
				}	
				
				if (!phpbb_deldir(ROOT_PATH . 'mods/' . $delete))
				{
					trigger_error('无法删除 mod 包中的某些文件', E_USER_ERROR);
				}
				
				trigger_error('删除成功！点击 <a href="' . append_sid('admin_mods.php') . '">这里</a> 返回MODS列表', E_USER_ERROR);
			}
			else
			{
				trigger_error('删除失败', E_USER_ERROR);
			}	
		}
		else
		{
			if (!phpbb_deldir(ROOT_PATH . 'mods/' . $delete))
			{
				trigger_error('无法删除 mod 包中的某些文件', E_USER_ERROR);
			}
			
			trigger_error('删除成功！点击 <a href="' . append_sid('admin_mods.php') . '">这里</a> 返回MODS列表', E_USER_ERROR);
		}
		
	}
	else
	{
		trigger_error('该MOD没有提供卸载方法，请不要直接删除', E_USER_ERROR);
	}
	
}
else if ($mode == 'power')
{
	$mod 	= get_var('mod', '');
	$power 	= get_var('power', 'off');
	
	if (empty($mod))
	{
		trigger_error('请指定要设置的MOD', E_USER_ERROR);
	}
	
	if ($power == 'on')
	{
		$mod_power = 1;
	}
	else
	{
		$mod_power = 0;
	}
	
	$sql = 'UPDATE ' . MODS_TABLE . ' 
		SET mod_power = ' . $mod_power . " 
		WHERE mod_dir = '" . $db->sql_escape($mod) . "'";
	
	if ( !$db->sql_query($sql) )
	{
		trigger_error('无法更新 mods 表', E_USER_WARNING);
	}

	redirect(append_sid('admin/admin_mods.php', true));
	
}
else if ($mode == 'show')
{
	$mod 	= get_var('mod', '');
	$show 	= get_var('show', 'off');
	
	if (empty($mod))
	{
		trigger_error('请指定要设置的MOD', E_USER_ERROR);
	}
	
	if ($show == 'on')
	{
		$mod_show = 1;
	}
	else
	{
		$mod_show = 0;
	}
	
	$sql = 'UPDATE ' . MODS_TABLE . ' 
		SET mod_show = ' . $mod_show . " 
		WHERE mod_dir = '" . $db->sql_escape($mod) . "'";
	
	if ( !$db->sql_query($sql) )
	{
		trigger_error('无法更新 mods 表', E_USER_WARNING);
	}

	redirect(append_sid('admin/admin_mods.php', true));	
}
else if ($mode == 'uninstall')
{
	$uninstall = get_var('uninstall', '');
	
	if (empty($uninstall))
	{
		trigger_error('请指定要卸载的MOD', E_USER_ERROR);
	}
	
	if (@file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $uninstall . '/uninstall.php')))
	{
		require (ROOT_PATH . 'includes/functions/sql_parse.php');
		
		require(ROOT_PATH. 'mods/' . $uninstall . '/uninstall.php');
		
		if ($finish)
		{
			$sql = 'DELETE FROM ' . MODS_TABLE . " WHERE mod_dir = '" . $db->sql_escape($uninstall) . "'";
			
			if ( !$db->sql_query($sql) )
			{
				trigger_error('无法删除 mods 表', E_USER_WARNING);
			}	
			
			trigger_error('卸载成功！点击 <a href="' . append_sid('admin_mods.php') . '">这里</a> 返回MODS列表', E_USER_ERROR);
		}
		else
		{
			trigger_error('卸载失败', E_USER_ERROR);
		}		
		
	}
	else
	{
		trigger_error('您安装的MOD不提供卸载方法', E_USER_ERROR);
	}
}
else if ($mode == 'admin')
{
	$admin_mod = get_var('mods', '');
	$admin_load = get_var('load', '');

	if ($admin_mod == '')
	{
		trigger_error('请指定您要管理的MOD', E_USER_ERROR);
	}

	$sql = 'SELECT mod_id 
		FROM ' . MODS_TABLE . " 
		WHERE mod_dir = '" . $db->sql_escape($admin_mod) . "'";
	
	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('无法查询 mods 表', E_USER_WARNING);
	}	
	
	if (!$db->sql_numrows($result))
	{
		trigger_error('您没有安装此MOD', E_USER_ERROR);
	}

	if ($admin_load)
	{
		if(@file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $admin_mod . '/admin_' . $admin_load . '.php')))
		{
			
			$template->reset_root(ROOT_PATH. "mods/$admin_mod/template/");
			
			@include (ROOT_PATH. 'mods/' . $admin_mod . '/admin_' . $admin_load . '.php');
			
			$template->reset_root(ROOT_PATH . 'styles/' . $style->path . '/');

			page_footer();
		}
		else
		{
			trigger_error('此MOD没有这管理文件', E_USER_ERROR);
		}
	}
	else
	{

		if( @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $admin_mod . '/admin.php')) )
		{	
			
			$template->reset_root(ROOT_PATH. "mods/$admin_mod/template/");

			@include (ROOT_PATH. 'mods/' . $admin_mod . '/admin.php');
			
			$template->reset_root(ROOT_PATH . 'styles/' . $style->path . '/');

			page_footer();

		}
		else
		{
			trigger_error('这个MOD是不带后台管理模块的', E_USER_ERROR);
		}
	}
}
else if ($mode == 'upload')
{
	if (isset($_POST['import']))
	{
		if (@ini_get('allow_url_fopen') == '1' || strtolower(@ini_get('allow_url_fopen')) == 'on')
		{

			$importurl = $_POST['url'];

			$file_type = substr(strrchr($importurl, '.'), 1);
			
			if ($file_type != 'zip')
			{
				trigger_error('只能上传zip格式的文件' . back_link(append_sid('admin_mods.php')));
			}

			$filename = basename($path);

			@set_time_limit(0);

			@copy($importurl, ROOT_PATH . 'store/' . $filename);

		}
		else
		{
			trigger_error('对不起，你的服务器没有开启 allow_url_fopen，请选择本地上传' . back_link(append_sid('admin_mods.php')));
		}
	}
	else
	{
		if ($_FILES["file"]["error"] > 0)
		{
			trigger_error('文件上传失败' . $_FILES["file"]["error"] . back_link(append_sid('admin_mods.php')));
		}

		$filename = $_FILES["file"]["name"];

		$file_type = substr(strrchr($filename, '.'), 1);
		
		if ($file_type != 'zip')
		{
			trigger_error('只能上传zip格式的文件' . back_link(append_sid('admin_mods.php')));
		}

		move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_PATH . 'store/' . $filename);
	}

	require_once ROOT_PATH . 'includes/class/zip.php';

	$zip = new PHPZip();

	$zip->unZip(ROOT_PATH . 'store/' . $filename, ROOT_PATH . 'mods');

	trigger_error('上传成功！' . back_link(append_sid('admin_mods.php')));
}
else
{

	//$per 	= $board_config['topics_per_page'];
	//$start 	= get_pagination_start($per);
	
	$sql = 'SELECT mod_name, mod_dir, mod_author, mod_version, mod_support, mod_show, mod_desc, mod_power 
		FROM ' . MODS_TABLE;
		//LIMIT $start, $per ";
	
	if(!$result = $db->sql_query($sql))
	{
		trigger_error('无法查询 ' . MODS_TABLE . ' 表', E_USER_WARNING);
	}
	
	$i = 0;
	while($row = $db->sql_fetchrow($result))
	{
		
		if($row['mod_power'])
		{ 
			$l_power 		= '停用';
			$l_admin_power 	= '启用';
			$u_power 		= append_sid('admin_mods.php?mode=power&amp;power=off&amp;mod=' . $row['mod_dir']);
		}
		else
		{
			$l_power 		= '启用';
			$l_admin_power 	= '停用';
			$u_power 		= append_sid('admin_mods.php?mode=power&amp;power=on&amp;mod=' . $row['mod_dir']);
		}
		
		if ($row['mod_show'])
		{ 
			$l_show 		= '隐藏';
			$l_admin_show 	= '隐藏';
			$u_show 		= append_sid('admin_mods.php?mode=show&amp;show=off&amp;mod=' . $row['mod_dir']);
		}
		else
		{
			$l_show 		= '显示';
			$l_admin_show 	= '显示';
			$u_show 		= append_sid('admin_mods.php?mode=show&amp;show=on&amp;mod=' . $row['mod_dir']);
		}
		
		$template->assign_block_vars('install_list', array(
			//'MOD_NUMBER'		=> $i + $start + 1,
			'MOD_NUMBER'		=> $i + 1,			
			'MOD_POWER'			=> '<a href="' . $u_power . '">' . $l_power . '</a>',
			'S_MOD_SHOW'		=> '<a href="' . $u_show . '">' . $l_show . '</a>',
			'U_MOD_UNINSTALL'	=> append_sid('admin_mods.php?mode=uninstall&amp;uninstall=' . $row['mod_dir']),
			'U_MOD_DELETE'		=> append_sid('admin_mods.php?mode=delete&amp;delete=' . $row['mod_dir']))
		);
		
		if (@file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $row['mod_dir'] . '/admin.php')))
		{
			$template->assign_block_vars('install_list.admin', array(
				'MOD_NAME' 			=> $row['mod_name'],
				'MOD_AUTHOR'		=> $row['mod_author'],
				'MOD_VERSION'		=> $row['mod_version'],
				'MOD_SUPPORT'		=> $row['mod_support'],
				'MOD_SHOW'			=> $row['mod_show'],
				'MOD_DESC'			=> $row['mod_desc'],
				'MOD_POWER'			=> $l_admin_power,
				'S_MOD_SHOW'		=> '<a href="' . $u_show . '">' . $l_show . '</a>',
				'U_ADMIN_MODS'		=> append_sid('admin_mods.php?mode=admin&amp;mods=' . $row['mod_dir']))
			);
		}
		else
		{
			$template->assign_block_vars('install_list.not_admin', array(
				'MOD_NAME' 			=> $row['mod_name'],
				'MOD_AUTHOR'		=> $row['mod_author'],
				'MOD_VERSION'		=> $row['mod_version'],
				'MOD_SUPPORT'		=> $row['mod_support'],
				'MOD_SHOW'			=> $row['mod_show'],
				'MOD_DESC'			=> $row['mod_desc'],
				'MOD_POWER'			=> $l_admin_power)
			);
		}	
		
		$i++;
	}

	// 打开mods目录
	if( $dir = @opendir(ROOT_PATH . 'mods/') )
	{
		// 读出mods目录的内容
		$i = 0;
		while( $sub_dir = @readdir($dir) )
		{
			// 去除文件、符号链接、本目录、上级目录
			if( !is_file(phpbb_realpath(ROOT_PATH . 'mods/' .$sub_dir)) && !is_link(phpbb_realpath(ROOT_PATH . 'mods/' . $sub_dir)) && $sub_dir != '.' && $sub_dir != '..' )
			{
				// 合法的mods文件必须存在 install.php（安装）、uninstall.php（卸载） 和一个mod文件 
				if( @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $sub_dir . '/install.php')) && @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $sub_dir . '/uninstall.php')) && @file_exists(@phpbb_realpath(ROOT_PATH. 'mods/' . $sub_dir . '/' . $sub_dir . '.php')) )
				{
					$sql = 'SELECT mod_id 
						FROM ' . MODS_TABLE . " 
						WHERE mod_dir = '" . $db->sql_escape($sub_dir) . "'";
					
					if ( !$result = $db->sql_query($sql) )
					{
						trigger_error('查询 mods 表失败！', E_USER_WARNING);
					}
					
					// 如果数据库中没有此 mod 的记录，说明此 mod 还没有安装
					if (!$db->sql_numrows($result))
					{

						// 获取MOD的信息
						$modinfo = get_mod_data(ROOT_PATH . 'mods/' . $sub_dir . '/' . $sub_dir . '.php');

						// MOD的名称不能为空
						if (!empty($modinfo['mod_name']))
						{
							$row_class = ( !($i % 2) ) ? 'row1' : 'row2';
							
							$template->assign_block_vars('uninstall_list', array(
								'ROW_CLASS'			=> $row_class,
								'MOD_NUMBER'		=> $i + 1,
								'MOD_NAME' 			=> trim($modinfo['mod_name']),
								'MOD_SUPPORT'		=> add_http($modinfo['mod_support']),
								'MOD_SHOW'			=> get_mod_show($modinfo['mod_show']),
								'MOD_VERSION'		=> $modinfo['mod_version'],
								'MOD_DESCRIPTION'	=> $modinfo['mod_description'],
								'MOD_AUTHOR'		=> (!empty($modinfo['mod_author'])) ? $modinfo['mod_author'] : '热心网友',
								
								'U_MOD_INSTALL'		=> append_sid('admin_mods.php?mode=install&amp;install=' . $sub_dir),
								'U_MOD_DELETE'		=> append_sid('admin_mods.php?mode=delete&amp;delete=' . $sub_dir))
							);
						
							$i++;
						}
					}
				}
			}
		}
	}
	
	page_header();
	
	$template->assign_var('S_UPLOAD', append_sid('admin_mods.php?mode=upload'));

	$template->set_filenames(array(
		'body' => 'admin/mods_body.tpl')
	);
	
	$template->pparse('body');
	
	page_footer();
}
?>