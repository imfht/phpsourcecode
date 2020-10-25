<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
 * HDPHP框架入口文件
 * 在应用入口引入hdphp.php即可运行框架
 * @package hdphp
 * @supackage core
 * @author hdxj <houdunwangxj@gmail.com>
 */
define('HDPHP_VERSION', '2014-12-21');
defined("DEBUG")        	or define("DEBUG", FALSE);//调试模式
defined("DEBUG_TOOL")       or define("DEBUG_TOOL", FALSE);//调试工具
defined('APP_PATH') 		or define('APP_PATH', './Application/');//应用目录
defined('TEMP_PATH')    	or define('TEMP_PATH', APP_PATH. 'Temp/');
defined('TEMP_FILE')    	or define('TEMP_FILE',TEMP_PATH.'~Boot.php');//编译文件
//加载核心编译文件
if (!DEBUG and is_file(TEMP_FILE)) {
    require TEMP_FILE;
} else {
    //编译文件
    define('HDPHP_PATH', str_replace('\\','/',dirname(__FILE__)) . '/');
    require HDPHP_PATH . 'Lib/Core/Boot.class.php';
    Boot::run();
}