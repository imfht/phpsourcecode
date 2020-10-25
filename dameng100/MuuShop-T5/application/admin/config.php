<?php

//配置文件
return [
    // 视图输出字符串内容替换,留空则会自动进行计算
    'view_replace_str'       => [
        '__STATIC__'    => '/static',
        '__COMMON__'    => '/static/common',
        '__ZUI__'       => 'https://cdn.bootcss.com/zui/1.8.1',   
        '__JS__'    	=> '/static/admin/js',
        '__IMG__'       => '/static/admin/images',
        '__CSS__'       => '/static/admin/css',
        '__LIB__'       => '/static/admin/lib',
        '__PLUGIN__'    => '/static/admin/plugin',   
    ],
];
