<?php
return [
    'system' => [
        'common_dir' => 'common',
    ],
    'project' => [
        'mode' => 0, //0单模块 1多模块 2多版本
    ],
    'route' => [
        'mode' => 0, //0单级 1分级
    ],
    'url' => [
        'c' => 'Index', //默认控制器
        'a' => 'index', //默认操作
        'mode' => 1, // 0 普通模式; 1PATHINFO模式; 2REWRITE模式; 3兼容模式;
        'ext' => '/', //结束符
    ],
    'cache' => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path' => ROOT_PATH . 'cache' . DS,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    'config' => [
        'path' => ROOT_PATH . 'config' . DS
    ],
    'log' => [
        'record' => true,
        'path' => ROOT_PATH . 'logs' . DS,
    ],
    'session' => [
        'name' => 'TMP',
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'timo',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    'cli' => [
        'entry_path' => ROOT_PATH . 'cli.php',
    ],
    'default_app' => 'web',
    'var_jsonp_callback' => '__callback',
    'default_jsonp_handler' => 'jsonp_handler',
    'default_return_type' => 'html',
    // 默认跳转页面对应的模板文件
    'jump_success_tpl'  => FRAME_PATH . 'tpl' . DS . 'jump.tpl.php',
    'jump_error_tpl'    => FRAME_PATH . 'tpl' . DS . 'jump.tpl.php',
];
