<?php

/**
 * 订单管理
 */

namespace app\mall\mobile;

class OrderMobile extends \app\member\mobile\MemberMobile {


    protected $_middle = 'mall/Order';

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'order_no' => request('get', 'order_no')
        ])->meta()->info()->export(function ($data) {
            $this->assign($data);
            $wechat = target('wechat/Wechat', 'service')->init();
            $this->assign('js', $wechat->js);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }
}