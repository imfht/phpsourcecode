<?php

/**
 * 推荐关系
 */

namespace app\sale\mobile;

class UserHasMobile extends \app\member\mobile\MemberMobile {

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

    public function ajax() {
        $type = request('get', 'type');
        target($this->_middle, 'middle')->setParams([
            'type' => $type,
            'user_id' => $this->userInfo['user_id']
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
            $this->error('暂无数据');
        });

    }

}