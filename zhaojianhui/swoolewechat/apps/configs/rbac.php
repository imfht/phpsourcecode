<?php

$rbac = [
    'authOn'                => false,
    'authType'              => 1, // 认证方式，1为实时认证；2为登录认证。
    'authGroupTable'        => 'sys_user_group', // 用户组数据表名
    'authGroupAccessTable'  => 'sys_user_group_access', // 用户-用户组关系表
    'authRuleTable'         => 'sys_auth_rule', // 权限规则表
    'authUser'              => 'sys_user', // 用户信息表
];

return $rbac;
