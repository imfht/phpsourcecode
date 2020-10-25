<?php

/**
 * 购物车
 */

namespace app\order\controller;

class CartController extends \app\member\controller\MemberController {


    public function index() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->otherDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function submit() {
        if (!isPost()) {
            $urlParams = [
                'user_id' => $this->userInfo['user_id'],
                'add_id' => request('get', 'add_id'),
                'quick' => request('get', 'quick'),
            ];
            target('order/CartSubmit', 'middle')->setParams($urlParams)->meta()->data()->export(function ($data) {
                $this->assign($data);
                $this->otherDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            target('order/CartSubmit', 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'add_id' => request('get', 'add_id'),
                'quick' => request('get', 'quick'),
                'cod_status' => request('post', 'cod_status'),
                'coupon_id' => request('post', 'coupon_id'),
                'take_id' => request('post', 'take_id'),
                'remark' => request('post', 'remark'),
                'invoice' => request('post', 'invoice'),
                'invoice_type' => request('post', 'invoice_type'),
                'invoice_class' => request('post', 'invoice_class'),
                'invoice_name' => request('post', 'invoice_name'),
                'invoice_label' => request('post', 'invoice_label'),
            ])->post()->export(function ($data) {
                if(!$data['cod_status']) {
                    $this->success('订单提交成功,请选择付款方式!', url('order/Pay/index', ['order_no' => $data['order_no']]));
                }else {
                    $this->success('订单提交成功,请耐心等待发货!', url('member/Index/index'));
                }
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function take() {
        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'add_id' => request('get', 'add_id'),
        ])->Take()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function num() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => request('post', 'rowid'),
            'qty' => request('post', 'qty')
        ])->put()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function checked() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'checked' => request('post', 'checked'),
            'uncheck' => request('post', 'uncheck')
        ])->checked()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function del() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => request('post', 'rowid'),
        ])->delete()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function getJson() {
        $info = target('order/Cart', 'service')->getCart($this->userInfo['user_id']);
        if (!empty($info)) {
            $this->success($info);
        } else {
            $this->error('您的购物车还没有商品，赶紧去选购吧!');
        }
    }

}