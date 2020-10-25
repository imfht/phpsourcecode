<?php

/**
 * 实名认证
 */

namespace app\member\api;


class RealApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Real';


    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->info()->export(function ($data) {
            $this->success('ok', $data);
        });
    }

    public function bind() {
        target($this->_middle, 'middle')->setParams(
            array_merge($this->data, [
                'user_info' => $this->userInfo,
                'val_type' => $this->data['valtype'],
                'user_id' => $this->userInfo['user_id']
            ]))->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function getCode() {
        target($this->_middle, 'middle')->setParams([
            'user_info' => $this->userInfo,
            'val_type' => $this->data['valtype'],
            'img_code' => $this->data['imgcode']
        ])->getCode()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}