<?php

/**
 * 领券中心
 */

namespace app\order\api;

class CouponApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Coupon';

	public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userId,
            'class_id' => $this->data['class_id']
        ])->meta()->data()->export(function ($data) use ($pageLimit) {
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

    public function classList() {
        target($this->_middle, 'middle')->classData()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });

    }

	
	public function receive() {
		target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'coupon_id' => $this->data['id']
        ])->receive()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
	}

}
