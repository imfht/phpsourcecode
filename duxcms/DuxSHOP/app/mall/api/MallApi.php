<?php

/**
 * 商城API
 */

namespace app\mall\api;

class MallApi extends \app\member\api\MemberApi {

    protected $_middle = 'mall/Mall';

    public function addCart() {
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['mall_id'],
            'pro_id' => $this->data['pro_id'],
            'qty' => $this->data['qty'],
            'row_id' => $this->data['row_id'],
            'user_id' => $this->userId
        ])->addCart()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function buyCart() {
        target($this->_middle, 'middle')->setParams([
            'pro_id' => $this->data['pro_id'],
            'qty' => $this->data['qty'],
            'user_id' => $this->userInfo['user_id']
        ])->buyCart()->export(function ($data, $msg) {

            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function addFollow() {
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['mall_id'],
            'user_id' => $this->userId
        ])->addFollow()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function addFaq() {
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['mall_id'],
            'content' => $this->data['content'],
            'user_id' => $this->userId
        ])->addCart()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}