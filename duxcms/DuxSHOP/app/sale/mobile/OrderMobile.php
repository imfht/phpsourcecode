<?php

/**
 * 推广订单
 */

namespace app\sale\mobile;

class OrderMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'sale/Order';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => request('get', 'type')
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->assign('page', $this->htmlPage($data['pageData']['raw']));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}