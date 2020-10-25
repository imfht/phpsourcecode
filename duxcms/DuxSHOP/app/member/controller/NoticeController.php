<?php

/**
 * 会员通知
 */

namespace app\member\controller;


class NoticeController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Notice';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], []));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
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

}