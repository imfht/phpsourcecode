<?php
/*********************************************************************************
 * TookPHP framework
 *-------------------------------------------------------------------------------
 * Homepage: http://www.19www.com
 * Copyright (c) 2015, http://19www.com. All Rights Reserved
 *-------------------------------------------------------------------------------
 * Author: lajox <lajox@19www.com>
 ********************************************************************************/

/**
 * TookPHP框架入口文件
 * @package tookphp
 * @supackage core
 * @author lajox <lajox@19www.com>
 */

define('TOOK_VERSION', '1.0.0'); // 版本信息

// 类文件后缀
const EXT       =   '.class.php';

// 系统常量定义
defined('TOOK_PATH')        or define('TOOK_PATH', str_replace('\\','/',__DIR__) . '/');
defined('ROOT_PATH')        or define('ROOT_PATH', str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']).'/')); //根目录
defined('DEBUG')            or define('DEBUG',      false); // 是否调试模式

// 系统信息
if(version_compare(PHP_VERSION,'5.4.0','<')) {
    ini_set('magic_quotes_runtime',0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()? true : false);
}else{
    define('MAGIC_QUOTES_GPC',false);
}
define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

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
        $_root  =   rtrim(dirname(_PHP_FILE_),'/');
        define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':$_root));
    }
}


defined("DEBUG")        	or define("DEBUG", FALSE);//调试模式
defined("DEBUG_TOOL")       or define("DEBUG_TOOL", FALSE);//调试工具
defined('APP_PATH') 		or define('APP_PATH', './Application/');//应用目录
defined('TEMP_PATH')    	or define('TEMP_PATH', APP_PATH. 'Temp/');
defined('TEMP_FILE')    	or define('TEMP_FILE',TEMP_PATH.'~runtime.php');//编译文件
//加载核心编译文件
if (!DEBUG and is_file(TEMP_FILE)) {
    require_once TEMP_FILE; //编译文件
    Took\TookPHP::init();
    Took\App::run();
} else {
    require TOOK_PATH . 'Library/Took/TookPHP'.EXT;
    Took\TookPHP::run();
}