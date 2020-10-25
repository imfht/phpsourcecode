<?php

/**
 * 订单商品退款
 */

namespace app\order\model;

use app\system\model\SystemModel;

class OrderRefundModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'refund_id',
    ];

    protected function base($where) {

        $base = $this->table('order_refund(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->join('order_goods(C)', ['C.id', 'A.order_goods_id']);
        $field = ['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)', 'B.avatar(user_avatar)', 'C.id(order_goods_id)', 'C.order_id', 'C.goods_name', 'C.goods_name', 'C.goods_image', 'C.goods_url', 'C.goods_qty', 'C.goods_price', 'C.price_total', 'C.extend'];
        return $base->field($field)->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.refund_id desc')
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key] = $this->formatInfo($vo);

        }
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $info = $this->formatInfo($info);
        }
        return $info;
    }

    public function formatInfo($info) {
        $statusData = [];
        if($info['status'] == 1) {
            $statusData['title'] = '待审核';
            $statusData['tip'] = '退款申请卖家等待审核，请耐心等待!';
            $statusData['color'] = 'warning';
            $statusData['icon'] = 'info';
        }
        if($info['status'] == 2 && $info['delivery_no'] == '') {
            $statusData['title'] = '待退货待退款';
            $statusData['tip'] = '退款申请审核成功，请退回货品!';
            $statusData['color'] = 'warning';
            $statusData['icon'] = 'bus';
        }
        if($info['status'] == 2 && $info['delivery_no'] <> '') {
            $statusData['title'] = '已退货待退款';
            $statusData['tip'] = '买家已经退货，收货后请退款!';
            $statusData['color'] = 'warning';
            $statusData['icon'] = 'bus';
        }
        if($info['status'] == 3) {
            $statusData['title'] = '已完成';
            $statusData['tip'] = '退款已成功，请注意查收!';
            $statusData['color'] = 'success';
            $statusData['icon'] = 'check';
        }
        if(!$info['status']) {
            $statusData['title'] = '已取消';
            $statusData['tip'] = '该退款申请已被取消!';
            $statusData['color'] = 'danger';
            $statusData['icon'] = 'close';
        }
        $info['status_data'] = $statusData;

        if($info['type'] == 0) {
            $typeTitle = '仅退款';
        }
        if($info['type'] == 1) {
            $typeTitle = '退货退款';
        }
        if($info['type'] == 2) {
            $typeTitle = '整单退';
        }

        $info['type_title'] = $typeTitle;


        $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
        $info['images'] = unserialize($info['images']);
        $info['user_avatar'] = target('member/MemberUser')->getAvatar($info['user_id']);
        $info['extend'] = unserialize($info['extend']);

        $info['refund_price'] = price_calculate($info['price'], '+', $info['delivery_price']);
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.refund_id'] = $id;
        return $this->getWhereInfo($where);
    }

    public function causeList() {
        return [
            '外观/参数等与描述不符',
            '商品发错货',
            '产品质量/故障',
            '效果不好或不喜欢',
            '收到商品少件/破损/污渍',
            '假冒商品',
            '其他',
        ];
    }

}