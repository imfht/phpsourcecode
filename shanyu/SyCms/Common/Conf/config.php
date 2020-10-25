<?php

return  array(

    /* 00-注册命名空间*/
    'AUTOLOAD_NAMESPACE' => array(
        'Common' => COMMON_PATH,//Common 移动根目录
        'Lib'   => COMMON_PATH . 'Lib',//lib 第三方类库
    ),

    /* 01-应用设定 */
    'APP_SUB_DOMAIN_DEPLOY' =>  false,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  =>  array(
        'www'        => 'Home',
        'user'       => 'User',
    ), // 子域名部署规则
    'MODULE_DENY_LIST'      =>  array('Common','Runtime'),
    'MODULE_ALLOW_LIST'     =>  array('Home','Admin','Mobile','User','Api','Demo'),


    /* 02-Cookie设置 */
    'COOKIE_PREFIX'         =>  'sy_',      // Cookie前缀 避免冲突

    /* 03-默认设定 */
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块

    /* 04-数据库设置 */
    'DB_DEBUG'              =>  false, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,  // 启用字段缓存

    /* 05-数据缓存设置 */
    'DATA_CACHE_PREFIX'     =>  'sy_',     // 缓存前缀

    /* 06-错误设置 */
    'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息

    /* 07-日志设置 */
    'LOG_RECORD'            =>  false,   // 默认不记录日志

    /* 08-SESSION设置 */
    'SESSION_PREFIX'        =>  'sy_', // session 前缀
    
    /* 09-模板引擎设置 */
    'TMPL_ACTION_ERROR'     =>  COMMON_PATH.'View/Public/jump.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  COMMON_PATH.'View/Public/jump.html', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  COMMON_PATH.'View/Public/exception.html',// 异常页面的模板文件

    /* 12-URL设置 */
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：

    /* 14-综合配置 */
    'HTTP_CACHE_CONTROL'    =>  'private',  // 网页缓存控制 public,private,no-cache,no-store

    /* 加载扩展配置 */
    'LOAD_EXT_CONFIG' => 'db',

);
