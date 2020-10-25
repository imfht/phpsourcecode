<?php

/**
 * 订单支付
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderPayModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'pay_id',
    ];

}