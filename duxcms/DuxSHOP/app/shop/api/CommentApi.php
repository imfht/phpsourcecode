<?php

/**
 * 评价列表
 */

namespace app\shop\api;

use \app\base\api\BaseApi;

class CommentApi extends BaseApi {

    protected $_middle = 'shop/Comment';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 20;
        target($this->_middle, 'middle')->setParams([
            'id' => $this->data['id'],
            'app' => $this->data['app'],
            'type' => $this->data['type'],
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