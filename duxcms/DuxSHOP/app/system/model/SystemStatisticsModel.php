<?php

/**
 * 访问统计
 */

namespace app\system\model;

use app\system\model\SystemModel;

class SystemStatisticsModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'stat_id'
    ];

    public function dataStats($startDate, $stopDate) {

        if (empty($startDate) || empty($stopDate)) {
            $startDate = date('Ymd', strtotime("-7 day"));
            $stopDate = date('Ymd');
        }else {
            $startDate = date('Ymd', strtotime($startDate));
            $stopDate = date('Ymd', strtotime($stopDate));
        }

        $list = $this->loadList([
            '_sql' => 'date >= ' . $startDate . ' AND date <= ' . $stopDate
        ], 0, 'date asc');

        $webData = [];
        $mobileData = [];
        $apiData = [];
        $dates = [];
        foreach ($list as $vo) {
            $webData[] = $vo['web'];
            $mobileData[] = $vo['api'];
            $apiData[] = $vo['mobile'];
            $dates[] = date('Y-m-d', strtotime($vo['date']));
        }

        return [
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => '电脑访问',
                    'data' => $webData,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#e25141',
                ],
                [
                    'label' => '手机访问',
                    'data' => $mobileData,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#2096f3',
                ],
                [
                    'label' => 'API访问',
                    'data' => $apiData,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'borderColor' => '#ff9800',
                ],
            ]

        ];


    }


    public function listStats($type, $limit = 7) {
        $list = $this->loadList([
            '_sql' => $type . ' != ""'
        ], $limit);
        $data = array();
        foreach ($list as $vo) {
            $data[$vo['date']] = $vo[$type];
        }
        return $data;
    }

    public function countStats($day = 0) {
        $where = '';
        if ($day) {
            $where = 'WHERE date >= ' . date("Ymd", strtotime("-{$day} day"));
        }
        $info = $this->query("SELECT SUM(web) as web, SUM(api) as api, SUM(mobile) as mobile FROM {pre}system_statistics {$where}");
        return $info[0];
    }

    /**
     * 保存统计数据
     * @param $type
     * @param $date
     * @param int $num
     * @return bool
     */
    public function saveStats($type, $date, $num = 1) {
        $where = array();
        $where['date'] = $date;
        $info = target('system/SystemStatistics')->getwhereInfo($where);
        if (empty($info)) {
            target('system/SystemStatistics')->add([
                'date' => $date,
                $type => $num,
            ]);
        } else {
            target('system/SystemStatistics')->where([
                'stat_id' => $info['stat_id']
            ])->setInc($type, $num);
        }
        return true;
    }

}
