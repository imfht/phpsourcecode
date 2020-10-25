<?php

/**
 * 订单管理
 */

namespace app\sale\controller;

class OrderController extends \app\member\controller\MemberController {

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