<?php

/**
 * 商城分类
 */

namespace app\mall\controller;

class InfoController extends \app\base\controller\SiteController {

    protected $_middle = 'mall/Info';

    public function index() {
        $id = request('get', 'id', 0, 'intval');
        $proId = request('get', 'pro_id', 0, 'intval');
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $id,
            'pro_id' => $proId,
            'user_id' => target('member/MemberUser')->getUid()
        ])->meta()->classInfo()->data()->export(function ($data) {
            $this->assign($data);
            $this->siteDisplay($data['tpl']);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}