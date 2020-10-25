<?php

/**
 * 账户提现
 */

namespace app\member\mobile;

class CashMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'member/Cash';

    public function index() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo,
            ])->meta('提现申请', '提现申请', url())->apply()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            target($this->_middle, 'middle')->setParams(array_merge(request('post'), [
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo
            ]))->applyPost()->export(function ($data, $msg) {
                $this->success($msg, url('log'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function log() {
        $type = request('get', 'type');
        $urlParams = [
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta('提现记录', '提现记录', url())->export(function ($data) use ($urlParams, $type) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('type', $type);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        $type = request('get', 'type');
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page'],
                ]);
            }else {
                $this->error('暂无数据');
            }
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'no' => request('get', 'no'),
        ])->meta('提现进度', '提现进度', url())->info()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}