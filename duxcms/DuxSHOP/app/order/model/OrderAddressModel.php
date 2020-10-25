<?php

/**
 * 收货地址
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderAddressModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'add_id',
    ];

    public function getAddress($userId, $id = 0) {
        $where = [];
        $where['user_id'] = $userId;
        if(empty($id)) {
            $where['default'] = 1;
        }else {
            $where['add_id'] = $id;
        }
        return $this->getWhereInfo($where);
    }

}