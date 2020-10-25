<?php

/**
 * 基础控制器
 */

namespace app\member\controller;


class LoginController extends \app\member\controller\MemberController {

    protected $noLogin = true;
    protected $_middle = 'member/Login';


    public function index() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'platform' => 'web'
            ])->meta()->data()->export(function ($data) {
                $this->assign($data);
                $this->dialogDisplay();
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
                'platform' => 'web',
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
                $this->dialogDisplay();
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
                'platform' => 'web'
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

    public function status() {
        $action = request('', 'action');
        target('member/Login', 'middle')->setParams([
            'action' => $action,
        ])->status()->export(function ($data, $msg){
            $this->success($msg);

        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}