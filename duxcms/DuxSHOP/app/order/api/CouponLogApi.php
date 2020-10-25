<?php

/**
 * 我的优惠券
 */

namespace app\order\api;

class CouponLogApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/CouponLog';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userId,
        ])->data()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多', 404);
            }
        }, function ($message, $code, $url) {
            $this->error('暂无更多', 404);
        });
    }

    public function del() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => target('member/MemberUser')->getUid(),
            'log_id' => request('', 'id')
        ])->del()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->errorCallback($message, $code, $url);
        });
    }


}