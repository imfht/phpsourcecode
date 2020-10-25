<?php

/**
 * 系统登录
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class LoginApi extends BaseApi {

    protected $_middle = 'member/Login';

    /**
     * 用户登录
     */
    public function index() {
        target($this->_middle, 'middle')->setParams([
            'username' => $this->data['username'],
            'password' => $this->data['password'],
            'platform' => 'api',
        ])->post()->export(function ($loginData) {
            $this->success('账号登录成功!', $loginData);
        }, function ($message, $code) {
            $this->error($message, $code);
        });

    }

    /**
     * 第三方登录
     */
    public function bind() {
        target('member/Bind', 'middle')->setParams([
            'open_id' => $this->data['open_id'],
            'type' => $this->data['type'],
            'username' => $this->data['username'],
            'password' => $this->data['password'],
            'code' => $this->data['code'],
            'agreement' => 1,
            'platform' => 'api'
        ])->post()->export(function ($loginData) {
            $this->success('账号绑定成功!', $loginData);
        }, function ($message, $code) {
            $this->error($message, $code);
        });

    }

}