<?php

/**
 * 资金账户
 */

namespace app\member\api;

class FinanceApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Finance';

    public function index() {
        $type = $this->data['type'];
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type,
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
        }, function () {
            $this->error('暂无更多记录', 404);
        });

    }

}