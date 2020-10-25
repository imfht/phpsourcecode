<?php
return [
    [
        'name' => '活动管理',
        'icon' => 'discount',
        'menu' => [
            ['name' => '抢购管理','url' => url('fastshop/sale/index')],
            ['name' => '客户商品','url' => url('fastshop/entrust/index')], 
            ['name' => '抢购时段','url' => url('fastshop/times/index')],
        ]
    ],
    [
        'name' => '商品管理',
        'icon' => 'store_icon',
        'menu' => [
            ['name' => '商品管理','url' => url('fastshop/item/index')], 
            ['name' => '团购管理','url' => url('fastshop/group/index')],
            ['name' => '商品分类','url' => url('fastshop/category/index')],
        ]
    ],
    [
        'name' => '订单管理',
        'icon' => 'calendar_icon',
        'menu' => [
            ['name' => '商城订单','url' => url('fastshop/shopping/index')],
            ['name' => '活动订单','url' => url('fastshop/order/index')],
        ]
    ],
    [
        'name' => '财务管理',
        'icon' => 'chuzhijine',
        'menu' => [
            ['name' => '财务统计','url' => url('fastshop/bank/counts')],
            ['name' => '客户收益','url' => url('fastshop/bank/index')],
            ['name' => '交易排行','url' => url('fastshop/bank/top')],
            ['name' => '提现管理','url' => url('fastshop/bank/cash')],
            ['name' => '成交奖励','url' => url('fastshop/entrust/lists')], 
            ['name' => '财务明细','url' => url('fastshop/bank/alllogs')], 
        ]
    ],
    [
        'name' => '内容管理',
        'icon' => 'text_icon',
        'menu' => [
            ['name' => '抢购广告','url' => url('fastshop/banner/index',['group'=>1])],
            ['name' => '首页幻灯','url' => url('fastshop/banner/index',['group'=>2])],
            ['name' => '首页导航','url' => url('fastshop/banner/index',['group'=>3])],
            ['name' => '首页图片','url' => url('fastshop/banner/index',['group'=>4])],
            ['name' => '内容管理','url' => url('fastshop/article/index')],
        ]
    ],
    [
        'name' => '商城设置',
        'icon' => 'store_icon',
        'menu' => [
            ['name' => '友情提示','url' => url('fastshop/config/message')],
            ['name' => '抢购时段','url' => url('fastshop/times/index')],
            ['name' => '商品分类','url' => url('fastshop/category/index')],
            ['name' => '运费管理','url' => url('fastshop/fare/index')],
        ]
    ],
    [
        'name' => '利润管理',
        'icon' => 'iconset0280',
        'menu' => [
            ['name' => '代理设置','url' => url('fastshop/user/agent')],
            ['name' => '分润配置','url' => url('fastshop/config/index')],
            ['name' => '功能配置','url' => url('fastshop/config/setting')],
            ['name' => '员工权限','url' => url('fastshop/auth/index')], 
        ]
    ]   
];
