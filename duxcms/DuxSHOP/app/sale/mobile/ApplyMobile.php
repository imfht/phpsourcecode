<?php

/**
 * 推广商申请
 */

namespace app\sale\mobile;

class ApplyMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'sale/Apply';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta('推广商申请', '推广商申请')->data()->export(function ($data) {
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