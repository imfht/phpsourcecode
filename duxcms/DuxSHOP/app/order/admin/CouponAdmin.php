<?php

/**
 * 优惠券管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class CouponAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderCoupon';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '优惠券管理',
                'description' => '管理订单优惠券',
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
            'keyword' => 'name',
            'status' => 'status',
        ];
    }

    public function _indexWhere($whereMaps) {
        switch ($whereMaps['status']) {
            case 1:
                $whereMaps['status'] = 0;
                break;
            case 2:
                $whereMaps['status'] = 1;
                break;
            default:
                unset($whereMaps['status']);
        }
        return $whereMaps;
    }


    public function _indexAssign($pageMaps) {
        return [
            'typeList' => target('order/OrderCoupon')->typeList(true),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _addAssign() {
        $typeList = target('order/OrderCoupon')->typeList(true);
        $typeCur = current($typeList);

        return [
            'info' => [
                'type' => $typeCur['key']

            ],
            'classList' => target('order/OrderCouponClass')->loadList(),
            'goodsClass' => target('mall/MallClass')->loadTreeList(),
            'mallList' => target('mall/Mall')->loadList(),
            'typeList' => $typeList,
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function edit() {
        $id = request('', 'id', 0, 'intval');
        if(empty($id)) {
            $this->error('参数不正确!');
        }
        $info = target($this->_model)->getInfo($id);
        if(empty($info)) {
            $this->error('优惠券不存在！');
        }
        if(!isPost()) {
            $this->assign([
                'info' => $info,
                'classList' => target('order/OrderCouponClass')->loadList(),
                'goodsClass' => target('mall/MallClass')->loadTreeList(),
                'mallList' => target('mall/Mall')->loadList(),
                'typeList' => target('order/OrderCoupon')->typeList(true),
                'currencyList' => target('member/MemberCurrency')->typeList()
            ]);

            $proList = [];
            if ($info['has_id'] && $info['typeInfo']['type'] == 1) {
                $proList = target('mall/Mall')->loadList([
                    '_sql' => 'B.mall_id in (' . $info['has_id'] . ')'
                ]);
            }

            $proClass = [];
            if ($info['has_id'] && $info['typeInfo']['type'] == 2) {
                $proClass = target('mall/MallClass')->getInfo($info['has_id']);
            }

            $this->assign('proClass', $proClass);
            $this->assign('proList', $proList);
            $this->systemDisplay();
        }else {
            $data = [];
            $data['coupon_id'] = $id;
            $endTime = request('post', 'end_time', '', 'strtotime');
            $stock = request('post', 'stock', '', 'intval');
            if($info['start_time'] >= $endTime) {
                $this->error('结束时间不能大于发放时间！');
            }
            if(!empty($endTime)) {
                $data['end_time'] = $endTime;
                $data['stock'] = $stock;
            }
            $status = target($this->_model)->edit($data);
            if(!$status) {
                $this->error(target($this->_model)->getError());
            }
            $this->success('编辑成功！');
        }
    }

    public function _editTpl() {
        return 'edit';
    }

    public function _editAssign($info) {

    }

    public function _indexOrder() {
        return 'A.coupon_id desc';
    }

    public function del() {
        $id = request('', 'id', 0, 'intval');
        if (empty($id)) {
            $this->error('参数传递错误！');
        }
        $status = target($this->_model)->edit([
            'coupon_id' => $id,
            'del_status' => 1
        ]);
        if ($status) {
            $this->success('删除成功！');
        } else {

            $this->error('删除失败！');
        }
    }

    public function _addBefore() {
        $_POST['platform'] = 1;
    }


    public function status() {
        $id = request('get', 'id', 0, 'intval');
        $status = request('get', 'status', 0, 'intval');
        if(empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target($this->_model)->getInfo($id);
        $data = [];
        $data['coupon_id'] = $info['coupon_id'];

        if($status == 1) {
            $data['status'] = 1;
            $msg = '优惠券上架成功！';
        }else {
            $data['status'] = 0;
            $msg = '优惠券下架成功！';
        }
        $status = target($this->_model)->edit($data);
        if(!$status) {
            $this->error(target($this->_model)->getError());
        }
        $this->success($msg);
    }


}