<?php

/**
 * 订单发货管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderDeliveryModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'delivery_id',
    ];

    protected function base($where) {
        return $this->table('order_delivery(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(['B.*', 'A.create_time(delivery_create_time)', 'A.create_time(delivery_receive_time)', 'A.remark(delivery_remark)', 'A.log(delivery_log)', 'A.log_update(delivery_log_update)', 'A.delivery_id', 'A.delivery_name', 'A.delivery_no', 'A.receive_time', 'A.receive_status', 'A.print_status', 'A.export_status'])
            ->where((array)$where);
    }

    /**
     * 获取分类树
     * @return array
     */
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
        $info = $this->base($where)->find();
        if($info) {
            $info['delivery_log'] = unserialize($info['delivery_log']);
        }
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.delivery_id'] = $id;
        return $this->getWhereInfo($where);
    }

}