<?php

/**
 * 头像
 */

namespace app\member\mobile;

class AvatarMobile extends \app\base\mobile\SiteMobile {

    protected $_middle = 'member/Avatar';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'id' => request('get', 'id'),
            'type' => request('get', 'type'),
        ])->avatar()->export(function ($data) {
            header('content-type: image/png');
            echo file_get_contents($data['file']);
        });
    }
}