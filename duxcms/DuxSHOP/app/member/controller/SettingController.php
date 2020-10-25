<?php

/**
 * 账户管理
 */

namespace app\member\controller;


class SettingController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Setting';

    public function index() {
        if (!isPost()) {
            target($this->_middle, 'middle')->meta('修改资料', '修改资料', url())->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            });
        } else {
            target($this->_middle, 'middle')->setParams(array_merge(request('post'), ['user_id' => $this->userInfo['user_id']]))->putInfo()->export(function ($data, $msg) {
                $this->success($msg);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function avatar() {
        if (!isPost()) {
            target($this->_middle, 'middle')->meta('修改头像', '修改头像', url())->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            });
        } else {
            target($this->_middle, 'middle')->setParams(['user_id' => $this->userInfo['user_id']])->putAvatar()->export(function ($data, $msg) {
                $this->success($msg);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function password() {
        if (!isPost()) {
            target($this->_middle, 'middle')->meta('修改密码', '修改密码', url())->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            });
        } else {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'old_password' => request('post', 'old_password'),
                'password' => request('post', 'password'),
                'password2' => request('post', 'password2'),
            ])->putPassword()->export(function ($data, $msg) {
                $this->success($msg);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function payPassword() {
        if (!isPost()) {
            target($this->_middle, 'middle')->meta('支付密码', '支付密码', url())->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            });
        } else {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo,
                'val_type' => request('post', 'valtype'),
                'val_code' => request('post', 'val_code'),
                'password' => request('post', 'password'),
            ])->putPayPassword()->export(function ($data, $msg) {
                $this->success($msg, url('member/Index/index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function username() {
        if (!isPost()) {
            target($this->_middle, 'middle')->meta('更改帐号', '更改帐号', url())->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            });
        } else {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo,
                'type' => request('post', 'type'),
                'val_type' => request('post', 'valtype'),
                'val_code' => request('post', 'val_code'),
            ])->putUsername()->export(function ($data, $msg) {
                $this->success($msg);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }


}