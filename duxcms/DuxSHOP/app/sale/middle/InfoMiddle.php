<?php

/**
 * 账户信息
 */

namespace app\sale\middle;

class InfoMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'sale/SaleOrder';

    protected function meta() {
        $this->setMeta('推广信息');
        $this->setName('推广信息');
        $this->setCrumb([
            [
                'name' => '推广信息',
                'url' => url()
            ],
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function saleInfo() {
        $userId = intval($this->params['user_id']);

        return target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId,
            'agent' => 1
        ]);
    }

    protected function data() {
        $saleInfo = $this->saleInfo();
        if (empty($saleInfo)) {
            return $this->stop('请先成为推广商!', 302, url('sale/Apply/index'));
        }

        $parentInfo = target('sale/SaleUser')->getWhereInfo([
            'A.parent_id' => $saleInfo['parent_id'],
        ]);


        $model = target($this->_model);
        //本年消费
        $yearList = $model->loadList([
            'A.sale_status' => 2,
            'A.user_id' => $saleInfo['user_id'],
            '_sql' => 'A.create_time > ' . strtotime(date("Y", time()) . "-1" . "-1") . ' AND A.create_time <' . strtotime(date("Y", time()) . "-12" . "-31"),

        ]);

        $statsLabel = [];
        for ($i = 1; $i < 13; $i++) {
            $statsLabel[] = $i . '月';
        }

        $yearCharge = [];
        foreach ($yearList as $vo) {
            $yearCharge[intval(date('m', $vo['time']))] += $vo['money'];
        }
        $yearChargeData = [];

        for ($i = 1; $i < 13; $i++) {
            $yearChargeData[] = $yearCharge[$i] ? $yearCharge[$i] : '0';
        }



        $stats = [
            'labels' => $statsLabel,
            'datasets' => [
                [
                    'label' => '佣金',
                    'data' => $yearChargeData,
                    'backgroundColor' => 'rgba(237, 110, 133, 0.3)',
                    'borderColor' => '#ed6e85',
                ]
            ]

        ];

        $saleMoneyCount = target('sale/SaleOrder')->query("select sum(sale_money) as cashout from {pre}sale_order where sale_status = 2 AND user_id = " . $saleInfo['user_id']);
        $saleMoneyCount = $saleMoneyCount[0]['cashout'] ? $saleMoneyCount[0]['cashout'] : 0.00;

        $saleMoneyMonth = target('sale/SaleOrder')->query("select sum(sale_money) as cashout from {pre}sale_order where sale_status = 2 AND user_id = " . $saleInfo['user_id'] . " AND create_time > " . mktime(0, 0, 0, date("m", time()), 1, date("Y", time())) . ' AND create_time < ' . mktime(23, 59, 59, date("m", time()), date('t'), date("Y", time())));
        $saleMoneyMonth = $saleMoneyMonth[0]['cashout'] ? $saleMoneyMonth[0]['cashout'] : 0.00;

        $userCount = target('sale/SaleUser')->countList([
            'parent_id' => $saleInfo['user_id']
        ]);

        $agentCount = target('sale/SaleUser')->countList([
            'parent_id' => $saleInfo['user_id'],
            'agent' => 1
        ]);



        return $this->run([
            'stats' => $stats,
            'saleInfo' => $saleInfo,
            'userCount' => $userCount,
            'agentCount' => $agentCount,
            'saleMoneyCount' => $saleMoneyCount,
            'saleMoneyMonth' => $saleMoneyMonth,
            'parentInfo' => $parentInfo
        ]);
    }

}