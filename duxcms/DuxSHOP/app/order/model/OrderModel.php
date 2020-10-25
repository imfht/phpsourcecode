<?php

/**
 * 订单管理
 */

namespace app\order\model;

use app\system\model\SystemModel;

class OrderModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'order_id',
    ];

    protected function base($where) {
        $base = $this->table('order(A)')
            ->join('member_user(B)', ['B.user_id', 'A.order_user_id']);
        $field = ['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'];

        return $base
            ->field($field)
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'order_id desc', $lock = false) {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->lock($lock)
            ->select();
        if (empty($list)) {
            return [];
        }
        foreach ($list as $key => $vo) {
            $totalPrice = price_calculate($vo['delivery_price'], '+', $vo['pay_price']);
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['total_price'] = $totalPrice;
            $list[$key]['balance_price'] = price_calculate($totalPrice, '-', $vo['refund_price']);
            $list[$key]['status_data'] = target('order/Order', 'service')->getAction($vo);
            $list[$key]['status_data']['html'] = target('order/Order', 'service')->orderActionHtml($list[$key]);
            $list[$key]['pay_currency'] = unserialize($vo['pay_currency']);
            $list[$key]['pay_data'] = unserialize($vo['pay_data']);
            $list[$key]['url'] = url($vo['order_app'] . '/order/info', ['order_no' => $vo['order_no']]);
        }
        return $list;
    }

    public function getWhereInfo($where) {
        $info = parent::getWhereInfo($where);
        if (empty($info)) {
            return [];
        }
        $totalPrice = price_calculate($info['delivery_price'], '+', $info['pay_price']);
        $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
        $info['status_data'] = target('order/Order', 'service')->getAction($info);
        $info['status_data']['html'] = target('order/Order', 'service')->orderActionHtml($info);
        $info['total_price'] = $totalPrice;
        $info['balance_price'] = price_calculate($totalPrice, '-', $info['refund_price']);
        $info['pay_currency'] = unserialize($info['pay_currency']);
        $info['pay_data'] = unserialize($info['pay_data']);

        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['order_id'] = $id;

        return $this->getWhereInfo($where);
    }

    public function actionHtml($info) {
    }



}