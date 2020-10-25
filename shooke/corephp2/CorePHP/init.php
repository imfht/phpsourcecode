<?php

//验证php版本
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
function_exists('memory_get_usage') && $GLOBALS['_startUseMems'] = memory_get_usage();

const CP_VER = '2.0.2015.0127';//框架版本号,后两段表示发布日期

//框架路径常量定义
$cp_path = dirname(__FILE__);
define('CP_ROOT_PATH', $cp_path.'/');//框架所在目录
define('CP_CORE_PATH', $cp_path.'/Core/');//框架核心文件夹路径
define('CP_LIB_PATH', $cp_path.'/Lib/');//框架附加类库文件夹路径
define('CP_EXT_PATH', $cp_path.'/Ext/');//框架集成第三方类库文件夹路径
define('CP_CONFIG_PATH',dirname(APP_CONFIG).'/');//配置文件目录
//魔术引号检测
if(version_compare(PHP_VERSION,'5.4.0','<')) {
	ini_set('magic_quotes_runtime',0);
	define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}else{
	define('MAGIC_QUOTES_GPC',false);
}

require('Core/App.class.php');//加载应用控制类
\Core\App::run();//执行项目
?>