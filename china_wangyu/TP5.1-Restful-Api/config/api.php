<?php

return[
    // 是否开启授权验证
    'API_AUTHORIZATION' => false,

    // 授权验证类型， 默认类型：HS256。类型map[HS256,RS256]
    "API_AUTHORIZATION_TYPE" => 'HS256',

    // 授权key
    "API_AUTHORIZATION_KEY" => md5('HS256'),

    // 授权token模板
    "API_AUTHORIZATION_TOKEN" => [
        "iss" => "http://www.example.org", //签发者
        "aud" => "http://www.example.com", //jwt所面向的用户
        "iat" => 1356999524, //签发时间
        "nbf" => 1357000000,  //在什么时间之后该jwt才可用
        'exp' => 1357000000 + 600, //过期时间-10min
        'data' => [  // 角色授权数据
        ]
    ],

    # API 同一个IP每小时请求上限
    "API_HOUR_REQUEST_COUNT" => 15,

    # API 请求溢出时间
    "API_REQUEST_EXP" => 60,



    # 设置API 网址，{本机IP}
    'API_HOST'=> 'http://127.0.0.1:8000',

    //API模块
    'API_MODULE' => 'api',

    //API版本控制目录
    'API_CONTROLLER' => 'controller',

    //API版本控制
    'API_VERSION' => ['v1'],

    //API生成文档不包含控制器
    'API_IGNORE_CONTROLLER' => [],

    //API生成文档不包含类函数,
    // 注意：以 "__"（两个下划线）开头的类方法都不会被系统加载和验证，以及接口文档输出
    'API_IGNORE_METHOD' => [],

    // API扩展名
    'API_EXT' => '.php',

    // API请求类型对应方法
    'API_METHOD_DEFAULT_ACTION' => [
        'GET' => 'read',
        'POST' => 'save',
        'PUT' => 'update',
        'DELETE' => 'delete',
        'PATCH' => 'patch',
        'HEAD' => 'head',
        'OPTIONS' => 'options',
    ],

    # API ACTION DOCUMENT 接口方法文档定义
    'API_ACTION_DOCUMENT_DEFINE' => [
        'doc' => ['declare','value'], // 定义：方法名称
        'route' => ['declare','route','method'], // 定义：路由
        'param' => ['declare','type','name','doc','rule','default'], // 定义：参数
        'success' => ['declare','value'], // 定义：返回成功名称
        'error' => ['declare','value'], // 定义：返回失败名称
    ]
];