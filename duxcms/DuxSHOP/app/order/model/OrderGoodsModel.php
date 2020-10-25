<?php

/**
 * 订单商品
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderGoodsModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
    ];

    public function loadList($where = array(), $limit = 0, $order = 'id desc') {
        $list = parent::loadList($where, $limit, $order);
        foreach($list as $key => $vo) {
            $list[$key]['goods_options'] = unserialize($vo['goods_options']);
            $list[$key]['goods_currency'] = unserialize($vo['goods_currency']);
            $list[$key]['extend'] = unserialize($vo['extend']);
        }
        return $list;
    }

    public function getWhereInfo($where) {
        $info = parent::getWhereInfo($where);
        if($info) {
            $info['goods_options'] = unserialize($info['goods_options']);
            $info['goods_currency'] = unserialize($info['goods_currency']);
            $list['extend'] = unserialize($info['extend']);
        }
        return $info;
    }


    public function loadHasList($where) {
        return $this->table('order_goods(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(['B.*', 'A.*'])
            ->where((array)$where)->select();
    }

}