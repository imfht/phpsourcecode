<?php

/**
 * 收货地址管理
 */
namespace app\order\api;

class AddressApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Address';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta('收货地址', '收货地址')->data()->export(function ($data) {
            $this->success('ok', $data['pageList']);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function add() {
        target($this->_middle, 'middle')->setParams(array_merge($this->data, ['user_id' => $this->userInfo['user_id']]))->add()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function edit() {
        target($this->_middle, 'middle')->setParams(array_merge($this->data, ['user_id' => $this->userInfo['user_id']]))->edit()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function del() {
        target($this->_middle, 'middle')->setParams([
            'id' => $this->data['id'],
            'user_id' => $this->userInfo['user_id']
        ])->del()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function default() {
        $info = target('order/OrderAddress')->getAddress($this->userId);
        if(!$info) {
            $this->error(target('order/OrderAddress')->getError());
        }
        $this->success('ok', $info);
    }

    public function setting() {
        target($this->_middle, 'middle')->setParams([
            'id' => $this->data['id'],
            'user_id' => $this->userInfo['user_id']
        ])->default()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}