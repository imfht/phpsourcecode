<?php
return array(
    //后台应用配置 开启layout布局
    'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'Layout/common',
    //加载菜单配置文件
    'LOAD_EXT_CONFIG'       =>  'menu,uploadFile',

    //URL区分大小写
    'URL_CASE_INSENSITIVE'  =>  false,                        // 是否区分url大小写 为TRUE不区分当是必须严格按Think规范书写

    //auth权限管理配置
    'AUTH_CONFIG'            => array(
        'AUTH_USER'      => 'users'                         //用户信息表
    ),
);