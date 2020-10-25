<?php

/**
 * 我的优惠券
 */

namespace app\order\controller;


class CouponLogController extends \app\member\controller\MemberController {

    protected $_middle = 'order/CouponLog';

    public function index() {
        $type = request('get', 'type');
        $urlParams = [
            'type' => $type
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type
        ])->meta()->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function del() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => target('member/MemberUser')->getUid(),
            'log_id' => request('', 'id')
        ])->del()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}