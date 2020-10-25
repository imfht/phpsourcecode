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
        'name' => '文章管理',
        'icon' => 'news_icon',
        'menu' => [
            ['name' => '文章管理','url' => url('green/news/index'),'icon' => 'news_icon'],
            ['name' => '文章分类','url' => url('green/newscate/index'),'icon' => 'classify_icon'],
        ]
    ],
    [
        'name' => '招募管理',
        'icon' => 'news_icon',
        'menu' => [
            ['name' => '招募管理','url' => url('green/recruit/index'),'icon' => 'news_icon'],
            ['name' => '求职管理','url' => url('green/job/index'),'icon' => 'classify_icon'],
        ]
    ],
    [
        'name' => '设备管理',
        'icon' => 'tools-hardware',
        'menu' => [
            ['name' => '设备列表','icon' =>'list_icon','url' => url('green/device/index')],
            ['name' => '设备地图','icon' =>'address_icon','url' => url('green/device/deviceMap')],
        ]
    ],
    [
        'name' => '积分商城',
        'icon' => 'cart',
        'menu' => [
            ['name' => '商品管理','url' => url('green/shop/index')],
            ['name' => '订单管理','url' => url('green/order/index')],

        ]
    ],
    [
        'name' => '签到管理',
        'icon' => 'cart',
        'menu' => [
            ['name' => '配置管理','url' => url('green/sign/config')],
            ['name' => '签到列表','url' => url('green/sign/index')],

        ]
    ],
    [
        'name' => '财务管理',
        'icon' => 'chuzhijine',
        'menu' => [
            ['name' => '提现管理','icon' => 'zhuanzhang','url' => url('green/bank/cash')]
        ]
    ],
    [
        'name' => '系统设置',
        'icon' => 'setup_icon',
        'menu' => [
            ['name' => '运营商','url' => url('green/operate/index')],
            ['name' => '首页广告','url' => url('green/adwords/index',['group'=>'banner'])],
            ['name' => '图片导航','url' => url('green/adwords/index',['group'=>'pic'])],
            ['name' => '图标导航','url' => url('green/adwords/index',['group'=>'icon'])],
            ['name' => '旧物分类','url' => url('green/category/index')],
            ['name' => '投递指南','url' => url('green/config/help')],
            ['name' => '应用配置','url' => url('green/config/index')]
        ]
    ]
];