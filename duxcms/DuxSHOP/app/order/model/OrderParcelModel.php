<?php

/**
 * 发货单管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderParcelModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'parcel_id',
    ];

    protected function base($where) {
        return $this->table('order_parcel(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(['B.*', 'A.create_time(parcel_create_time)', 'A.status(parcel_status)', 'A.log(parcel_log)', 'A.parcel_id'])
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
        return $this->base($where)->find();
    }

    public function getInfo($id) {
        $where = [];
        $where['A.parcel_id'] = $id;
        return $this->getWhereInfo($where);
    }

    public function addLog($log = '', $msg = '', $remark = '', $time) {
        if(!empty($log)) {
            $log = unserialize($log);
        }else {
            $log = [];
        }
        $log[] = [
            'msg' => $msg,
            'remark' => $remark,
            'time' => $time
        ];
        return serialize($log);
    }

}