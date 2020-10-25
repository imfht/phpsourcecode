<?php

/**
 * 头像输出
 */

namespace app\member\controller;

class AvatarController extends \app\base\controller\SiteController {

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