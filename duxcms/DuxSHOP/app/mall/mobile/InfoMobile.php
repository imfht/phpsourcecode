<?php

/**
 * 商城详情
 */

namespace app\mall\mobile;

class InfoMobile extends \app\base\mobile\SiteMobile {

    protected $_middle = 'mall/Info';

    public function index() {
        $id = request('get', 'id', 0, 'intval');
        $proId = request('get', 'pro_id', 0, 'intval');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $id,
            'pro_id' => $proId,
            'user_id' => target('member/MemberUser')->getUid(),
            'layer' => 'mobile'
        ])->meta()->classInfo()->data()->export(function ($data) {
            $this->assign($data);
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function content() {
        $id = request('get', 'id', 0, 'intval');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $id,
            'layer' => 'mobile'
        ])->meta()->data()->export(function ($data) {
            $this->assign($data);
            $this->mobileDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

    public function spec() {
        $id = request('get', 'id', 0, 'intval');
        $proId = request('get', 'pro_id', 0, 'intval');
        target($this->_middle, 'middle')->setParams([
            'mall_id' =>$id,
            'pro_id' => $proId,
        ])->spec()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->error($message, $url, $code);
        });
    }

}