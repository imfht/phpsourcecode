<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2015, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */
!defined('VGOTFASTER') && exit('Access Deined');

define('VF_VERSION','2.0.0 alpha');
define('VF_VERSION_ID', 20000);

/**
 * VgotFaster Core Process
 *
 * Use single interface file set constant VGOTFASTER,APPLICATION_PATH,SYSTEM_PATH
 * and require this file to finish your VgotFaster Application
 *
 * @created 23:08 2009/8/6
 * @updated 2015/1/16
 */
$started = microtime(true);
/*
	用于存储已载入系统相关内容
	通过此缓存可以在相关内容第二次调用时无需再次载入文件
	CONFIG: 已载入的应用程序配置数组
	MODEL:  已载入的模型列表
	LANGUAGE: 已载入的语言数组
	LOG: 系统运行过程中 () 函数的日志记录，key 为标题, value 为内容
*/
$CONFIG = $MODEL = $LANGUAGE = $LOG = array();

require SYSTEM_PATH.'/Common.php';
require SYSTEM_PATH.'/Base.php';

$Router =& \VF\loadCore('router');
$URI = $Router->analysis(); //通过路由类获取分析得到URI和访问控制器所需的启动参数
unset($Router);

$URI === false && showError404('controller');  //控制器不存在

require SYSTEM_PATH.'/Interface.php';  //控制器及模型继承接口类文件

//加载核心扩展
foreach (array('Controller', 'Model') as $ifName) {
	$ifFile = APPLICATION_PATH.'/core/'.$ifName.'.php';
	is_file($ifFile) && include $ifFile;
}

//加载控制器
include $URI['file'];

//实例化控制器类
$class = '\\Controller\\'.ucfirst(strtolower($URI['controller'])); //For example: [Index] \Controller\Index
if (!class_exists($class)) {
	$class .= 'Controller';
	class_exists($class) || showError404('class');
}

$Controller = new $class;
_systemLog("Initialize Controller '$class'");

//过滤非公开动作列表，并判断动作是否存在
$actions = array_diff(get_class_methods($Controller), array('__getInstance', '__construct', '__ControllerAfterRuntime'));
if (!in_array($URI['action'], $actions)) { //Support php keyword as action like "classAction"
	if (in_array($URI['action'].'Action', $actions)) {
		$URI['action'] = $URI['action'].'Action';
	} else {
		array_unshift($URI['params'], $URI['action']);
		$URI['action'] = '_redirect';
	}
}

_systemLog("Call Action '{$URI['action']}'");

//运行动作
//empty($URI['params']) ? $Controller->$URI['action']() : call_user_func_array(array(&$Controller, $URI['action']), $URI['params']);
call_user_func_array(array(&$Controller, $URI['action']), $URI['params']);

//To run the registered after functions
$Controller->__ControllerAfterRuntime();

//VgotFaster Process End