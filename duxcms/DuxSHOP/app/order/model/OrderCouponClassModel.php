<?php

/**
 * 优惠券分类
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderCouponClassModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'class_id',
    ];

}