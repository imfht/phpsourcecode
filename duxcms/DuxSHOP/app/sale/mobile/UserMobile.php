<?php

/**
 * 推广信息
 */

namespace app\sale\mobile;

class UserMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'sale/User';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'id' => request('get', 'id'),
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}