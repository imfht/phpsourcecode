<?php

/**
 * 领券中心
 */

namespace app\order\mobile;

class CouponMobile extends \app\base\mobile\SiteMobile {


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
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function ajax() {
        $type = request('get', 'type');
        $classId = request('get', 'class_id');
        target($this->_middle, 'middle')->setParams([
            'type' => $type,
            'user_id' => target('member/MemberUser')->getUid(),
            'class_id' => $classId
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