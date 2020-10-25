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

define('IN_PHPBB', true);
	
$no_page_header = false;

define('ROOT_PATH', './../');

require('pagestart.php');

if ( $board_config['guide_progress']  == -1)
{
	trigger_error('您已经完成了设置向导！', E_USER_ERROR);
}
else
{
	$progress = $board_config['guide_progress'];
}
$mode = ( isset($_GET['mode']) ) ? intval($_GET['mode']) : 0;

//删除安装目录
if ( $progress == 1 )
{
	$install_dir = './../install';
	
	//如果安装文件目录不存在则直接进入下一步
	if (!file_exists($install_dir))
	{
		set_config('guide_progress', 2);
		$cache->clear('global_config');
		trigger_error('安装目录已不存在或已被更名！点击 <a href="' . append_sid('guide.php') . '">这里</a> 进入下一步', E_USER_ERROR);
	}
	
	if( !phpbb_deldir($install_dir) )
	{ 
		trigger_error('删除失败！', E_USER_ERROR);
	}
	else
	{
		set_config('guide_progress', 2);
		$cache->clear('global_config');
	}
	
	//执行删除install文件夹的代码
	$template->set_filenames(array(
		'body' => 'admin/guide_delete_install.tpl')
	);
	$template->assign_vars(array(
		'U_NEXT_2' => append_sid('guide.php'))
	);
}
else if ( $progress == 2 )
{
	$submit = isset($_POST['submit']) ? true :false;
	if( $submit )
	{
		if ( (($_FILES['logo']['type'] == 'image/png') || ($_FILES['logo']['type'] == 'image/gif') || ($_FILES['logo']['type'] == 'image/jpeg') || ($_FILES['logo']['type'] == 'image/jpg') || ($_FILES['logo']['type'] == 'image/pjpeg')) && ($_FILES['logo']['size'] < 2097152) )
		{
			if ($_FILES['logo']['error'] > 0)
			{
				// trigger_error('文件上传出错：' . $_FILES['logo']['error'], E_USER_WARNING);
				trigger_error('文件上传出错：' . $_FILES['logo']['error'], E_USER_ERROR);
			}
			else
			{
				if ($_FILES['logo']['type'] == 'image/png')
				{
					$logo_name = 'logo.png';
				}
				else if($_FILES['logo']['type'] == 'image/gif')
				{
					$logo_name = 'logo.gif';
				}
				else if($_FILES['logo']['type'] == 'image/jpeg' || $_FILES['logo']['type'] == 'image/jpg' || $_FILES['logo']['type'] == 'image/pjpeg')
				{
					$logo_name = 'logo.jpg';
				}
				else
				{
					$logo_name = 'logo.jpg';
				}
				move_uploaded_file($_FILES['logo']['tmp_name'], '../images/' . $logo_name);
				set_config('site_logo', $logo_name);
				set_config('guide_progress', 3);
				$cache->clear('global_config');
				trigger_error('Logo上传成功！点击 <a href="' . append_sid('guide.php?progress=3') . '">这里</a> 进入下一步', E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('请检查图片类型和大小是否正确！', E_USER_ERROR);
		}
	}
	else
	{
		$template->set_filenames(array(
			'body' => 'admin/guide_upload_logo.tpl')
		);
		$template->assign_vars(array(
			'S_UPLOAD_ACTION' => append_sid('guide.php'))
		);
	}
}
else if ($progress == 3 )
{
	$submit = isset($_POST['submit']) ? true :false;
	if( $submit )
	{
		
		$site_desc = (isset($_POST['site_desc'])) ? $_POST['site_desc'] : '';
		$board_email = (isset($_POST['board_email'])) ? $_POST['board_email'] : '';
		if ( $board_email != '@' && !filter_var($board_email, FILTER_VALIDATE_EMAIL) )
		{
			trigger_error('E-Mail地址不正确！', E_USER_ERROR);
		}
		$smtp_host = (isset($_POST['smtp_host'])) ? $_POST['smtp_host'] : '';
		$smtp_username = (isset($_POST['smtp_username'])) ? $_POST['smtp_username'] : '';
		$smtp_password = (isset($_POST['smtp_password'])) ? $_POST['smtp_password'] : '';
		$smtp_delivery = (isset($_POST['smtp_delivery'])) ? $_POST['smtp_delivery'] : 0;
		
		$config_array = array(
			'site_desc' 		=> $site_desc,
			'board_email' 		=> $board_email,
			'smtp_host' 		=> $smtp_host,
			'smtp_username' 	=> $smtp_username,
			'smtp_password' 	=> $smtp_password,
			'smtp_delivery' 	=> $smtp_delivery,
			'guide_progress' 	=> -1,
		);
		
		foreach($config_array as $name => $value)
		{
			set_config($name, $value);
		}
		set_config('guide_progress', -1);
		
		$cache->clear('global_config');
		trigger_error('设置向导已完成！点击 <a href="../index.php">这里</a> 开始您的网站生涯吧！', E_USER_ERROR);
	}
	else
	{
		$template->set_filenames(array(
			'body' => 'admin/guide_setup.tpl')
		);
		$template->assign_vars(array(
			'S_SETUP_ACTION' => append_sid('guide.php'))
		);	
	}
	
}
//向导页面
else
{
	set_config('guide_progress', 1);
	$cache->clear('global_config');

	$template->set_filenames(array(
		'body' => 'admin/guide_body.tpl')
	);

	$template->assign_vars(array(
		'U_NEXT_1' => append_sid('guide.php'))
	);
}
$template->pparse('body');

page_footer();
?>