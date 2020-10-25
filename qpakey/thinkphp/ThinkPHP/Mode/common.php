<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
      
/**
 * ThinkPHP 普通模式定义
 */
return array(
    // 配置文件
    'config'    =>  array(
        THINK_PATH.'Conf/convention.php',   // 系统惯例配置
        CONF_PATH.'config.php',      // 应用公共配置
    ),

    // 函数和类文件
    'core'      =>  array(
        THINK_PATH.'Common/functions.php',
        COMMON_PATH.'Common/function.php',
    ),

	// 需要编译的文件列表
	'build'=>array(
		CORE_PATH . 'Hook'.EXT,
		CORE_PATH . 'App'.EXT,
		CORE_PATH . 'Dispatcher'.EXT,
		//CORE_PATH . 'Log'.EXT,
		CORE_PATH . 'Route'.EXT,
		CORE_PATH . 'Controller'.EXT,
		CORE_PATH . 'View'.EXT,
		CORE_PATH . 'Behavior'.EXT,
	),
    // 行为扩展定义
    'tags'  =>  array(
        'app_init'      =>  array(
        ),
        'app_begin'     =>  array(
            'Behavior_ReadHtmlCacheBehavior', // 读取静态缓存
        ),
        'app_end'       =>  array(
            'ShowPageTraceBehavior', // 页面Trace显示
        ),
        'path_info'     =>  array(),
        'action_begin'  =>  array(),
        'action_end'    =>  array(),
        'view_begin'    =>  array(),
        'view_parse'    =>  array(
            'ParseTemplateBehavior', // 模板解析 支持PHP、内置模板引擎和第三方模板引擎
        ),
        'template_filter'=> array(
            'ContentReplaceBehavior', // 模板输出替换
        ),
        'view_filter'   =>  array(
            'WriteHtmlCacheBehavior', // 写入静态缓存
        ),
        'view_end'      =>  array(),
    ),
);
