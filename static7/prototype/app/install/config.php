<?php

//配置文件
return [
    // 应用调试模式
    'app_debug' => true,
    'Trace' => [
        //支持Html,Console,Socket 设为false则不显示
        'type' => 'Console',
    ],
    'orginal_table_prefix' => 'tp5_', //默认表前缀
    // 视图输出字符串内容替换
    'replace_str' => [
        '__JS__' => TPL_PATH . 'install/js',
        '__CSS__' => TPL_PATH . 'install/css',
        '__STATIC__' => TPL_PATH . 'static',
        '__IMG__' => TPL_PATH . 'install/images',
        '__UPLOAD__' => TPL_PATH . 'upload/default',
    ],
];
