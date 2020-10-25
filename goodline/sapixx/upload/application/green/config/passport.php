<?php
return [
    [
        'name' => '用户管理',
        'icon' => 'my_icon',
        'menu' => [
            ['name' => '用户列表','icon' =>'kehuziliao','url' => url('green/user/index')],
            ['name' => '回收人员','icon' =>'iconset0280','url' => url('green/staff/index')],
        ]
    ],
    [
        'name' => '回收管理',
        'icon' => 'classify_icon',
        'menu' => [
            ['name' => '回收列表','icon' =>'jingpincaiji','url' => url('green/retrieve/index')],
        ]
    ],
    [
        'name' => '设备管理',
        'icon' => 'tools-hardware',
        'menu' => [
            ['name' => '设备列表','icon' =>'list_icon','url' => url('green/device/index')],
            ['name' => '设备地图','icon' =>'address_icon','url' => url('green/device/deviceMap')],
        ]
    ]
];