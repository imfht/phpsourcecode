<?php

/**
 * 退款管理
 */

namespace app\order\mobile;

class RefundMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'order/Refund';

    public function index() {
        $pageLimit = request('get', 'limit', 0, 'intval');
        $type = request('get', 'type', 0, 'intval');
        $urlParams = [
            'limit' => $pageLimit,
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams(array_merge($urlParams, ['user_id' => $this->userInfo['user_id']]))->meta('售后管理', '售后管理')->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'refund_no' => request('get', 'refund_no'),
            'user_id' => $this->userInfo['user_id']
        ])->meta('退款管理', '退款管理')->info()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function push() {
        if(!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'id' => request('get', 'id'),
                'user_id' => $this->userInfo['user_id']
            ])->meta('退款申请', '退款申请', url(''))->orderInfo()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }else {
            target($this->_middle, 'middle')->setParams([
                'id' => request('post', 'id'),
                'type' => request('post', 'type'),
                'cause' => request('post', 'cause'),
                'content' => request('post', 'content'),
                'price' => request('post', 'price'),
                'delivery_price' => request('post', 'delivery_price'),
                'images' => request('post', 'images'),
                'user_id' => $this->userInfo['user_id']
            ])->orderInfo()->push()->export(function ($data, $msg) {
                $this->success($msg, url('index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function delivery() {
        target($this->_middle, 'middle')->setParams([
            'refund_no' => request('post', 'refund_no'),
            'delivery_name' => request('post', 'delivery_name'),
            'delivery_no' => request('post', 'delivery_no'),
            'user_id' => $this->userInfo['user_id']
        ])->delivery()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function cancel() {
        target($this->_middle, 'middle')->setParams([
            'refund_no' => request('get', 'refund_no'),
            'user_id' => $this->userInfo['user_id']
        ])->cancel()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}