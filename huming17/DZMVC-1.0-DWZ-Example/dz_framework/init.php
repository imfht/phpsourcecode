<?php

/**
 * 框架加载入口文件
 * @author HumingXu E-mail:huming17@126.com
 */
define('IN_SYSTEM', true);//系统安全入口认证
define('DZ_ROOT', dirname(__FILE__).'/');//框架目录绝对路径
define('SITE_ROOT', substr(dirname(__FILE__), 0, -12));//
define('DZF_ROOT', DZ_ROOT);//框架目录绝对路径

require DZ_ROOT.'source/class/class_core.php';//DEBUG 引入核心文件

//DEBUG 框架函数(数据库操作 模版引擎 以及 数据逻辑处理函数)
require DZ_ROOT.'source/function/function_cache.php';

//DEBUG 框架业务逻辑功能类/函数及系统初始化(可以不在框架入口文件写入)
define('IN_SITE', true);
require SITE_ROOT.'source/function/function_ext.php';//DEBUG ext 引入扩展函数文件
require SITE_ROOT.'source/class/class_init.php';//DEBUG 站点数据初始化类文件
require SITE_ROOT.'source/class/class_ext.php';//DEBUG ext 引入扩展类文件
$site_init = ext::app();
$site_init->init();

/* TODO */