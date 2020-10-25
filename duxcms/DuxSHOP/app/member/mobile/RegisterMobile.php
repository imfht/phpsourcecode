<?php

/**
 * 基础控制器
 */

namespace app\member\mobile;


class RegisterMobile extends \app\member\mobile\MemberMobile {


    protected $noLogin = true;
    protected $_middle = 'member/Register';

    public function index() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams(['reg_type' => request('get', 'reg_type')])->meta()->data()->export(function ($data) {
                $regType = $data['regType'];
                if ($regType == 'tel') {
                    $nav = ['name' => '邮箱注册', 'url' => url('', ['reg_type' => 'email'])];
                } else {
                    $nav = ['name' => '手机注册', 'url' => url('', ['reg_type' => 'tel'])];
                }
				$this->setTpl('nav', $nav);
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
            $imgcode = request('post', 'imgcode');
            $regType = request('get', 'reg_type');
            target($this->_middle, 'middle')->setParams([
                'username' => $userName,
                'password' => $password,
                'code' => $code,
                'agreement' => $agreement,
                'imgcode' => $imgcode,
                'reg_type' => $regType,
            ])->post()->export(function ($loginData) use ($remember) {
                $time = $remember ? 2592000 : 86400;
                \dux\Dux::cookie()->set('user_login', [
                    'uid' => $loginData['uid'],
                    'token' => $loginData['token']
                ], $time);
                $this->success('账号注册成功!', $this->action ? $this->action : url('member/Index/index'));

            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function getCode() {
        $userName = request('post', 'username');
        $imgcode = request('post', 'imgcode');
		
		if (!filter_var($userName, \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
		
		if(request('get', 'register')){
			$info = target('member/MemberUser')->getWhereInfo([
				$type => $userName
			]);
			if (!empty($info)) {
				$this->errorCallback('该用户已被注册!');
			}
		}
		
        target($this->_middle, 'middle')->setParams([
            'username' => $userName,
            'imgcode' => $imgcode
        ])->getCode()->export(function () {
            $this->success('验证码已发送,请注意查收!');
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


    public function code() {
        $this->getImgCode()->showImage();
    }

    public function forgot() {
        if (!isPost()) {
            target('member/Forgot', 'middle')->meta()->data()->export(function ($data) {
                $this->assign($data);
                $this->setTpl('nav', [
                    'name' => '登录',
                    'url' => url('member/Login/index')
                ]);
                $this->otherDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $userName = request('post', 'username');
            $password = request('post', 'password');
            $code = request('post', 'code');
            $imgcode = request('post', 'imgcode');
            target('member/Forgot', 'middle')->setParams([
                'username' => $userName,
                'password' => $password,
                'code' => $code,
                'imgcode' => $imgcode
            ])->post()->export(function ($loginData) {
                \dux\Dux::cookie()->set('user_login', [
                    'uid' => $loginData['uid'],
                    'token' => $loginData['token']
                ], 86400);
                $this->success('密码修改成功!', $this->action ? $this->action : url('member/Index/index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }


    public function agreement() {
        target('member/Agreement', 'middle')->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->otherDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}