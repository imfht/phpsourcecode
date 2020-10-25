<?php

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(

   
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
     'URL_MODEL' => 0, // 如果你的环境不支持PATHINFO 请设置为3
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符  

    'AUTOLOAD_NAMESPACE' => array('Addons' => ZS_ADDON_PATH), //扩展模块列表
    'MODULE_ALLOW_LIST'   => array('Home','Admin'),
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common', 'User'),
    'VAR_PAGE' => 'pageNum',
    'PAGE_LISTROWS' => 10, //分页 每页显示多少条
    'PAGE_NUM_SHOWN' => 10, //分页 页标数字多少个
    'TMPL_ACTION_ERROR' => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:success', // 默认成功跳转对应的模板文件
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'USER_AUTH_GATEWAY' => '/Admin/Public/login', // 默认认证网关
    'NOT_AUTH_MODULE' => 'Public,Index', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    
    'RBAC_ROLE_TABLE' => '[DB_PREFIX]role',
    'RBAC_USER_TABLE' => '[DB_PREFIX]role_user',
    'RBAC_ACCESS_TABLE' => '[DB_PREFIX]access',
    'RBAC_NODE_TABLE' => '[DB_PREFIX]node',
    'SHOW_PAGE_TRACE' => false, //显示调试信息

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static'
    ),
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,WARN', // 只记录EMERG ALERT CRIT ERR 错误

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE'   => '[DB_TYPE]', // 数据库类型
    'DB_HOST'   => '[DB_HOST]', // 服务器地址
    'DB_NAME'   => '[DB_NAME]', // 数据库名
    'DB_USER'   => '[DB_USER]', // 用户名
    'DB_PWD'    => '[DB_PWD]',  // 密码
    'DB_PORT'   => '[DB_PORT]', // 端口
    'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀

    

);
