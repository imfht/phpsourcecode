<?php
/**
 * 应用管理菜单
 * 所有级别菜单都支持设置 icon图标和auth权限。
 * 'icon' => 'store_icon',  在public/common/bsse/demo.html目录中查看icon名称（如.icon-store_icon设置的设置去掉.icon）。
 * 'auth' => '1,2,3'          能看见这个菜单的权限ID(在同目录auth.php中设置),多个用户以逗号分隔。不设置auth属性代表任何人都可以看。
 */
return [
    [
        'name' => '一级菜单1',
        'icon' => 'store_icon', 
        'auth' => '1,2',
        'menu' => [
            ['name' =>'二级菜单1','url'=> url('demo/manage.index/index'),'auth' => '1'],
            ['name' =>'二级菜单2','url'=> url('demo/manage.index/index'),'auth' => '2'],
        ]
    ],
    [
        'name' => '一级菜单2',
        'icon' => 'iconset0280',
        'auth' => '2,3',
        'menu' => [
            ['name' =>'二级菜单1','url'=> url('demo/manage.index/index'),'icon' => 'text_icon','menu' => [
                ['name' => '三级菜单1','url' => url('demo/manage.index/index'),'auth' => '2'],
                ['name' => '三级菜单2','url' => url('demo/manage.index/index'),'auth' => '3'],
            ]],
        ]
    ]    
];