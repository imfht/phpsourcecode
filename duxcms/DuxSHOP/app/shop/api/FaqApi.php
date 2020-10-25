<?php

/**
 * 商品咨询
 */

namespace app\shop\api;

use \app\member\api\MemberApi;

class FaqApi extends MemberApi {

    protected $_middle = 'shop/Faq';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'id' => $this->data['id'],
            'app' => $this->data['app'],
            'limit' => $pageLimit,
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