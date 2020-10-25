<?php

/**
 * 订单收款管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderReceiptModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'receipt_id',
    ];

    protected function base($where) {
        return $this->table('order_receipt(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(['B.*', 'A.status(receipt_status)','A.create_time(receipt_create_time)','A.receipt_time(receipt_receipt_time)', 'A.create_time(receipt_receive_time)', 'A.remark(receipt_remark)', 'A.receipt_id'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.create_time desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if(empty($list)){
            return [];
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        return $this->base($where)->find();
    }

    public function getInfo($id) {
        $where = [];
        $where['A.receipt_id'] = $id;
        return $this->getWhereInfo($where);
    }

}