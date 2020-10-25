<?php

/**
 * 购物车数据
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderCartModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'cart_id',
    ];

}