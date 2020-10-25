<?php

/**
 * 购物车
 */
namespace app\order\api;

class CartApi extends \app\member\api\MemberApi {

    public function index() {

        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'add_id' => $this->data['add_id'],
            'quick' => $this->data['quick'],
        ])->meta()->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function submit() {
        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'add_id' => $this->data['add_id'],
            'quick' => $this->data['quick'],
            'cod_status' => $this->data['cod_status'],
            'coupon_id' => $this->data['coupon_id'],
            'take_id' => $this->data['take_id'],
            'remark' => $this->data['remark'],
            'invoice' => $this->data['invoice'],
            'invoice_type' => $this->data['invoice_type'],
            'invoice_class' => $this->data['invoice_class'],
            'invoice_name' => $this->data['invoice_name'],
            'invoice_label' => $this->data['invoice_label'],
        ])->post()->export(function ($data) {
            if(!$data['cod_status']) {
                $this->success('订单提交成功,请选择付款方式!', $data);
            }else {
                $this->success('订单提交成功,请耐心等待发货!');
            }
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function take() {
        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'add_id' => $this->data['add_id'],
        ])->Take()->export(function ($data) {
            $this->success('', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


    public function num() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => $this->data['rowid'],
            'qty' => $this->data['qty'],
        ])->put()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function checked() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'checked' => $this->data['checked'],
            'uncheck' => $this->data['uncheck'],
        ])->checked()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function del() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => $this->data['rowid'],
        ])->delete()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}