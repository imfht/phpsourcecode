<?php

/**
 * 推荐关系
 */

namespace app\sale\controller;

class UserHasController extends \app\member\controller\MemberController {

    protected $_middle = 'sale/UserHas';

    public function index() {

        $type = request('get', 'type');
        target($this->_middle, 'middle')->setParams([
            'type' => $type,
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) use ($type) {
            $this->assign($data);
            $this->assign('page', $this->htmlPage($data['pageData']['raw'], ['type' => $type]));
            $this->memberDisplay();
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }

}