<?php

/**
 * 发票
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderInvoiceModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'invoice_id',
    ];

    protected function base($where) {
        return  $this->table('order_invoice(A)')
            ->join('order_invoice_class(B)', ['B.class_id', 'A.class_id'])
            ->join('order(C)', ['C.order_id', 'A.order_id'])
            ->field(['A.*', 'B.name(class_name)', 'C.order_no'])
            ->where((array)$where);
    }


    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.invoice_id asc')
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
        $info = $this->base($where)->find();
        return $info;
    }


}