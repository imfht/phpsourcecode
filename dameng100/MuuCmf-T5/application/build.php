<?php

return [
// 生成应用公共文件
    '__file__' => [],
    // 定义demo模块的自动生成 （按照实际定义的文件名生成）
    'app'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
        'controller' => ['Index', 'Admin'],
        'model'      => ['Appmodel'],
        'view'       => ['index/index'],
    ],
        // 其他更多的模块定义
];
