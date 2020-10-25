<?php
return [
    [
        'name' => '商城管理',
        'icon' => 'store_icon',
        'menu' => [
            ['name' => '商品管理','url' => url('popupshop/item/index')], 
            ['name' => '首页导航','url' => url('popupshop/adwords/index',['group'=>1])],
            ['name' => '首页幻灯','url' => url('popupshop/adwords/index',['group'=>2])],
            ['name' => '首页图片','url' => url('popupshop/adwords/index',['group'=>3])],
        ]
    ],
    [
        'name' => '寄卖管理',
        'icon' => 'discount',
        'menu' => [
            ['name' => '套装管理','url' => url('popupshop/sale/index')],
            ['name' => '寄卖商品','url' => url('popupshop/saleUser/index')], 
            ['name' => '平台仓库','url' => url('popupshop/saleHouse/index')], 
        ]
    ],
    [
        'name' => '订单管理',
        'icon' => 'calendar_icon',
        'menu' => [
            ['name' => '商城订单','url' => url('popupshop/order/index')],
            ['name' => '活动订单','url' => url('popupshop/saleOrder/index')],
        ]
    ],
    [
        'name' => '内容管理',
        'icon' => 'text_icon',
        'menu' => [
            ['name' => '活动广告','url' => url('popupshop/adwords/index',['group'=>4])],
            ['name' => '内容管理','url' => url('popupshop/article/index')],
        ]
    ],
    [
        'name' => '财务管理',
        'icon' => 'chuzhijine',
        'menu' => [
            ['name' => '客户收益','url' => url('popupshop/bank/index')],
            ['name' => '提现管理','url' => url('popupshop/bank/cash')],
            ['name' => '交易账单','url' => url('popupshop/bank/bill')], 
            ['name' => '订单统计','url' => url('popupshop/bank/statistics')],
            ['name' => '财务统计','url' => url('popupshop/bank/bankcount')],
        ]
    ],
    [
        'name' => '商城设置',
        'icon' => 'store_icon',
        'menu' => [
            ['name' => '商城分类','url' => url('popupshop/category/index')],
            ['name' => '库存分类','url' => url('popupshop/saleCategory/index')], 
            ['name' => '运费管理','url' => url('popupshop/fare/index')],
        ]
    ],
    [
        'name' => '利润管理',
        'icon' => 'iconset0280',
        'menu' => [
            ['name' => '代理设置','url' => url('popupshop/user/agent')],
            ['name' => '分润配置','url' => url('popupshop/config/index')],
            ['name' => '员工权限','url' => url('popupshop/auth/index')], 
        ]
    ]   
];
