<?php
/**
* @package phpBB-WAP
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
define('IN_MOD', true);
define('ROOT_PATH', './');
require(ROOT_PATH . 'common.php');
	
require(ROOT_PATH . 'includes/functions/mods.php');

if ( isset($_GET['mod']) )
{	
	if ( empty($_GET['mod']) )
	{
		redirect(append_sid('mods.php'));
	}
	else
	{
		define('MOD_PATH', $_GET['mod']);
	}
}
else
{
	redirect(append_sid('mods.php'));
}

// 如果要加载的文件没有使用 $_GET 进行传递，那么将加载mod的默认文件
if ( isset($_GET['load']) )
{
	$load = ( empty($_GET['load']) ) ? MOD_PATH : $_GET['load'];
	define('MOD_LOAD', $load);
}
else
{
	define('MOD_LOAD', MOD_PATH);
}

if ( is_dir(ROOT_PATH . 'mods/' . MOD_PATH) )
{
	$sql = "SELECT mod_power  
		FROM " . MODS_TABLE . " 
		WHERE mod_dir = '" . $db->sql_escape(MOD_PATH) . "'
			AND mod_power = 1";
		
	if ( !$result = $db->sql_query($sql) )
	{
		trigger_error('查询 mods 表失败！', E_USER_WARNING);
	}
	
	if ( !$mod_row = $db->sql_fetchrow($result) )
	{
		redirect(append_sid('mods.php'));
	}
	
	if ( !$mod_row['mod_power'] )
	{
		trigger_error('该 MOD 没有开启，如需要使用此 MOD 请联系管理员！' . back_link(append_sid('mods.php')), E_USER_ERROR);
	}
	
	if( is_string(MOD_PATH) && preg_match("/^[\w\-\/]+$/", MOD_PATH) && file_exists(ROOT_PATH . 'mods/' . MOD_PATH . '/' . MOD_LOAD . '.php') )
	{
		$filename = MOD_LOAD . '.php';

		// 请不要加载后台文件
		if (mods_adminfile($filename))
		{
			redirect(append_sid('mods.php'));
		}
		
		// 请不要加载安装和卸载文件
		if ($filename == 'install.php' || $filename == 'uninstall.php')
		{
			redirect(append_sid('mods.php'));
		}

		$userdata = $session->start($user_ip, PAGE_MODS);
		init_userprefs($userdata, MOD_PATH);
		require(ROOT_PATH . 'mods/' . MOD_PATH . '/' . MOD_LOAD . '.php');
	}
	else
	{
		redirect(append_sid('mods.php'));
	}
}
else
{
	redirect(append_sid('mods.php'));
}

$db->sql_close();

exit;

?>