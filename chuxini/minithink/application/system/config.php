<?php
//配置文件
return [
// 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ROOT__'      =>  request()->root() ,
        '__STATIC__'    =>  request()->root() . '/static/system',
        '__MODULES__'    =>  request()->root() . '/static/system/modules',
        '__CSS__'       =>  request()->root() . '/static/system/css',
        '__IMG__'        =>  request()->root() . '/static/system/images',
        '__JS__'        =>  request()->root() . '/static/system/js',
    ],
];