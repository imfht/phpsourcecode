<?php

/**
 * 领券中心
 */

namespace app\order\controller;


class CouponController extends \app\base\controller\SiteController {

    protected $_middle = 'order/Coupon';

    public function index() {
        $type = request('get', 'type');
        $classId = request('get', 'class_id');
        $urlParams = [
            'type' => $type,
            'class_id' => $classId
        ];
        target($this->_middle, 'middle')->setParams([
            'user_id' => target('member/MemberUser')->getUid(),
            'type' => $type,
            'class_id' => $classId
        ])->meta()->classData()->data()->export(function ($data) use ($urlParams) {
            $this->assign($data);
            $this->assign('urlParams', $urlParams);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], $urlParams));
            $this->siteDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


    public function receive() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => target('member/MemberUser')->getUid(),
            'coupon_id' => request('', 'id')
        ])->receive()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}