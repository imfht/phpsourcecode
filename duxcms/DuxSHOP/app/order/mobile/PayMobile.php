<?php

/**
 * 订单管理
 */

namespace app\order\mobile;

class PayMobile extends \app\member\mobile\MemberMobile {


    protected $_middle = 'order/Pay';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'mobile',
            'user_id' => $this->userInfo['user_id'],
            'order_no' => request('get', 'order_no'),
        ])->meta('订单支付', '订单支付', url(''))->base()->info()->export(function ($data) {
			$this->tplInfo['back'] = '/';
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function pay() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'mobile',
            'type' => request('post', 'type'),
            'type_expend' => request('post', 'type_expend'),
            'order_no' => request('post', 'order_no'),
            'currency' => request('post', 'currency'),
            'password' => request('post', 'password'),
            'user_id' => $this->userInfo['user_id'],
        ])->base()->pay()->export(function ($data, $msg) {
            $this->success($msg, $data['data']['url']);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function complete() {
        target($this->_middle, 'middle')->setParams([
            'pay_no' => request('get', 'pay_no'),
            'pay_sign' => request('get', 'pay_sign'),
        ])->meta('支付完成', '支付完成', URL)->complete()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}