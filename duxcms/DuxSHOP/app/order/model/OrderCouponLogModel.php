<?php

/**
 * 优惠券记录
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderCouponLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    protected function base($where) {
        return $this->table('order_coupon_log(A)')
            ->join('order_coupon(B)', ['B.coupon_id', 'A.coupon_id'])
            ->join('member_user(C)', ['C.user_id', 'A.user_id'])
            ->field([ 'B.*', 'A.*','C.email(user_email)', 'C.tel(user_tel)', 'C.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.log_id desc')
            ->select();
        if(empty($list)) {
            return [];
        }
        $currencyList = target('member/MemberCurrency')->typeList();
        $typeList = target('order/OrderCoupon')->typeList(true);
        foreach($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['currencyInfo'] = $currencyList[$vo['exchange_type']];
            $list[$key]['typeInfo'] =$typeList[$vo['type']];
            $list[$key]['overdue'] = $vo['end_time'] < time() ? true : false;
            $list[$key]['url'] = target($typeList[$vo['type']]['target'])->urlCoupon($vo);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $currencyList = target('member/MemberCurrency')->typeList();
            $typeList = target('order/OrderCoupon')->typeList();
            $info['currencyInfo'] = $currencyList[$info['exchange_type']];
            $info['typeInfo'] =$typeList[$info['type']];
        }
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.log_id'] = $id;
        return $this->getWhereInfo($where);
    }

}