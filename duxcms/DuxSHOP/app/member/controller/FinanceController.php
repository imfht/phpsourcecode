<?php

/**
 * 交易记录
 */

namespace app\member\controller;


class FinanceController extends \app\member\controller\MemberController {

    protected $_middle = 'member/Finance';

    public function index() {
        $type = request('get', 'type');
        $urlParams = [
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->meta('我的钱包', '我的钱包', url())->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function statis() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->meta('账户统计', '账户统计', url())->statistical()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}