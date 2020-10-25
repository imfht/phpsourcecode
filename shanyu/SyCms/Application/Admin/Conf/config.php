<?php
return array(

    /* 12-URL设置 */
    'URL_HTML_SUFFIX'       =>  '',  // URL伪静态后缀设置
    'URL_CASE_INSENSITIVE'  =>  false,// 默认false 表示URL区分大小写 true则表示不区分大小写


    /*AUTH权限认证*/
    'AUTH_CONFIG'=>array(
        'AUTH_ON'           => true,                      // 认证开关
        'AUTH_TYPE'         => 2,                         // 认证方式，1为实时认证；2为登录认证。
        'AUTH_GROUP'        => 'sy_auth_group',        // 用户组数据表名
        'AUTH_GROUP_ACCESS' => 'sy_auth_access', // 用户-用户组关系表
        'AUTH_RULE'         => 'sy_auth_rule',         // 权限规则表
        'AUTH_USER'         => 'sy_admin'             // 用户信息表
    ),

	/*超级管理员*/
	'ADMIN_SUPER'=>1,

    //动态配置中Select所需参数
    'CONFIG_GROUPS'=>array(
        1=>'常用配置',
        3=>'手机短信',
        4=>'电子邮箱',
        5=>'综合配置',
        11=>'上传配置',
    ),
    'CONFIG_MODULE'=>array(
        'Common',
        'Admin',
        'Home',
        'User',
    ),

);