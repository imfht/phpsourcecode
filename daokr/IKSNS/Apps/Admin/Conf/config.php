<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦
* @Email:810578553@qq.com
*/
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

	/* 数据缓存设置 */
	'DATA_CACHE_PREFIX'    => 'Admin_', // 缓存前缀
	'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型
	
	/* 后台错误页面模板 */
	'TMPL_ACTION_ERROR'     =>  MODULE_PATH.'View/Public/error.html', // 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'   =>  MODULE_PATH.'View/Public/success.html', // 默认成功跳转对应的模板文件
	
	'URL_CASE_INSENSITIVE' => true,
	'URL_MODEL' => 0,
	'VAR_URL_PARAMS' => '',
	'URL_PATHINFO_DEPR' => '/',
	'URL_HTML_SUFFIX' => '',
);
