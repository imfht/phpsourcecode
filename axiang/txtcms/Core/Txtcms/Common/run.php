<?php
/**
 * TXTCMS 框架运行文件
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
defined('INI_TXTCMS') or exit();
if(version_compare(PHP_VERSION,'5.2.0','<')) exit("本系统运行环境要求PHP版本5.2.0及以上！");

define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if(MEMORY_LIMIT_ON) $GLOBALS['_start_memory'] = memory_get_usage();

//项目名称
defined('APP_NAME') or define('APP_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER['PHP_SELF']);
            define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
        }
    }
    if(!defined('__ROOT__')) {
        // 网站URL根目录
        $_root = dirname(_PHP_FILE_);
        define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));
    }
}
//定义路径常量
defined('APP_DEBUG') or define('APP_DEBUG',false);
defined('LIB_PATH') or define('LIB_PATH',TXTCMS_PATH.'Libs/');
defined('TEMPLATE_PATH') or define('TEMPLATE_PATH',LIB_PATH.'Template/');
defined('APPLIB_PATH') or define('APPLIB_PATH',APP_PATH.'Core/');
defined('FUNCTION_PATH') or define('FUNCTION_PATH',APPLIB_PATH.'Functions/');
defined('CONFIG_PATH') or define('CONFIG_PATH',APPLIB_PATH.'Configs/');
defined('TMPL_PATH') or define('TMPL_PATH',APP_PATH.'Template/');
defined('TEMP_PATH') or define('TEMP_PATH',APP_PATH.'Temp/');
defined('LOG_PATH') or define('LOG_PATH',TEMP_PATH.'Logs/');
defined('CACHE_PATH') or define('CACHE_PATH',TEMP_PATH.'Cache/');
defined('DATA_PATH') or define('DATA_PATH',TEMP_PATH.'Data/');
defined('DB_PATH') or define('DB_PATH',TEMP_PATH.'Db/');
defined('SESSION_PATH') or define('SESSION_PATH',TEMP_PATH.'Session/');
defined('TPLCACHE_PATH') or define('TPLCACHE_PATH',CACHE_PATH.'Tplcache/');

//设置session路径
session_save_path(SESSION_PATH);
session_set_cookie_params(24 * 3600);

//调试模式
if(APP_DEBUG){
	@ini_set('display_errors','On');
	error_reporting(E_ALL & ~E_NOTICE);
}else{
	error_reporting(0);
}

//iis7 REQUEST_URI
if(isset($_SERVER['HTTP_X_ORIGINAL_URL'])){
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
}
//iis6 REQUEST_URI
else if(isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}
//加载系统函数库
require TXTCMS_PATH.'Common/function.php';

config(include TXTCMS_PATH.'Configs/config.php');
//设置时区
function_exists('date_default_timezone_set') && date_default_timezone_set (config('DEFAULT_TIMEZONE'));
//创建目录
if(!is_dir(APP_PATH)) mkdir(APP_PATH,0755,true);
if(!is_dir(SESSION_PATH))  mkdir(SESSION_PATH,0755,true);
if(!is_dir(APPLIB_PATH) && is_writeable(APP_PATH)) {
	$dirs=array(
		FUNCTION_PATH, APPLIB_PATH, CONFIG_PATH, TMPL_PATH, TEMP_PATH, LOG_PATH, CACHE_PATH, DATA_PATH, TPLCACHE_PATH, SESSION_PATH, DB_PATH
	);
	foreach ($dirs as $dir){
		if(!is_dir($dir)) mkdir($dir,0755,true);
	}
}
//加载文件
$list=array(
	LIB_PATH.'Sys.class.php',
	LIB_PATH.'Route.class.php',
	LIB_PATH.'Db/db.class.php',
	LIB_PATH.'Db.class.php',
	TEMPLATE_PATH.'smarty/Smarty.class.php',
	LIB_PATH.'Action.class.php',
	LIB_PATH.'View.class.php',
);
//加载系统基础库
foreach ($list as $key=>$file){
	require_load($file);
}
Sys::run();