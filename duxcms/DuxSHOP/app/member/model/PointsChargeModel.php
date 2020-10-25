<?php

/**
 * 积分记录
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PointsChargeModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
    ];

    public function loadList($where = array(), $limit = 0, $order = 'id desc') {
        $list = parent::loadList($where, $limit, $order);
        foreach ($list as $key => $vo) {
            $list[$key]['show_stop_time'] = date('Y-m-d', $vo['stop_time']);

            if(!$vo['status']) {
                $list[$key]['status_text'] = '已使用';
            }else {
                if ($vo['stop_time'] < time()) {
                    $list[$key]['status_text'] = '未过期';
                }elseif($vo['stop_time'] <= time() + 259200) {
                    $list[$key]['status_text'] = '即将过期';
                }else {
                    $list[$key]['status_text'] = '已过期';
                }
            }

        }
        return $list;
    }


}