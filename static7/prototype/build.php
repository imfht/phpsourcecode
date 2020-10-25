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
// $Id$

return [

    // plugin 插件模块
    'plugin'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
        'controller' => ['Index', 'Test', 'User'],
        'model'      => ['User', 'User'],
        'view'       => ['index/index'],
    ],
    //install 安装模块
    'install'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view','Data'],
        'controller' => ['Index', 'Install',],
        'model'      => ['Index', 'Install'],
        'view'       => ['index/index','install','base'],
    ],
];
