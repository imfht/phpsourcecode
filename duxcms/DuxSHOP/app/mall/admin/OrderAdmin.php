<?php

/**
 * 订单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class OrderAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MallOrder';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商城订单',
                'description' => '管理商城商品订单',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.order_no',
            'type' => 'type',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time'
        ];
    }

    public function _indexWhere($whereMaps) {

        switch($whereMaps['type']) {
            case 1:
                $where = 'B.order_status = 1 AND B.pay_status = 0';
                break;
            case 2:
                $where = 'B.order_status = 1 AND B.parcel_status = 0';
                break;
            case 3:
                $where = 'B.order_status = 1 AND B.delivery_status = 0';
                break;
            case 4:
                $where = 'B.order_status = 1 AND B.delivery_status = 1 AND B.order_complete_status = 0';
                break;
            case 5:
                $where = 'B.order_status = 1 AND B.order_complete_status = 1';
                break;
            case 6:
                $where = 'B.order_status = 0';
                break;
        }
        if(!empty($where)) {
            $whereMaps['_sql'] = $where;
        }
        unset($whereMaps['type']);

        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'D.order_create_time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'D.order_create_time <= ' . $stopTime;
        }


        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        return $whereMaps;
    }

    public function _indexAssign($pageMaps) {
        $orderNo = $pageMaps['order_no'];
        return array(
            'order_no' => $orderNo
        );
    }

    public function _indexOrder() {
        return 'B.order_id desc';
    }


    public function info() {
        $id = request('get', 'id', 0, 'intval');
        if(empty($id)) {
            $this->error404();
        }
        $info = target('mall/MallOrder')->getWhereInfo([
            'B.order_id' => $id
        ]);
        if(empty($info)) {
            $this->error404();
        }
        $payData = [];
        if($info['pay_status']) {
            $payList = target('member/PayConfig')->typeList();
            foreach ($info['pay_data'] as $vo) {
                $payTypeInfo = $payList[$vo['way']];
                $payData[] = array_merge(target($payTypeInfo['target'], 'pay')->getLog($vo['id']), ['pay_type' => $payTypeInfo['name']]);
            }
        }

        $deliveryList = target('order/OrderDelivery')->loadList([
            'A.order_id' => $info['order_id']
        ]);

        $logList = target('order/OrderLog')->loadList([
            'order_id' => $info['order_id']
        ]);

        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $info['order_id']
        ]);

        $status = target('order/Order', 'service')->getManageStatus($info);

        $takeInfo = target('order/OrderTake')->getInfo($info['take_id']);

        $groupLog = [];
        $groupUser = [];
        $parcelInfo = target('order/OrderParcel')->getWhereInfo([
            'A.order_id' =>  $id
        ]);

        $this->assign('info', $info);
        $this->assign('payData', $payData);
        $this->assign('status', $status);
        $this->assign('deliveryList', $deliveryList);
        $this->assign('logList', $logList);
        $this->assign('orderGoods', $orderGoods);
        $this->assign('takeInfo', $takeInfo);
        $this->assign('groupUser', $groupUser);
        $this->assign('groupLog', $groupLog);
        $this->assign('parcelInfo', $parcelInfo);
        $this->systemDisplay();
    }

}