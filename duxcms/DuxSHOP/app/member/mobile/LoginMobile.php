<?php

/**
 * 基础控制器
 */

namespace app\member\mobile;


class LoginMobile extends \app\member\mobile\MemberMobile {

    protected $noLogin = true;
    protected $_middle = 'member/Login';


    public function index() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'mobile'
            ])->meta()->data()->export(function ($data) {
                $this->assign($data);
                $this->setTpl('nav', [
                    'name' => '注册',
                    'url' => url('member/Register/index')
                ]);
                $this->otherDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $userName = request('post', 'username');
            $password = request('post', 'password');
            $remember = request('post', 'remember', 0);
            target($this->_middle, 'middle')->setParams([
                'username' => $userName,
                'password' => $password,
                'platform' => 'mobile',
            ])->post()->export(function ($loginData) use ($remember) {
                $time = $remember ? 2592000 : 86400;
                \dux\Dux::cookie()->set('user_login', [
                    'uid' => $loginData['uid'],
                    'token' => $loginData['token']
                ], $time);
                $this->success('账号登录成功!', $this->action ? $this->action : url('member/Index/index'));

            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function bind() {
        if (!isPost()) {
            target('member/Bind', 'middle')->setParams([
                'open_id' => request('get', 'open_id'),
                'type' => request('get', 'type'),
            ])->meta()->data()->export(function ($data) {
                $this->assign($data);
                $this->otherDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $userName = request('post', 'username');
            $password = request('post', 'password');
            $code = request('post', 'code');
            $remember = request('post', 'remember', 0, 'intval');
            $agreement = request('post', 'agreement', 0, 'intval');
            target('member/Bind', 'middle')->setParams([
                'open_id' => request('post', 'open_id'),
                'type' => request('post', 'type'),
                'username' => $userName,
                'password' => $password,
                'code' => $code,
                'agreement' => $agreement,
                'platform' => 'mobile'
            ])->post()->export(function ($loginData) use ($remember, $agreement) {
                $time = $remember ? 2592000 : 86400;
                \dux\Dux::cookie()->set('user_login', [
                    'uid' => $loginData['uid'],
                    'token' => $loginData['token']
                ], $time);
                $this->success('账号绑定成功!', $this->action ? $this->action : url('member/Index/index'));

            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function logout() {
        \dux\Dux::cookie()->del('user_login');
        $this->redirect(url('index'));
    }

}