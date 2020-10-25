<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

defined('IS_IN') or die('Include Error!');
define('RC_PATH_CFG', RECHO_PHP . 'Conf/');			//全局配置路径
define('RC_PATH_LIB', RECHO_PHP . 'Lib/Recho/');	//全局公用库
define('RC_PATH_KEL', RECHO_PHP . 'Lib/Model/');	//全局核心
define('APP_PATH', RECHO_PHP . '../apps/');			//应用目录
define('IS_SERVER', false);							//是否线上

ob_start();
set_time_limit(10);
date_default_timezone_set('Asia/Shanghai');
error_reporting(IS_SERVER ? 0 : E_ALL ^ E_NOTICE);		//线下测试
include_once RC_PATH_LIB . 'function.functions.php';	//公用函数
include_once RC_PATH_CFG . 'inc.base.php';				//装载配置文件
include_once RC_PATH_KEL . 'class.Odb.php';				//分布式DB
include_once RC_PATH_LIB . 'cache/class.Ocache.php';	//data cache
include_once RC_PATH_KEL . 'class.Cache.php';			//data cache
include_once RC_PATH_KEL . 'class.rc.php';				//核心入口
include_once RC_PATH_LIB . 'class.Functions.php';		//公用函数
include_once RC_PATH_LIB . 'class.Image.php';			//图像处理类
rc::setConfig( $construct);								//开始初始化
define( 'EXT', '.php');
require_once( RECHO_PHP . "Lib/Base/front.php");
require_once RECHO_PHP.'Lib/Base/controller.php';
$front = FrontController::getInstance();

//initialization route
if($_REQUEST['act'] != 'xml'){
	@ob_start("ob_gzip");
}
