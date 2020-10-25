<?php

/**
 * 退款管理
 */

namespace app\order\api;

class RefundApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Refund';

    public function index() {
        $pageLimit = $this->data['limit'];
        $type = $this->data['type'];
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'limit' => $pageLimit,
            'type' => $type
        ])->data()->export(function ($data) use ($pageLimit) {
            if (!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            } else {
                $this->error('暂无更多', 404);
            }
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'refund_no' => $this->data['refund_no'],
            'user_id' => $this->userInfo['user_id']
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function push() {
        if (!isPost()) {
            target($this->_middle, 'middle')->setParams([
                'id' => $this->data['id']
            ])->orderInfo()->export(function ($data) {
                $this->success('ok', $data);
            }, function ($message, $code) {
                $this->error($message, $code);
            });
        } else {
            target($this->_middle, 'middle')->setParams([
                'id' => $this->data['id'],
                'cause' => $this->data['cause'],
                'content' => $this->data['content'],
                'money' => $this->data['money'],
                'images' => $this->data['images'],
                'user_id' => $this->userInfo['user_id']
            ])->orderInfo()->push()->export(function ($data, $msg) {
                $this->success($msg);
            }, function ($message, $code) {
                $this->error($message, $code);
            });
        }
    }

    public function delivery() {
        target($this->_middle, 'middle')->setParams([
            'return_no' => $this->data['return_no'],
            'delivery_name' => $this->data['delivery_name'],
            'delivery_no' => $this->data['delivery_no'],
            'user_id' => $this->userInfo['user_id']
        ])->delivery()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function cancel() {
        target($this->_middle, 'middle')->setParams([
            'refund_no' => $this->data['refund_no'],
            'user_id' => $this->userInfo['user_id']
        ])->cancel()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}