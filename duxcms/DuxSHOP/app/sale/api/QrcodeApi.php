<?php

/**
 * 推荐二维码
 */
namespace app\sale\api;

class QrcodeApi extends \app\member\api\MemberApi {
    protected $_middle = 'sale/Qrcode';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'user_info' => $this->userInfo
        ])->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}