<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// ThinkPHP 入口文件
if(!ini_get('session.use_trans_sid')){
	ini_set('session.use_trans_sid',1);
}
// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);
// 记录内存初始使用
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();
// 系统目录定义
defined('THINK_PATH') 	or define('THINK_PATH', dirname(__FILE__).'/');
defined('APP_PATH') 	or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_DEBUG') 	or define('APP_DEBUG',false); // 是否调试模式
define('ROOT', APP_PATH);
$document = str_replace("\\", '/', $_SERVER['PHP_SELF']);
$index_end = substr($document,1);
$index = str_replace('/'.$index_end,'',$document);
$item_end = strstr($document,'/index.php');
$item = str_replace($item_end,'',$document);
define('INDEX', $index);
define('ITEM', $item);
define('DOMAIN',$_SERVER['SERVER_NAME']);


if(defined('ENGINE_NAME')) {
    defined('ENGINE_PATH') or define('ENGINE_PATH',THINK_PATH.'Extend/Engine/');
	require ENGINE_PATH.strtolower(ENGINE_NAME).'.php';
}else{
    defined('RUNTIME_PATH') or define('RUNTIME_PATH',APP_PATH.'Runtime/');
	$runtime = defined('MODE_NAME')?'~'.strtolower(MODE_NAME).'_runtime.php':'~runtime.php';
	defined('RUNTIME_FILE') or define('RUNTIME_FILE',RUNTIME_PATH.$runtime);
	if(!APP_DEBUG && is_file(RUNTIME_FILE)) {
	    // 部署模式直接载入运行缓存
	    require RUNTIME_FILE;
	}else{
	    // 加载运行时文件
	    require THINK_PATH.'Common/runtime.php';
	}	
}
