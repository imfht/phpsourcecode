<?php

return [
    'web_route'         => 'logs', // 访问路由
    'web_middleware'    => ['web', 'auth'], // 路由中间件
    'web_navbar'        => [
        'Home'   => '/',
        'Github' => 'https://github.com/gouguoyin/laravel-log-viewer',
        'Gitee'  => 'https://gitee.com/gouguoyin/laravel-log-viewer',
    ],
    'locale_language'   => 'cn',  // 本地化语言，en:英文 cn:中文
    'page_size_menu'    => '10, 20, 30, 50, 100', // 每页显示条数下拉菜单
    'default_page_size' => 20, // 每页显示条数下拉菜单默认选项
    'fix_header'        => true, // 固定表格头部
];
