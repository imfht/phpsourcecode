<?php

/**
 * 我的二维码
 */

namespace app\sale\controller;

class QrcodeController extends \app\member\controller\MemberController {

    protected $_middle = 'sale/Qrcode';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'user_info' => $this->userInfo
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}