<?php

//配置文件
return [
    'app_trace' =>  true,
    // 视图输出字符串内容替换
    'replace_str' => [
        '__JS__' => TPL_PATH . 'home/js',
        '__CSS__' => TPL_PATH . 'home/css',
        '__STATIC__' => TPL_PATH . 'static',
        '__IMG__' => TPL_PATH . 'home/images',
        '__UPLOAD__' => TPL_PATH . 'upload/default',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
];
