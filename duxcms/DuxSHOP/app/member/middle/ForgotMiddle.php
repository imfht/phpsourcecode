<?php

/**
 * 基础控制器
 */

namespace app\member\middle;


class ForgotMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('找回密码');
        $this->setName('找回密码');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '找回密码',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();

        return $this->config;
    }

    private function getNameTip() {
        $this->config = $this->getConfig();
        $nameTip = '手机号/邮箱';
        if ($this->config['reg_type'] == 'email') {
            $nameTip = '邮箱';
        }
        if ($this->config['reg_type'] == 'tel') {
            $nameTip = '手机号码';
        }

        return $nameTip;
    }

    protected function data() {
        return $this->run([
            'nameTip' => $this->getNameTip(),
            'userConfig' => $this->config,
        ]);
    }

    protected function post() {
        $this->config = $this->getConfig();
        $userName = $this->params['username'];
        $password = $this->params['password'];
        $code = $this->params['code'];
        if (empty($userName) || empty($password)) {
            return $this->stop('用户名或密码未填写！');
        }
        if ($this->config['verify_image']) {
            $imgCode = new \dux\lib\Vcode(90, 37, 4, '', 'code');
            if (!$imgCode->check($this->params['imgcode'])) {
                return $this->stop('图片验证码输入不正确!');
            }
        }
        $loginData = target('member/Member', 'service')->forgotUser($userName, $password, $code);
        if (!$loginData) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        return $this->run($loginData);
    }

}