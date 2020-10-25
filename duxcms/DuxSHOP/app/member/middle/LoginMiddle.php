<?php

/**
 * 基础控制器
 */

namespace app\member\middle;


class LoginMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('登录');
        $this->setName('会员登录');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '登录',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();
        return $this->config;
    }

    private function getNameTip() {
        $this->config = $this->getConfig();
        $nameTip = '手机号/邮箱';
        if($this->config['reg_type'] == 'email') {
            $nameTip = '邮箱';
        }
        if($this->config['reg_type'] == 'tel') {
            $nameTip = '手机号码';
        }
        return $nameTip;
    }

    protected function data() {
        $loginList = target('member/MemberConnect')->getLoginList($this->params['platform']);
        if (!empty($loginList)) {
            foreach ($loginList as $vo) {
                if ($vo['jump']) {
                    return $this->stop('', 302, $vo['url']);
                }
            }
        }
        return $this->run([
            'nameTip' => $this->getNameTip(),
            'loginList' => $loginList
        ]);
    }

    protected function post() {
        $this->params['type'] = $this->params['type'] ? $this->params['type'] : 'all';
        $data = target('Member/Member', 'service')->loginUser($this->params['username'], $this->params['password'], $this->params['type'], $this->params['platform']);
        if(!$data) {
            return $this->stop(target('Member/Member', 'service')->getError());
        }
        return $this->run($data);
    }


    protected function status() {
        $action = $this->params['action'];
        $action = str_replace(DOMAIN, '', $action);
        if(strpos($action, '/', 0) === false) {
            $action = '';
        }
        $msg = '<a href="'.url('member/login/index', ['action' => $action]).'">登录</a> <a href="'.url('member/register/index').'">注册</a>';
        $login = \dux\Dux::cookie()->get('user_login');
        if(empty($login)) {
            return $this->stop($msg);
        }
        if(!target('member/MemberUser')->checkUser($login['uid'], $login['token'])) {
            return $this->stop($msg);
        }
        $userInfo = target('member/MemberUser')->getUser($login['uid']);
        if(!$userInfo) {
            return $this->stop($msg);
        }else {
            return $this->run([], '<a href="'.url('member/Index/index').'">'.$userInfo['show_name'] .'</a> <a href="'.url('member/login/logout').'">退出</a>');
        }

    }



}