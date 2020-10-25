<?php

/**
 * 订单统计
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;


class OrderStatisAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Order';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '订单统计',
                'description' => '订单统计信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function index() {

        $pageMaps = [];
        $startTime = request('', 'start_time', 0);
        $stopTime = request('', 'stop_time', 0);

        if (empty($startTime) || empty($stopTime)) {
            $startTime = date('Y-m-d', strtotime("-30 day"));
            $stopTime = date('Y-m-d', strtotime('+1 day'));
        }
        $pageMaps['start_time'] = $startTime;
        $pageMaps['stop_time'] = $stopTime;

        $where = [];
        $where['A.order_app'] = 'mall';
        $where['A.order_status'] = 1;
        $where['_sql'] = 'order_create_time >=' . strtotime($startTime) . ' AND order_create_time <=' . strtotime($stopTime);
        $data = target('order/Order')->loadList($where, 0, 'order_id asc');


        $orderPeopleArray = [];
        $orderNum = 0;
        $orderPrice = 0;

        $payPeopleArray = [];
        $payNum = 0;
        $sales = 0;
        $payPrice = 0;
        $avgPrice = 0;


        foreach ($data as $vo) {
            if (!isset($orderPeopleArray[$vo['order_user_id']])) {
                $orderPeopleArray[$vo['order_user_id']] = 1;
            }
            $orderNum++;
            $orderPrice = price_calculate($orderPrice, '+', $vo['order_price']);

            if ($vo['pay_status']) {
                if (!isset($payPeopleArray[$vo['order_user_id']])) {
                    $payPeopleArray[$vo['order_user_id']] = 1;
                }
                $payNum++;
                $sales += $vo['order_sum'];
                $payPrice = price_calculate($payPrice, '+', $vo['order_price']);
            }
        }

        $orderPeople = count($orderPeopleArray);
        $payPeople = count($payPeopleArray);

        if ($payPeople) {
            $avgPrice = price_calculate($payPrice, '/', $payPeople);
        }


        $orderData = [];
        $payData = [];
        $statsLabel = [];
        foreach ($data as $vo) {
            if (!$vo['order_status']) {
                continue;
            }
            $date = date('Y-m-d', $vo['order_create_time']);
            $statsLabel[] = $date;
            $orderData[$date]++;

            if ($vo['pay_status']) {
                $payData[$date]++;
            }
        }

        $statsLabel = array_unique($statsLabel);
        $statsLabel = array_values($statsLabel);

        $statsOrder = [];
        $statsPay = [];
        foreach ($statsLabel as $vo) {
            if ($orderData[$vo]) {
                $statsOrder[] = $orderData[$vo];
            } else {
                $statsOrder[] = 0;
            }
            if ($payData[$vo]) {
                $statsPay[] = $payData[$vo];
            } else {
                $statsPay[] = 0;
            }
        }

        $dateParams = array(
            array(
                'start_time' => date('Y-m-01', strtotime('-1 month')),
                'stop_time' => date('Y-m-t', strtotime('-1 month')),
            ),
            array(
                'start_time' => date('Y-m-01', strtotime(date("Y-m-d"))),
                'stop_time' => date('Y-m-d', strtotime((date('Ym01', strtotime(date("Y-m-d")))) . " +1 month -1 day")),
            ),
            array(
                'start_time' => date('Y-m-d', strtotime("-15 day")),
                'stop_time' => date('Y-m-d', strtotime('+1 day')),
            ),
            array(
                'start_time' => date('Y-m-d', strtotime("-30 day")),
                'stop_time' => date('Y-m-d', strtotime('+1 day')),
            ),
        );


        $stats = [
            'labels' => $statsLabel,
            'datasets' => [
                [
                    'label' => '下单量',
                    'data' => $statsOrder,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#e25141',
                ],
                [
                    'label' => '成交量',
                    'data' => $statsPay,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#2096f3',
                ],
            ]

        ];

        $this->assign([
            'pageMaps' => $pageMaps,
            'orderPeople' => $orderPeople,
            'orderNum' => $orderNum,
            'orderPrice' => $orderPrice,
            'payPeople' => $payPeople,
            'payNum' => $payNum,
            'sales' => $sales,
            'payPrice' => $payPrice,
            'avgPrice' => $avgPrice,
            'statsData' => $stats,
            'dateParams' => $dateParams
        ]);

        $this->systemDisplay();
    }


    public function _indexParam() {
        return [
            'keyword' => 'C.name,B.tel,B.email',
            'log_no' => 'A.log_no',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
        ];
    }

    public function _indexWhere($whereMaps) {
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }
        if ($startTime) {
            $whereMaps['_sql'][] = 'A.time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.time <= ' . $stopTime;
        }
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);
        return $whereMaps;

    }

    public function _indexAssign($data, $where) {
        $list = target($this->_model)->loadList($where);
        $money = 0;
        $num = 0;
        foreach ($list as $vo) {
            $money += price_calculate($vo['charge'], '-', $vo['spend']);
            $num += $vo['charge_num'] - $vo['spend_num'];
        }
        return [
            'data' => [
                'money' => $money,
                'num' => $num
            ]
        ];
    }

}