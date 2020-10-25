<?php

/**
 * 推广订单管理
 */
namespace app\sale\model;

use app\system\model\SystemModel;

class SaleOrderModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
        'into' => '',
        'out' => '',
    ];

    protected function base($where) {
        return $this->table('sale_order(A)')
            ->join('order_goods(B)', ['B.id', 'A.order_goods_id'])
            ->join('member_user(C)', ['C.user_id', 'A.user_id'])
            ->join('member_user(D)', ['D.user_id', 'B.user_id'])
            ->join('order(E)', ['E.order_id', 'B.order_id'])
            ->field(['A.*', 'B.user_id(order_user_id)', 'B.goods_qty', 'B.goods_price', 'B.goods_weight', 'B.goods_options', 'B.goods_name', 'B.goods_image', 'B.goods_url', 'B.price_total', 'C.email(user_email)', 'C.tel(user_tel)', 'C.nickname(user_nickname)', 'D.email(order_user_email)', 'D.tel(order_user_tel)', 'D.nickname(order_user_nickname)', 'E.order_title', 'E.order_no'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['order_show_name'] = target('member/MemberUser')->getNickname($vo['order_user_nickname'], $vo['order_user_tel'], $vo['order_user_email']);
            $list[$key]['goods_options'] = unserialize($vo['goods_options']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['order_show_name'] = target('member/MemberUser')->getNickname($info['order_user_nickname'], $info['order_user_tel'], $info['order_user_email']);
            $info['goods_options'] = unserialize($info['goods_options']);
        }
        return $info;
    }

    public function getInfo($id) {
        return $this->getWhereInfo([
            'A.id' => $id
        ]);
    }


}