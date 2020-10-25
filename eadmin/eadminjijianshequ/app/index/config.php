<?php
return [


    'view_suffix' => 'html',
    'view_depr'   => '/',
    //'taglib_pre_load'     =>    'app\common\taglib\Article',


    'view_replace_str'      => [
        '__ROOT__'       => WEB_URL,
        '__INDEX__'      => WEB_URL . '/index.php',
        '__HOME__'       => WEB_URL . '/template/' . webconfig('site_tpl') . '/res',
        '__JS__'         => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/js',
        '__CSS__'        => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/css',
        '__IMG__'        => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/images',
        '__WAPJS__'      => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/wap/js',
        '__WAPCSS__'     => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/wap/css',
        '__WAPIMG__'     => WEB_URL . '/template/' . webconfig('site_tpl') . '/res/wap/images',
        '__UPLOAD__'     => WEB_URL . '/uploads',
        '__PUBLIC__'     => WEB_URL . '/public',
        '__PUBLIC_IMG__' => WEB_URL . '/public/images',

    ],
    //默认错误跳转对应的模板文件
    'dispatch_error_tmpl'   => 'Public/error',
    //默认成功跳转对应的模板文件
    'dispatch_success_tmpl' => 'Public/error',

];