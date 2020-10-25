<?php

/**
 * 会员首页
 */

namespace app\member\controller;


class IndexController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Index';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'web',
            'user_info' => $this->userInfo
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }



}