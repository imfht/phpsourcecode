<?php

/**
 * 订单分组管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderGroupModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'group_id',
    ];

}