<?php

return [
    'seller' => [
        'poundage' => 0.05,//在提现的时候扣除，商家结算时平台扣取手续费，0-1的数字，乘以100就是百分之几
    ],
    //快递鸟物流
    'kdniao' => [
        'id' => env('KDNIAO_ID'),
        'key' => env('KDNIAO_KEY')
    ],
    'goods' => [
        'goods_search_es' => false,//商品搜索启用ES 0否1是
    ],
    'order' => [
        'order_cancel_time' => 30 * 60,//订单超时取消时间单位秒，30分钟
        'order_confirm_time' => 10 * 24 * 3600,//订单超时确认时间单位秒，10天
        'order_evaluation_time' => 7 * 24 * 3600,//订单超时评价时间单位秒，7天
        'order_complete_time' => 7 * 24 * 3600,//订单超时完成时间单位秒，7天
    ]
];
