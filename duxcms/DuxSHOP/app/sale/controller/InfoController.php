<?php

/**
 * 推广信息
 */

namespace app\sale\controller;

class InfoController extends \app\member\controller\MemberController {

    protected $_middle = 'sale/Info';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function submit() {
        target($this->_middle, 'middle')->setParams(['user_id' => $this->userInfo['user_id']])->apply()->export(function ($data, $msg) {
            $this->success($msg, url('index'));
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}