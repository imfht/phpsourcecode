<?php

/**
 * 银行卡管理
 */

namespace app\member\controller;

class CardController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Card';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function add() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id']
            ])->meta('添加银行卡', '添加银行卡')->realInfo()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay('info');
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $data = request('post');
            target($this->_middle, 'middle')->setParams(array_merge($data, [
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo,
            ]))->post()->export(function ($data, $msg) {
                $this->success($msg, url('index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function edit() {
        $id = request('get', 'id');
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'card_id' => $id
            ])->meta('编辑银行卡', '编辑银行卡')->realInfo()->info()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay('info');
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            $data = request('post');
            target($this->_middle, 'middle')->setParams(array_merge($data, [
                'user_id' => $this->userInfo['user_id'],
                'user_info' => $this->userInfo,
            ]))->post()->export(function ($data, $msg) {
                $this->success($msg, url('index'));
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

    public function del() {
        target($this->_middle, 'middle')->setParams([
            'card_id' => request('get', 'id'),
            'user_id' => $this->userInfo['user_id']
        ])->del()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }
}