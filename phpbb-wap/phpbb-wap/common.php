<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/


if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

if (!defined('E_DEPRECATED'))
{
	define('E_DEPRECATED', 8192);
}

$level = E_ALL & ~E_NOTICE & ~E_DEPRECATED;

if (version_compare(PHP_VERSION, '5.4.0-dev', '>='))
{
	if (!defined('E_STRICT'))
	{
		define('E_STRICT', 2048);
	}
	$level &= ~E_STRICT;
}

error_reporting($level);

/*
* Remove variables created by register_globals from the global scope
* Thanks to Matt Kavanagh
*/
function deregister_globals()
{
	$not_unset = array(
		'GLOBALS'	=> true,
		'_GET'		=> true,
		'_POST'		=> true,
		'_COOKIE'	=> true,
		'_REQUEST'	=> true,
		'_SERVER'	=> true,
		'_SESSION'	=> true,
		'_ENV'		=> true,
		'_FILES'	=> true
	);

	// Not only will array_merge and array_keys give a warning if
	// a parameter is not an array, array_merge will actually fail.
	// So we check if _SESSION has been initialised.
	if (!isset($_SESSION) || !is_array($_SESSION))
	{
		$_SESSION = array();
	}

	// Merge all into one extremely huge array; unset this later
	$input = array_merge(
		array_keys($_GET),
		array_keys($_POST),
		array_keys($_COOKIE),
		array_keys($_SERVER),
		array_keys($_SESSION),
		array_keys($_ENV),
		array_keys($_FILES)
	);

	foreach ($input as $varname)
	{
		if (isset($not_unset[$varname]))
		{
			// Hacking attempt. No point in continuing unless it's a COOKIE (so a cookie called GLOBALS doesn't lock users out completely)
			if ($varname !== 'GLOBALS' || isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_SERVER['GLOBALS']) || isset($_SESSION['GLOBALS']) || isset($_ENV['GLOBALS']) || isset($_FILES['GLOBALS']))
			{
				exit;
			}
			else
			{
				$cookie = &$_COOKIE;
				while (isset($cookie['GLOBALS']))
				{
					if (!is_array($cookie['GLOBALS']))
					{
						break;
					}

					foreach ($cookie['GLOBALS'] as $registered_var => $value)
					{
						if (!isset($not_unset[$registered_var]))
						{
							unset($GLOBALS[$registered_var]);
						}
					}
					$cookie = &$cookie['GLOBALS'];
				}
			}
		}

		unset($GLOBALS[$varname]);
	}

	unset($input);
}

if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get'))
{
	date_default_timezone_set(@date_default_timezone_get());
}

// 魔术引号在 PHP 5.3.0 起废弃并将自 PHP 5.4.0 起移除。
if (version_compare(PHP_VERSION, '5.4.0-dev', '>='))
{
	define('MAGIC_QUOTES', false);
}
else
{
	// 尝试关闭魔术引号
	@set_magic_quotes_runtime(0);
	
	// Be paranoid with passed vars
	if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on' || !function_exists('ini_get'))
	{
		deregister_globals();
	}

	define('MAGIC_QUOTES', (get_magic_quotes_gpc()) ? true : false);
}

// 页面的执行时间类
require(ROOT_PATH . 'includes/functions/runtime.php');

$starttime = start_runtime();

$board_config 		= array();
$userdata 			= array();
$nav_links 			= array();
$images 			= array();
$gen_simple_header 	= FALSE;

@include(ROOT_PATH . 'config.php');

// 转到安装
if( !defined('PHPBB_INSTALLED') )
{
	header('Location: ' . ROOT_PATH . 'install/install.php');
	exit;
}

//常量
require(ROOT_PATH . 'includes/constants.php');

//模版解析
require(ROOT_PATH . 'includes/class/template.php');

//session
require(ROOT_PATH . 'includes/class/session.php');

//权限
require(ROOT_PATH . 'includes/functions/auth.php');

//常用函数
require(ROOT_PATH . 'includes/functions/common.php');

// 自定义的错误处理
set_error_handler('error_message');

//数据库
require(ROOT_PATH . 'includes/class/mysql.php');

$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);

if(!$db->db_connect_id)
{
	die('<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>提示</title><style type="text/css">@charset "utf-8";*{margin:0;padding:0;}body{margin:0 auto;max-width:640px;font-family:"Century Gothic","Microsoft yahei";background-color:#F9F9F9;}#wrap{background-color:#FFF;width:640px;}.error{padding:20px;margin:0;border-style:solid;border-width:1px;border-color:#000;}.main{padding:115px 0 6px 0;}</style></head><body><div id="wrap"><div class="main"><div class="error"><p style="color:red;">无法链接到数据库，请检查您的数据库配置文件是否正确</p></div></div><div></body></html>');
}

$db->sql_query('SET NAMES utf8');

// 为了安全起见，注销数据库密码这个变量
unset($dbpasswd);

// 引入缓存功能
// 虽然目前的缓存系统比较弱
require(ROOT_PATH . 'includes/class/cache.php');
$cache = new cache();
$board_config = $cache->export('global_config');

$session = new Session();

// 获取用户Agent、IP
$http_user_agent 	= (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : getenv('HTTP_USER_AGENT');
if( strpos($http_user_agent, 'MSIE') ){
	$user_agent = 'Internet Explorer';
}else if( strpos($http_user_agent, 'Firefox') ){
	$user_agent = 'Firefox';
}else if( strpos($http_user_agent, 'Chrome') ){
	$user_agent = 'Chrome';
}else if( strpos($http_user_agent, 'Safari') ){
	$user_agent = 'Safari';
}else if( strpos($http_user_agent, 'Opera') ){
	$user_agent = 'Opera';
}else{
	$user_agent = strtok($http_user_agent,'/');
}
	
$client_ip 		= ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
$user_ip 		= encode_ip($client_ip, false);

if( $board_config['board_disable'] && !defined("IN_ADMIN") && !defined("IN_LOGIN") )
{
	// 随你喜欢
	// trigger_error('对不起, 该论坛暂时不可用, 请稍候重试', E_USER_ERROR);
	trigger_error('对不起, 该论坛暂时不可用, 请稍候重试', E_USER_WARNING);
}
?>