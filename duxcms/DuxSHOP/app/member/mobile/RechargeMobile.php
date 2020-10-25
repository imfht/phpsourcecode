<?php

/**
 * 会员充值
 */

namespace app\member\mobile;


class RechargeMobile extends \app\member\mobile\MemberMobile {


    protected $_middle = 'member/Recharge';

    public function index() {
        if(!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'mobile'
            ])->meta('账户充值', '账户充值', url())->data()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }else {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'mobile',
                'type' => request('post', 'type'),
                'money' => request('post', 'money'),
                'user_id' => $this->userInfo['user_id'],
            ])->recharge()->export(function ($data, $msg) {
                $this->success($msg, $data['url']);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function complete() {
        target($this->_middle, 'middle')->setParams([
            'pay_no' => request('get', 'pay_no'),
            'pay_sign' => request('get', 'pay_sign'),
        ])->meta('充值完成', '充值完成', URL)->complete()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function log() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->meta('充值记录','充值记录',url())->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'no' => request('get', 'no'),
        ])->meta('充值详情', '充值详情', url())->info()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->log()->export(function ($data) {
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


}