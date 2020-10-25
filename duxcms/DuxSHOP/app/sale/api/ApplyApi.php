<?php

/**
 * 推广商申请
 */

namespace app\sale\api;

class ApplyApi extends \app\member\api\MemberApi {

    protected $_middle = 'sale/Apply';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->data()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function submit() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->apply()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}