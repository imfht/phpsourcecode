<?php
 
/**
 * 会员首页
 */

namespace app\member\mobile;


class IndexMobile extends \app\member\mobile\MemberMobile {

    protected $_middle = 'member/Index';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'mobile',
            'user_info' => $this->userInfo
        ])->meta()->data()->export(function ($data) {
            unset($data['menuList']['setting']);
            $this->assign($data);
            $this->memberDisplay('', false);
        });
    }

    public function setting() {
        target($this->_middle, 'middle')->setParams([
            'platform' => 'mobile',
            'user_info' => $this->userInfo
        ])->meta('个人设置', '个人设置')->data()->export(function ($data) {
            $this->assign($data);
            $this->assign('menuList', $data['menuList']['setting']['menu']);
            $this->memberDisplay();
        });
    }

    public function about() {
        target($this->_middle, 'middle')->meta('关于我们', '关于我们')->about()->export(function ($data) {
            $this->assign($data);
            $this->memberDisplay();
        });

    }


}