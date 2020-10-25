<?php

/**
 * 账户提现
 */

namespace app\member\controller;

class CashController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Cash';

    public function index() {
        $type = request('get', 'type');
        $urlParams = [
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->meta('提现记录', '提现记录', url())->data()->export(function ($data) use ($urlParams, $type) {
            $this->assign($data);
            $this->assign('type', $type);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });

    }

    public function submit() {
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
                $this->success($msg, url('index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
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