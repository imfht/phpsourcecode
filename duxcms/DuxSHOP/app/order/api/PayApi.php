<?php

/**
 * 支付订单
 */
namespace app\order\api;

class  PayApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Pay';


    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'api',
            'user_id' => $this->userInfo['user_id'],
            'order_no' => $this->data['order_no'],
        ])->base()->info()->export(function ($data) {
            $data['userMoney'] = $this->userInfo['money'];
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function pay() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'api',
            'type' => $this->data['type'],
            'type_expend' => $this->data['type_expend'],
            'order_no' => $this->data['order_no'],
            'currency' => $this->data['currency'],
            'password' => $this->data['password'],
            'user_id' => $this->userInfo['user_id'],
        ])->base()->pay()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}