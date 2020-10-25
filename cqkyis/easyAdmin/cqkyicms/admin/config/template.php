<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板参数替换
    'tpl_replace_string'  =>  [
        '__STATIC__'=>'/static',
        '__JS__' => '/static/admins/js',
        '__CSS__' => '/static/admins/css',
        '__IMG__' => '/static/admins/img',
        '__IMAGES__' => '/static/admins/images',
        '__JSPS__' => '/static/admins/plugins',
        '__OTHER__' => '/static/admins/pages',
        '__UP__' => '/uploads/',
    ],

//    'layout_on'     =>  true,
//    'layout_name'   =>  'layout',
];
