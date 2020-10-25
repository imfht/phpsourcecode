<?php

/**
 * 会员注册
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class RegisterApi extends BaseApi {

    protected $_middle = 'member/Register';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'username' => $this->data['username'],
            'password' => $this->data['password'],
            'code' => $this->data['code'],
            'agreement' => 1,
            'imgcode' => $this->data['imgcode'],
        ])->post()->export(function ($loginData) {
            $this->success('账号注册成功!', $loginData);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function forgot() {
        target('member/Forgot', 'middle')->setParams([
            'username' => $this->data['username'],
            'password' => $this->data['password'],
            'code' => $this->data['code'],
            'imgcode' => $this->data['imgcode'],
        ])->post()->export(function ($loginData) {
            $this->success('密码重置成功!', $loginData);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function agreement() {
        target('member/Agreement', 'middle')->data()->export(function ($data) {
            $this->success('ok', $data['content']);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function getCode() {
		if (!filter_var($this->data['username'], \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
		$info = target('member/MemberUser')->getWhereInfo([
            $type => $this->data['username'] 
        ]);
        if (!empty($info)) {
			$this->error('该用户已被注册', 500);
        }
        target($this->_middle, 'middle')->setParams([
            'username' => $this->data['username'],
            'imgcode' => $this->data['imgcode']
        ])->getCode()->export(function () {
            $this->success('验证码已发送,请注意查收!');
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}