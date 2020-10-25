<?php

/**
 * 等级设置
 */
namespace app\sale\model;

use app\system\model\SystemModel;

class SaleUserLevelModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'level_id',
        'into' => '',
        'out' => '',
    ];

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = parent::loadList($where, $limit, $order);
        foreach ($list as $key => $vo) {
            $list[$key]['comm_rate'] = unserialize($vo['comm_rate']);
        }
        return $list;
    }


}