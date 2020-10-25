<?php

/**
 * 商品列表
 */

namespace app\mall\api;

use \app\base\api\BaseApi;

class ListApi extends BaseApi {

    protected $_middle = 'mall/List';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;

        target($this->_middle, 'middle')->setParams([
            'class_id' => $this->data['id'],
            'limit' => $pageLimit,
            'keyword' => $this->data['keyword'],
            'pos' => $this->data['pos'],
            'user_id' => target('member/MemberUser')->getUid(),
            'layer' => 'api'
        ])->data()->filter()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多商品', 404);
            }
        }, function ($message, $code, $url) {
            $this->error('暂无更多商品', 404);
        });

    }

}
