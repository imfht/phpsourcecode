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
	$module['系统']['风格'] = $filename;
	return;
}

define('IN_PHPBB', true);
define('ROOT_PATH', './../');
require('./pagestart.php');

$confirm = ( isset($_POST['confirm']) ) ? true : false;

if( isset($_GET['mode']) || isset($_POST['mode']) )
{
	$mode = ( isset($_GET['mode']) ) ? $_GET['mode'] : $_POST['mode'];
	$mode = htmlspecialchars($mode);
}
else 
{
	$mode = '';
}

switch( $mode )
{
	// 卸载风格
	case 'uninstall':
	
		// 指定卸载的风格路径
		$uninstall_to 	= ( isset($_GET['path']) ) ? $_GET['path'] : '';

		//  没有选择风格
		if (!$uninstall_to)
		{
			trigger_error('您没有选中任何风格');
		}
		
		// 不卸载
		if ( isset($_POST['cancel']) )
		{
			redirect(append_sid('admin/admin_styles.php', true));
		}
		
		if (count($style->data) == 1)
		{
			trigger_error('目前只有一个风格，您不能卸载' . back_link(append_sid('admin_styles.php')));
		}
		
		$sql = 'SELECT style_id, style_name 
			FROM ' . STYLES_TABLE . " 
			WHERE style_path = '" . $db->sql_escape($uninstall_to) . "'";
			
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法查询风格的信息', E_USER_WARNING);
		}
		
		if (!$uninstall = $db->sql_fetchrow($result))
		{
			trigger_error('您没有安装这个风格' . back_link(append_sid('admin_styles.php')));
		}
		
		// 网站默认的风格删除了，匿名用户？？
		if($uninstall['style_id'] == $board_config['default_style'])
		{
			trigger_error('这个风格是你网站的默认风格，请先把网站的默认风格设置成其它风格再来卸载' . back_link(append_sid('admin_styles.php')));
		}
		
		// 确认卸载
		if( !$confirm )
		{

			$s_hidden = '<p>卸载后所有使用本风格的用户都将使用';

			$s_hidden .= '<select name="style">';

			foreach ($style->data as $style_id => $value)
			{
				if ($style_id == $uninstall['style_id']) continue;

				$s_hidden .= '<option value="' . $style_id . '">' . $value['name'] . '</option>';
			}

			$s_hidden .= '</select>这个风格！</p>';

			confirm_box(
				'卸载风格', 
				'确认', 
				'是否卸载 ' . $uninstall['style_name'] . ' 这个风格？', 
				append_sid('admin_styles.php?mode=uninstall&path=' . $uninstall_to),
				$s_hidden
			);
		}

		// 删除已卸载风格的数据
		$sql = 'DELETE FROM ' . STYLES_TABLE . " 
			WHERE style_id = " . (int) $uninstall['style_id'];
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法删除风格数据', E_USER_WARNING);
		}

		// 把使用此风格的用户风格设置为1
		$sql = 'UPDATE ' . USERS_TABLE . ' 
			SET user_style = ' . (int) $_GET['style'] . '
			WHERE user_style = ' . (int) $uninstall['style_id'];
			
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法更新用户使用的风格', E_USER_WARNING);
		}
		
		$style->Clear();

		trigger_error('风格卸载成功！' . back_link(append_sid('admin_styles.php')));
	break;
	
	case 'delete':
		$delete_to = isset($_GET['path']) ? $_GET['path'] : '';
		if ($delete_to == '')
		{
			trigger_error('请指定要删除的风格');
		}

		$sql = 'SELECT style_id
			FROM ' . STYLES_TABLE . " 
			WHERE style_path = '" . $db->sql_escape($delete_to) . "'";
			
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法获取风格的信息', E_USER_WARNING);
		}
		
		if ($db->sql_fetchrow($result))
		{
			trigger_error('请先把风格卸载后再进行删除' . back_link('admin_styles.php'));
		}

		if (!is_dir(ROOT_PATH . 'styles/' . $delete_to))
		{
			trigger_error('风格可能已经删除' . back_link(append_sid('admin_styles.php')));
		}

		if( !phpbb_deldir(ROOT_PATH . 'styles/' . $delete_to) )
		{ 
			trigger_error('删除失败！' . back_link(append_sid('admin_styles.php')));
		}

		trigger_error('删除成功！' . back_link(append_sid('admin_styles.php')));

	break;

	case 'download':
		$path = isset($_GET['path']) ? $_GET['path'] : '';

		if ($path == '')
		{
			trigger_error('打包下载的风格' . back_link(append_sid('admin_styles.php')));
		}

		if (!is_dir(ROOT_PATH . 'styles/' . $path))
		{
			trigger_error('您要打包的风格不存在');
		}

		require_once ROOT_PATH . 'includes/class/zip.php';

		$zip = new PHPZip();
		
		$zip->ZipAndDownload(ROOT_PATH . 'styles/' . $path, $path);

	break;

	case 'zip':
		$path = isset($_GET['path']) ? $_GET['path'] : '';

		if ($path == '')
		{
			trigger_error('打包下载的风格' . back_link(append_sid('admin_styles.php')));
		}

		if (!is_dir(ROOT_PATH . 'styles/' . $path))
		{
			trigger_error('您要打包的风格不存在');
		}

		require_once ROOT_PATH . 'includes/class/zip.php';

		$zip = new PHPZip();

		$zip->Zip(ROOT_PATH . 'styles/' . $path, ROOT_PATH . '/store/' . $path . '_' . date('Ymd') . '.zip');

		trigger_error('已为您保存在网站 store 目录下，文件名为 ' . $path . '_' . date('Ymd') . '.zip' . back_link(append_sid('admin_styles.php')));

	break;

	case 'upload':

		if (isset($_POST['import']))
		{
			if (@ini_get('allow_url_fopen') == '1' || strtolower(@ini_get('allow_url_fopen')) == 'on')
			{

				$importurl = $_POST['url'];

				$file_type = substr(strrchr($importurl, '.'), 1);
				
				if ($file_type != 'zip')
				{
					trigger_error('只能上传zip格式的文件' . back_link(append_sid('admin_styles.php')));
				}

				$filename = basename($path);

				@set_time_limit(0);

				@copy($importurl, ROOT_PATH . 'store/' . $filename);

			}
			else
			{
				trigger_error('对不起，你的服务器没有开启 allow_url_fopen，请选择本地上传' . back_link(append_sid('admin_styles.php')));
			}
		}
		else
		{
			if ($_FILES["file"]["error"] > 0)
			{
				trigger_error('文件上传失败' . $_FILES["file"]["error"] . back_link(append_sid('admin_styles.php')));
			}

			$filename = $_FILES["file"]["name"];

			$file_type = substr(strrchr($filename, '.'), 1);
			
			if ($file_type != 'zip')
			{
				trigger_error('只能上传zip格式的文件' . back_link(append_sid('admin_styles.php')));
			}

			move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_PATH . 'store/' . $filename);
		}

		require_once ROOT_PATH . 'includes/class/zip.php';

		$zip = new PHPZip();

		$zip->unZip(ROOT_PATH . 'store/' . $filename, ROOT_PATH . 'styles');

		trigger_error('上传成功！' . back_link(append_sid('admin_styles.php')));

	break;

	case 'default':
		
		$path = isset($_GET['path']) ? $_GET['path'] : '';

		$sql = 'SELECT style_id, style_name
			FROM ' . STYLES_TABLE . " 
			WHERE style_path = '" . $db->sql_escape($path) . "'";
			
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法查询风格的信息', E_USER_WARNING);
		}
		
		if (!$row = $db->sql_fetchrow($result))
		{
			trigger_error('您没有安装这个风格' . back_link(append_sid('admin_styles.php')));
		}

		set_config('default_style', $row['style_id']);

		$cache->clear('global_config');

		trigger_error('风格' . $row['style_name'] . '已被设为默认' . back_link(append_sid('admin_styles.php')));
		break;

	case 'install':
		
		// 获取要安装的风格
		$install_to = ( isset($_GET['path']) ) ? urldecode($_GET['path']) : '';

		if ($install_to == '')
		{
			trigger_error('请指定要安装的风格');
		}

		if (!file_exists(ROOT_PATH. 'styles/' . $install_to . '/install.cfg'))
		{
			trigger_error('您指定的风格没有 install.cfg 文件' . back_link(append_sid('admin_styles.php')));
		}

		// 取得安装风格的信息
		$stylecfg = parse_cfg_file(ROOT_PATH. 'styles/' . $install_to . '/install.cfg');
		
		if ( $stylecfg['name'] == '' || !isset($stylecfg['name']))
		{
			$style_name = '无名风格' . rand(1234, 5678);
			// 或
			//continue;
		} else $style_name = $stylecfg['name'];

		if (!isset($stylecfg['copyright'])) $copyright = '无名氏' . rand(1234, 5678);
		else $copyright = $stylecfg['copyright'];

		if (!isset($stylecfg['version']))
		{
			$version = '无';
		} else $version = $stylecfg['version'];
		
		$sql = 'SELECT style_id 
			FROM ' . STYLES_TABLE . " 
			WHERE style_path = '" . $db->sql_escape($install_to) . "'";
			
		if(!$result = $db->sql_query($sql))
		{
			trigger_error('无法获取风格的信息', E_USER_WARNING);
		}
		
		if ($db->sql_fetchrow($result))
		{
			trigger_error('风格已安装' . back_link('admin_styles.php'));
		}
		
		$sql = "INSERT INTO " . STYLES_TABLE . " (style_name, style_path, style_version, style_copyright) 
			VALUES ('" . $db->sql_escape($style_name) . "', '" . $db->sql_escape($install_to) . "', '" . $db->sql_escape($version) . "', '" . $db->sql_escape($copyright) . "')";
		
		if( !$db->sql_query($sql) )
		{
			trigger_error('无法安装风格', E_USER_WARNING);
		}
		
		$style->Clear();

		trigger_error('安装成功！' . back_link(append_sid('admin_styles.php')));
		break;
		
	default:

		// 列出已安装的风格
		$install_style_path = array();
		$i = 0;
		
		foreach ($style->data as $style_id => $style_value)
		{
			$row_class = ( !($i % 2) ) ? 'row1' : 'row2';

			$template->assign_block_vars('styles', array(
				'ROW_CLASS' 		=> $row_class,
				'L_NUMBER'			=> ($style_id == $board_config['default_style']) ? '√' : ($i+1),
				'STYLE_NAME'		=> $style_value['name'],
				'STYLE_PATH'		=> $style_value['path'],
				'STYLE_COPYRIGHT' 	=> $style_value['copy'],
				'STYLE_VERSION'		=> $style_value['version'],
				'U_DEFAULT_STYLE'	=> append_sid('admin_styles.php?mode=default&path=' . $style_value['path']),
				'U_DOWNLOAD'		=> append_sid('admin_styles.php?mode=download&path=' . $style_value['path']),
				'U_ZIP'				=> append_sid('admin_styles.php?mode=zip&path=' . $style_value['path']),
				'U_STYLE_UNINSTALL' => append_sid('admin_styles.php?mode=uninstall&path=' . $style_value['path']))
			);

			$install_style_path[$style_value['path']] = $style_value['path'];
			$i++;
		}
		
		// 列出未安装的风格
		$notinstall_styles = array();
		if( $dir = @opendir(ROOT_PATH . 'styles/') )
		{
			while( $sub_dir = @readdir($dir) )
			{
				if( !is_file(phpbb_realpath(ROOT_PATH . 'styles/' .$sub_dir)) && !is_link(phpbb_realpath(ROOT_PATH . 'styles/' . $sub_dir)) && $sub_dir != '.' && $sub_dir != '..' && $sub_dir != 'index.htm' )
				{
					if( @file_exists(@phpbb_realpath(ROOT_PATH. 'styles/' . $sub_dir . '/install.cfg')) )
					{

						$stylecfg = parse_cfg_file(ROOT_PATH. 'styles/' . $sub_dir . '/install.cfg');

						
						if ( $stylecfg['name'] == '' || !isset($stylecfg['name']))
						{
							$style_name = '无名风格' . rand(1234, 5678);
							// 或
							//continue;
						} else $style_name = $stylecfg['name'];

						if (!isset($stylecfg['copyright']))
						{
							$copyright = '无名氏' . rand(1234, 5678);
						} else $copyright = $stylecfg['copyright'];
					
						if (!isset($stylecfg['version']))
						{
							$version = '无';
						} else $version = $stylecfg['version'];


						if (!isset($install_style_path[$sub_dir]))
						{
							$notinstall_styles[] = array(
								'name' => $style_name,
								'path' => $sub_dir,
								'version' => $version,
								'copy' => $copyright
							);
						}					
					}
				}
			}
		}

		$j = 0;
		foreach ($notinstall_styles as $key => $styleinfo)
		{
			$row_class 	= ( !($i % 2) ) ? 'row1' : 'row2';
			
			$number 	= $j + 1;
			
			$template->assign_block_vars('notinstall_styles', array(
				'ROW_CLASS' 		=> $row_class,
				'NUMBER' 			=> $number,
				'STYLE_NAME' 		=> $styleinfo['name'],
				'STYLE_PATH' 		=> $styleinfo['path'],
				'STYLE_COPYRIGHT' 	=> $styleinfo['copy'],
				'STYLE_VERSION'		=> $styleinfo['version'],
				'U_DOWNLOAD'		=> append_sid('admin_styles.php?mode=download&path=' . $styleinfo['path']),
				'U_ZIP'				=> append_sid('admin_styles.php?mode=zip&path=' . $styleinfo['path']),
				'U_STYLES_INSTALL' 	=> append_sid('admin_styles.php?mode=install&path=' . $styleinfo['path']),
				'U_STYLES_DELETE'	=> append_sid('admin_styles.php?mode=delete&path=' . $styleinfo['path']))
			);

			$j++;
		}

		$template->assign_vars(array(
			'S_UPLOAD' => append_sid('admin_styles.php?mode=upload'),
			'U_BACK' => append_sid('admin_styles.php'))
		);

		$template->set_filenames(array(
			'body' => 'admin/styles_list_body.tpl')
		);
		
		$template->pparse('body');

}

page_footer();
?>