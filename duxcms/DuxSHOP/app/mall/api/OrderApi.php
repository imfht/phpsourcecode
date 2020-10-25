<?php

/**
 * 订单详情
 */
namespace app\mall\api;

class OrderApi extends \app\member\api\MemberApi {

    protected $_middle = 'mall/Order';

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'order_no' => $this->data['order_no']
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }


}