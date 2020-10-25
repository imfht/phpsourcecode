<?php

/**
 * 评论管理
 */

namespace app\order\mobile;

class CommentMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'order/Comment';

    public function push() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'id' => request('get', 'id'),
            ])->meta()->info()->export(function ($data) {
                $this->assign($data);
                $this->memberDisplay();
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        } else {
            target($this->_middle, 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'id' => request('post', 'id'),
                'level' => request('post', 'level'),
                'content' => request('post', 'content'),
                'images' => request('post', 'images'),
                'store' => request('post', 'store'),
            ])->info()->post()->export(function ($data, $msg) {
                $this->success($msg, $this->action);
            }, function ($message, $code, $url) {
                $this->errorCallback($message, $code, $url);
            });
        }
    }

}