<?php

/**
 * 银行卡管理
 */
namespace app\member\api;

class CardApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Card';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId
        ])->meta()->data()->export(function ($data) {
            $this->success('ok',$data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'card_id' => $this->data['id']
        ])->meta()->info()->export(function ($data) {
            $this->success('ok',$data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function bind() {
        $data = $this->data;
        target($this->_middle, 'middle')->setParams(
            array_merge($data, [
                'user_info' => $this->userInfo,
                'val_type' => $data['valtype'],
                'user_id' => $this->userId
            ]))->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}