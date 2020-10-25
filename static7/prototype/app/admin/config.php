<?php

//配置文件
return [
    'user_administrator' => 1, //管理员用户ID
    // 视图输出字符串内容替换
    'replace_str' => [
        '__JS__' => TPL_PATH . 'admin/js',
        '__CSS__' => TPL_PATH . 'admin/css',
        '__STATIC__' => TPL_PATH . 'static',
        '__IMG__' => TPL_PATH . 'admin/images',
        '__UPLOAD__' => TPL_PATH . 'upload/default',
    ],
    'auth_config' => [
        'auth_on' => true, // 认证开关
        'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group' => 'auth_group', // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule' => 'auth_rule', // 权限规则表
        'auth_user' => 'member', // 用户信息表
        'type_admin' => 1, //管理员用户组类型标识
        'ucenter_member' => 'ucenter_member',
        'auth_extend' => 'auth_extend', //动态权限扩展信息表
        'auth_extend_category_type' => 1, //分类权限标识
        'auth_extend_model_type' => 2//模型权限标识
    ],
    //权限规则设定
    'auth_rule' => [
        'rule_url' => 1, //url
        'rule_main' => 2, //主要节点
    ],
    'picture_path' => '/upload/default/picture/', //默认图片保存路径
    'file_path' => '/upload/default/file/', //默认图片保存路径
    'portrait_path' => '/upload/head_portrait/', //头像保存路劲
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => getcwd() . '/jump.html',
    'dispatch_error_tmpl' => getcwd() . '/jump.html',
    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'document_model_type' => [1 => '目录', 2 => '主题'],
    /* 文件限制 */
    'file_upload_restrict' => [
        'size' => 5 * 1024 * 1024, //上传的文件大小限制
        'ext' => 'zip,7z,chm,tar.gz,rar,xls,doc,ppt,xlsx,docx,pptx,rm,rmvb,wmv,avi,mp4,mp3,3gp,mkv', //允许上传的文件后缀
    ],
    /* 图片上传限制 */
    'picture_upload_restrict' => [
        'size' => 2 * 1024 * 1024, //上传的文件大小限制
        'ext' => 'gif,jpg,jpeg,bmp,png,swf,fla,flv', //允许上传的文件后缀
    ],
];
