<?php
// 后台配置文件
// +----------------------------------------------------------------------
// | PHP version 5.3+
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
return [
    // +----------------------------------------------------------------------
    // | 安全设置
    // +----------------------------------------------------------------------

    // 后台默认认证网关
    'user_auth_gateway'  => 'Admin/Login/index',
    // 管理员用户key
    'user_adminauth_key' => 'admin|15210455141',
    // 超级管理员用户ID
    'user_administrator' => [1],
    // SESSION识别标识
    'user_auth_session'  => 'user_auth_id',
    // 是否开启测试数据操作
    'show_testdata'      => false,
    // 认证开关
    'AUTH_ON'            => true,
    // 认证方式，1为实时认证；2为登录认证。
    'AUTH_TYPE'          => 1,
    // 用户组数据表名
    'AUTH_GROUP'         => 'admin_auth_group',
    // 用户-用户组关系表
    'AUTH_GROUP_ACCESS'  => 'admin_auth_group_access',
    // 权限规则表
    'AUTH_RULE'          => 'admin_auth_rule',
    // 用户信息表
    'AUTH_USER'          => 'admin_member',
    // 禁用IP访问列表
    'admin_allow_ip'     =>'',
];