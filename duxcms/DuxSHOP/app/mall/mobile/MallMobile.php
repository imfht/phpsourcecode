<?php

/**
 * 商品处理
 */

namespace app\mall\mobile;

class MallMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'mall/Mall';

    public function addCart() {
        $proId = request('post', 'pro_id');
        $mallId = request('post', 'mall_id');
        $qty = request('post', 'qty');
        $rowId = request('post', 'row_id');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $mallId,
            'pro_id' => $proId,
            'row_id' => $rowId,
            'qty' => $qty,
            'user_id' => $this->userInfo['user_id']
        ])->addCart()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function buyCart() {
        $proId = request('post', 'pro_id');
        $qty = request('post', 'qty');
        target($this->_middle, 'middle')->setParams([
            'pro_id' => $proId,
            'qty' => $qty,
            'user_id' => $this->userInfo['user_id']
        ])->buyCart()->export(function ($data, $msg) {
            $this->success($msg, url('order/Cart/submit', ['quick' => $data['quick']]));
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function addFollow() {
        $mallId = request('post', 'mall_id');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $mallId,
            'user_id' => $this->userInfo['user_id']
        ])->addFollow()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function addFaq() {
        $mallId = request('post', 'mall_id');
        $content = request('post', 'content');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $mallId,
            'content' => $content,
            'user_id' => $this->userInfo['user_id']
        ])->addFaq()->export(function ($data, $msg) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}