<?php

/**
 * 订单管理
 */

namespace app\mall\model;

use app\system\model\SystemModel;

class MallOrderModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
    ];

    protected function base($where) {
        $base = $this->table('mall_order(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->join('member_user(C)', ['C.user_id', 'B.order_user_id']);
        $field = ['A.*', 'B.*', 'C.email(user_email)', 'C.tel(user_tel)', 'C.nickname(user_nickname)'];
        return $base
            ->field($field)
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = 'A.id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if (empty($list)) {
            return [];
        }
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['status_data'] = target('order/Order', 'service')->getAction($vo);
            $list[$key]['status_data']['html'] = target('order/Order', 'service')->orderActionHtml($list[$key]);
            $list[$key]['pay_currency'] = unserialize($vo['pay_currency']);
            $list[$key]['pay_data'] = unserialize($vo['pay_data']);
            $list[$key]['total_price'] = price_format($vo['delivery_price'] + $vo['pay_price']);
        }

        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if (empty($info)) {
            return [];
        }
        $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
        $info['status_data'] = target('order/Order', 'service')->getAction($info);
        $info['status_data']['html'] = target('order/Order', 'service')->orderActionHtml($info);
        $info['pay_currency'] = unserialize($info['pay_currency']);
        $info['pay_data'] = unserialize($info['pay_data']);
        $info['total_price'] = price_format($info['delivery_price'] + $info['pay_price']);
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['B.order_id'] = $id;
        return $this->getWhereInfo($where);
    }

    public function getCouponHas($hasId) {
        $info = target('mall/Mall')->getInfo($hasId);
        if ($info) {
            return [
                'title' => '商品优惠券',
                'url' => VIEW_LAYER_NAME . '/mall/Info/index?id' . $hasId
            ];
        } else {
            return [];
        }
    }


    public function hasCoupon($coupon, $order) {
        if ($coupon['type'] <> 'mall') {
            return false;
        }
        if (!$coupon['has_id']) {
            return false;
        }
        $hasIds = explode(',', $coupon['has_id']);
        $ids = [];
        foreach ($order['items'] as $v) {
            if (!in_array($v['app_id'], $hasIds)) {
                continue;
            }
            if (bccomp($coupon['meet_money'], $v['total'], 2) !== 1) {
                $ids[] = $v['id'];
                break;
            }
        }
        return $ids ? $ids : false;
    }

    public function urlCoupon($info) {
        return url(VIEW_LAYER_NAME . '/mall/Index/index', ['coupon' => $info['coupon_id']]);
    }


}