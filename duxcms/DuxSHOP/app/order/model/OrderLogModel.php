<?php

/**
 * 订单记录管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    public function loadList($where = [], $limit = 0, $order = '') {
        return parent::loadList($where, $limit, 'log_id desc');
    }

}