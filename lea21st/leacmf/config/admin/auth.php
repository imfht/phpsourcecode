<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/30
 * Time: 14:23
 */
return [
    'auth_on'           => 1, // 权限开关
    'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
    'auth_group'        => 'auth_group', // 用户组数据表名
    'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
    'auth_rule'         => 'auth_rule', // 权限规则表
    'auth_user'         => 'admin', // 用户信息表

    //不需要登录的
    'no_need_login_url' => [
        '/public/login'
    ],

    //登录用户不需要验证的
    'allow_visit'       => [
        '/file/upload'
    ]
];