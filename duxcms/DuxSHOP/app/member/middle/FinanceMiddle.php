<?php

/**
 * 账户提现
 */

namespace app\member\middle;


class FinanceMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/PayLog';

    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => $name,
                'url' => $url
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }


    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        if ($type == 1) {
            $where['A.type'] = 1;
        }
        if ($type == 2) {
            $where['A.type'] = 0;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'log_id desc');
        $list = $model->showList($list);

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function info() {
        $no = intval($this->params['no']);
        $userId = intval($this->params['user_id']);
        $info = target('member/PayLog')->getWhereInfo([
            'B.user_id' => $userId,
            'A.log_no' => $no
        ]);
        $info = target('member/PayLog')->showInfo($info);
        if (empty($info)) {
            return $this->stop('该记录不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }


    protected function statistical() {
        $userId = intval($this->params['user_id']);
        $model = target($this->_model);
        //当日消费
        $dayList = $model->loadList([
            '_sql' => 'A.user_id = ' . $userId . ' AND A.time > ' . mktime(0, 0, 0, date("m"), date("d"), date("Y")) . ' AND A.time <' . mktime(23, 59, 59, date("m"), date("d"), date("Y")),
        ]);

        $dayCount = [];
        $daySpend = 0;
        $dayCharge = 0;
        foreach ($dayList as $vo) {
            if ($vo['type']) {
                $dayCharge += $vo['money'];
            } else {
                $daySpend += $vo['money'];
            }
        }
        $dayCountNum = $daySpend + $dayCharge;
        $daySpendRate = $dayCountNum ? round(($daySpend / $dayCountNum) * 100) : 50;
        $dayChargeRate = $dayCountNum ? round(($dayCharge / $dayCountNum) * 100) : 50;
        $dayCount['series'][] = $daySpendRate;
        $dayCount['series'][] = $dayChargeRate;

        //本月消费
        $monthList = $model->loadList([
            '_sql' => 'A.user_id = ' . $userId . ' AND A.time > ' . mktime(0, 0, 0, date("m", time()), 1, date("Y", time())) . ' AND A.time <' . mktime(23, 59, 59, date("m", time()), date('t'), date("Y", time())),
        ]);
        $monthCount = [];
        $monthSpend = 0;
        $monthCharge = 0;
        foreach ($monthList as $vo) {
            if ($vo['type']) {
                $monthCharge += $vo['money'];
            } else {
                $monthSpend += $vo['money'];
            }
        }

        $monthCountNum = $monthSpend + $monthCharge;

        $monthSpendRate = $monthCountNum ? round(($monthSpend / $monthCountNum) * 100) : 50;
        $monthChargeRate = $monthCountNum ? round(($monthCharge / $monthCountNum) * 100) : 50;

        $monthCount['series'][] = $monthSpendRate;
        $monthCount['series'][] = $monthChargeRate;


        //本年消费
        $yearList = $model->loadList([
            '_sql' => 'A.user_id = ' . $userId . ' AND A.time > ' . strtotime(date("Y", time()) . "-1" . "-1") . ' AND A.time <' . strtotime(date("Y", time()) . "-12" . "-31"),

        ]);

        $statsLabel = [];
        for ($i = 1; $i < 13; $i++) {
            $statsLabel[] = $i . '月';
        }

        $yearSpend = [];
        $yearCharge = [];
        foreach ($yearList as $vo) {
            if ($vo['type']) {
                $yearCharge[intval(date('m', $vo['time']))] += $vo['money'];
            } else {
                $yearSpend[intval(date('m', $vo['time']))] += $vo['money'];
            }
        }
        $yearSpendData = [];
        $yearChargeData = [];

        for ($i = 1; $i < 13; $i++) {
            $yearSpendData[] = $yearSpend[$i] ? $yearSpend[$i] : '0';
            $yearChargeData[] = $yearCharge[$i] ? $yearCharge[$i] : '0';
        }

        $stats = [
            'labels' => $statsLabel,
            'datasets' => [
                [
                    'label' => '收入',
                    'data' => $yearChargeData,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#e25141',
                ],
                [
                    'label' => '支出',
                    'data' => $yearSpendData,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#2096f3',
                ],
            ]

        ];


        return $this->run([
            'stats' => $stats,
            'dayInfo' => [
                'spend' => $daySpend,
                'charge' => $dayCharge
            ],
            'monthInfo' => [
                'spend' => $monthSpend,
                'charge' => $monthCharge
            ]
        ]);
    }


}