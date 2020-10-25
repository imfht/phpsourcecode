<?php

/**
 * 我的推荐
 */
namespace app\sale\api;

class UserHasApi extends \app\member\api\MemberApi {

    protected $_middle = 'sale/UserHas';

    public function index() {

        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userInfo['user_id']
        ])->meta()->data()->export(function ($data) {

            if (!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($data['pageLimit'], $data['pageList'], $data['pageData']),
                ]);
            } else {
                $this->error('暂无更多记录', 404);
            }
        }, function ($message, $code) {
            $this->error('暂无更多记录', 404);
        });

    }


}