<?php
/**
 * cache.php
 * @since   2016-08-29
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
return [
    'DATA_CACHE_TYPE'     => 'redis', // 数据缓存类型
    'DATA_CACHE_PREFIX'   => 'AppManage:', // 键前缀
    'AUTH_ON'             => true,                    //认证开关
    'AUTH_TYPE'           => 0,                       //认证方式，0为时时认证；1为登录认证[Cache缓存]；2为登录认证[SESSION缓存]。
    'AUTH_GROUP'          => 'AuthGroup',             //用户组数据表名
    'AUTH_GROUP_ACCESS'   => 'AuthGroupAccess',       //用户组明细表
    'AUTH_RULE'           => 'AuthRule',              //权限规则表
    'AUTH_USER'           => 'User'                   //用户信息表
];