<?php

/**
 * 优惠券管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderCouponApplyModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'apply_id',
        'format' => [
            'apply_time' => [
                'function' => ['time', 'add'],
            ],
            'complete_time' => [
                'function' => ['time', 'edit'],
            ],
        ]
    ];

}