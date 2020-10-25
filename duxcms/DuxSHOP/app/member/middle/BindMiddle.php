<?php

/**
 * 基础控制器
 */

namespace app\member\middle;


class BindMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];
    private $oathData = [];
    private $loginInfo = [];

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('绑定账号');
        $this->setName('绑定账号');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '绑定账号',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getOathData() {
        if ($this->oathData) {
            return $this->oathData;
        }
        $this->params['open_id'] = html_clear($this->params['open_id']);
        $this->params['type'] = html_clear($this->params['type']);
        if (empty($this->params['open_id']) || empty($this->params['type'])) {
            $this->stop('oath参数错误!');
            return false;
        }
        $loginInfo = target('member/MemberConnect')->getWhereInfo([
            'open_id' => $this->params['open_id'],
            'type' => $this->params['type']
        ]);
        if (empty($loginInfo)) {
            $this->stop('第三方登录信息不存在!');
            return false;
        }
        if ($loginInfo['user_id']) {
            $this->stop('已绑定会员账号', 302, url('member/Login/index'));
            return false;
        }
        $this->oathData = unserialize($loginInfo['data']);
        $this->loginInfo = $loginInfo;

        return $this->oathData;
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();
        return $this->config;
    }

    private function getNameTip() {
        $this->getConfig();
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
        if (!$this->getOathData()) {
            $this->status = false;
            return $this;
        }
        $hookField = [];
        $hookList = run('service', 'member', 'regField');
        foreach ($hookList as $app => $vo) {
            if (!empty($vo)) {
                $hookField = array_merge($hookField, $vo);
            }
        }

        return $this->run([
            'nameTip' => $this->getNameTip(),
            'userConfig' => $this->config,
            'loginInfo' => $this->loginInfo,
            'oathData' => $this->oathData,
            'hookField' => $hookField
        ]);

    }


    protected function post() {
        if (!$this->getOathData()) {
            $this->status = false;

            return $this;
        }
        $model = target('member/MemberUser');
        $model->beginTransaction();
        $userName = $this->params['username'];
        $password = $this->params['password'];
        $code = $this->params['code'];
        if (empty($userName) || empty($password)) {
            return $this->stop('用户名或密码未填写！');
        }
        $agreement = intval($this->params['agreement']);
        if (!$agreement) {
            return $this->stop('请先阅读注册协议');
        }
        $userInfo = target('member/Member', 'service')->isUser($userName);

        if ($this->config['verify_status']) {
            $status = target('member/Member', 'service')->checkVerify($userName, $code);
            if (!$status) {
                return $this->stop(target('member/Member', 'service')->getError());
            }
        }

        if ($userInfo) {
            $loginData = target('member/Member', 'service')->loginUser($userName, $password, '', $this->params['platform']);
            if (!$loginData) {
                $model->rollBack();

                return $this->stop(target('member/Member', 'service')->getError());
            }
            $userInfo = $loginData['data'];
            if (!empty($userInfo['avatar'])) {
                $this->oathData['avatar'] = '';
            }
            if (!empty($userInfo['nickname'])) {
                $this->oathData['nickname'] = '';
            }
        } else {
            $loginData = target('member/Member', 'service')->regUser($userName, $password, $code, $this->oathData['nickname']);
            if (!$loginData) {
                return $this->stop(target('member/Member', 'service')->getError());
            }
        }
        if (!target('member/Member', 'service')->connectUser($this->loginInfo['connect_id'], $loginData['uid'], $this->oathData['nickname'], $this->oathData['avatar'])) {
            $model->rollBack();

            return $this->stop(target('member/Member', 'service')->getError());
        }
        $model->commit();

        return $this->run($loginData);

    }


}