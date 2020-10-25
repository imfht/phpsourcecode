<?php

/**
 * 积分记录
 */

namespace app\member\mobile;


class PointsMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'member/Points';

    public function index() {
        $type = request('get', 'type');
        $urlParams = [
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->meta()->account()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->setTpl('nav', [
                'name' => '获取记录',
                'url' => url('charge')
            ]);
            $this->assign('urlParams', $urlParams);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function log() {
        $type = request('get', 'type');
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->data()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page'],
                ]);
            }else {
                $this->error('暂无数据');
            }
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'no' => request('get', 'no'),
        ])->meta('交易详情', '交易详情', url())->info()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function charge() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->meta()->account()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function chargeLog() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->charge()->export(function ($data) {
            if(!empty($data['pageList'])) {
                $this->success([
                    'data' => $data['pageList'],
                    'page' => $data['pageData']['page'],
                ]);
            }else {
                $this->error('暂无数据');
            }
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }



}