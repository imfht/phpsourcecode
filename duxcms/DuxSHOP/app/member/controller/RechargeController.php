<?php

/**
 * 会员充值
 */

namespace app\member\controller;


class RechargeController extends \app\member\controller\MemberController {


    protected $_middle = 'member/Recharge';

    public function index() {
        if(!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'web'
            ])->meta('账户充值', '账户充值', url(''))->data()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }else {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'web',
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
        ])->meta('充值记录','充值记录',url())->log()->export(function ($data) {
            $this->assign($data);
            $this->assign('page', $this->htmlPage($data['pageData']['raw']));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}