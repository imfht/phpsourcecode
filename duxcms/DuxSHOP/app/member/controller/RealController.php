<?php

/**
 * 实名认证
 */

namespace app\member\controller;

class RealController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Real';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function info() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id']
            ])->info()->meta()->export(function ($data) {
                $this->assign($data);
                $this->assign('userInfo', $this->userInfo);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $data = request('post');
            target($this->_middle, 'middle')->setParams(
                array_merge($data, [
                    'user_id' => $this->userInfo['user_id'],
                    'user_info' => $this->userInfo
                ]))->post()->export(function ($data, $msg) {
                $this->success($msg, url('index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function getCode() {
        $valType = request('post', 'valtype');
        target($this->_middle, 'middle')->setParams([
            'user_info' => $this->userInfo,
            'val_type' => $valType,
        ])->getCode()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }
}