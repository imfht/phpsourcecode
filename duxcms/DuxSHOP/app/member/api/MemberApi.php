<?php

/**
 * 系统基础
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class MemberApi extends BaseApi {

    protected $userId = 0;
    protected $userInfo = [];

    public function __construct() {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 检测登录
     */
    private function checkLogin() {
        if (!target('member/MemberUser')->checkUser($_SERVER['HTTP_AUTHUID'], $_SERVER['HTTP_AUTHTOKEN'])) {
            $this->error(target('Member/MemberUser')->getError(), 401);
        }
        $this->userId = $_SERVER['HTTP_AUTHUID'];
        $this->userInfo = target('Member/MemberUser')->getUser($this->userId);
        define('USER_ID', $this->userId);
    }

    /**
     * 获取用户资料
     */
    public function info() {
        $info = $this->userInfo;
        if (empty($info)) {
            $this->error(target('Member/MemberUser')->getError());
        }
        $this->success('ok', $info);
    }

    /**
     * 更新用户资料
     */
    public function update() {
        target('member/Setting', 'middle')->setParams(array_merge($this->data, ['user_id' => $this->userId]))->putInfo()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 更新头像
     */
    public function avatar() {
        target('member/Setting', 'middle')->setParams(['user_id' => $this->userInfo['user_id']])->putAvatar()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });

    }

    /**
     * 上传照片
     */
    public function upload() {
        target('member/Upload', 'middle')->setParams([
            'user_id' => $this->userId,
            'width' => 1000,
            'height' => 1000
        ])->post()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 修改密码
     */
    public function password() {
        target('member/Setting', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'old_password' => $this->data['old_password'],
            'password' => $this->data['password'],
            'password2' => $this->data['password2'],
        ])->putPassword()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function payPassword() {
        target('member/Setting', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'user_info' => $this->userInfo,
            'val_type' => $this->data['valtype'],
            'val_code' => $this->data['val_code'],
            'password' => $this->data['password'],
        ])->putPayPassword()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


    /**
     * 关于会员
     */
    public function about() {
        target('member/Index', 'middle')->about()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}