<?php

/**
 * 收藏列表
 */

namespace app\shop\api;

use \app\member\api\MemberApi;

class FollowApi extends MemberApi {

    protected $_middle = 'shop/Follow';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 20;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'limit' => $pageLimit
        ])->data()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多记录', 404);
            }
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}