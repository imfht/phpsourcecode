<?php
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/image',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
    'URL_HTML_SUFFIX'=>'',
    /* 数据库设置 */
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_NAME'               => 'taocms',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => '123456',          // 密码
    'DB_PORT'               => '3306',          // 端口
    'DB_PREFIX'             => 'tao_',    // 数据库表前缀
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DB_MASTER_NUM'         => 1,           // 读写分离后，主服务器数量
    'AUTH_CONFIG'=>array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为实时认证；2为登录认证。
        'AUTH_GROUP' => 'tao_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'tao_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'tao_auth_rule', //权限规则表
        'AUTH_USER' => 'tao_user'//用户信息表
    ),
    'SITE_CONF'  => array(
        'SITE_NAME'    => 'TAOBMS',
        'DOMAIN_NAME'  => 'taocms.com',),
);