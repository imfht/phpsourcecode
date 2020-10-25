<?php
/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    
    /* 主题设置 */
    'DEFAULT_THEME' =>  '',  // 默认模板主题名称

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'    => 'sent_', // 缓存前缀
    'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型
    'URL_MODEL'            => 3, //URL模式


    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__ADDONS__' => __ROOT__ . '/Addons',
        '__STATIC__' => __ROOT__ . '/Application/' . MODULE_NAME . 'Static',
        '__IMG__'    => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/images',
        '__CSS__'    => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/css',
        '__JS__'     => __ROOT__ . '/Application/' . MODULE_NAME . '/Static/js',
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'sent_admin', //session前缀
    'COOKIE_PREFIX'  => 'sent_admin_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug

    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR'     =>  MODULE_PATH.'View/Public/error.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  MODULE_PATH.'View/Public/success.html', // 默认成功跳转对应的模板文件
);