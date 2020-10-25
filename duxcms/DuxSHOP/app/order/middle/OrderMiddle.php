<?php

/**
 * 订单管理
 */

namespace app\order\middle;

class OrderMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'order/Order';


    protected function meta($title = '我的订单', $name = '我的订单', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => $name,
                'url' => url('index')
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);

        $where = [];
        $where['order_user_id'] = $userId;
        if ($type) {
            $where['order_status'] = 1;
        }
        switch ($type) {
            case 1:
                $where['pay_type'] = 1;
                $where['pay_status'] = 0;
                $where['delivery_status'] = 0;
                break;
            case 2:
                $where['_sql'][] = '(pay_type = 0 OR pay_status = 1)';
                $where['delivery_status'] = 0;
                $where['order_complete_status'] = 0;
                break;
            case 3:
                $where['delivery_status'] = 1;
                $where['order_complete_status'] = 0;
                break;
            case 4:
                $where['order_complete_status'] = 1;
                $where['comment_status'] = 0;
                break;
        }
        

        $pageLimit = 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'order_id desc');
		
        $orderIds = [];
        foreach ($list as $key => $vo) {
            $orderIds[] = $vo['order_id'];
        }
		
        if (!empty($orderIds)) {
            $orderGoods = target('order/OrderGoods')->loadList([
                '_sql' => 'order_id in (' . implode(',', $orderIds) . ')'
            ]);
            $orderGroup = [];
            foreach ($orderGoods as $key => $vo) {
                $orderGroup[$vo['order_id']][] = $vo;
            }
            foreach ($list as $key => $vo) {
                $list[$key]['order_items'] = $orderGroup[$vo['order_id']];
            }
        }
        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }


    protected function getInfo() {
        $orderNo = html_clear($this->params['order_no']);
        $userId = intval($this->params['user_id']);
        if(empty($orderNo)) {
            return false;
        }
        $target = target('order/Order');
        $orderInfo = $target->getWhereInfo([
            'order_no' => $orderNo,
            'order_user_id' => $userId
        ]);
        if(empty($orderInfo)) {
            return false;
        }
        return $orderInfo;
    }

    protected function cancel() {
        $orderInfo = $this->getInfo();
        if(empty($orderInfo)) {
            return $this->stop('无法操作订单!');
        }
        $parcelInfo = target('order/OrderParcel')->getWhereInfo([
            'A.order_id' => $orderInfo['order_id']
        ]);
        if($orderInfo['status_data']['action'] <> 'pay' && $orderInfo['status_data']['action'] <> 'parcel' && $parcelInfo['status'] > 1) {
            return $this->stop('无法取消该订单!');
        }
        $serviceGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id'],
            'service_status' => 1
        ]);
        if($serviceGoods) {
            return $this->stop('无法取消该订单,订单正在售后中!');
        }
        $model = target('order/Order');
        $model->beginTransaction();
        $refund = false;
        if($orderInfo['status_data']['action'] == 'parcel') {
            $refund = true;
        }
        if(!target('order/Order', 'service')->cancelOrder($orderInfo['order_id'], $refund)) {
            $model->rollBack();
            return $this->stop(target('order/Order', 'service')->getError());
        }
        $model->commit();
        return $this->run([], '取消订单成功!');
    }

    protected function confirm() {
        $orderInfo = $this->getInfo();
        if(empty($orderInfo)) {
            return $this->stop('无法操作订单!');
        }
        if($orderInfo['status_data']['action'] <> 'receive') {
            return $this->stop('无法进行确认收货!');
        }
        $serviceGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id'],
            'service_status' => 1
        ]);
        if($serviceGoods) {
            return $this->stop('无法取消该订单,订单正在售后中!');
        }
        $model = target('order/Order');
        $model->beginTransaction();
        if(!target('order/Order', 'service')->confirmOrder($orderInfo['order_id'])) {
            $model->rollBack();
            return $this->stop(target('order/Order', 'service')->getError());
        }
        $model->commit();
        return $this->run([], '确认收货成功!');
    }

    protected function delivery() {
        $orderInfo = $this->getInfo();
        $num = intval($this->data['num']);
        $deliveryList = target('order/OrderDelivery')->loadList([
            'A.order_id' => $orderInfo['order_id']
        ], 0, 'delivery_id asc');

        $deliveryInfo = $deliveryList[$num];
        if(empty($deliveryInfo)) {
            return $this->stop('物流信息不存在!', 404);
        }
        $waybillLog = target('order/Order', 'service')->getWaybillLog($deliveryInfo['delivery_id']);
        $msg = '暂无物流信息';
        if(empty($waybillLog)) {
            $msg = target('order/Order', 'service')->getError();
            $waybillLog = [];
        }
        return $this->run([
            'deliveryList' => $deliveryList,
            'deliveryInfo' => $deliveryInfo,
            'orderInfo' => $orderInfo,
            'num' => $num,
            'logList' => $waybillLog,
            'msg' => $msg
        ], $msg);
    }





}