<?php

/**
 * 优惠券记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class CouponLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderCouponLog';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '领券管理',
                'description' => '优惠券领券记录',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'C.tel,C.email',
        ];
    }


    public function _indexAssign($pageMaps) {
        return [
            'typeList' => target('order/OrderCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _addAssign() {
        return [
            'typeList' => target('order/OrderCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _editAssign($info) {
        return [
            'typeList' => target('order/OrderCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _indexOrder() {
        return 'coupon_id desc';
    }

    protected function _delAfter($id) {
        target('order/OrderCouponLog')->where([
            'coupon_id' => $id
        ])->delete();
    }

}