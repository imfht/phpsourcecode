<?php

/**
 * 物流配置
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderConfigDeliveryModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'delivery_id',
    ];

    public function _saveBefore($data) {

        $areaRaw = $_POST['area'];

        if(empty($areaRaw)) {
            $data['area'] = '';
            return $data;
        }

        $areaData = [];
        foreach($areaRaw['area'] as $key => $vo) {
            if(empty($vo)) {
                continue;
            }
            $areaData[] = [
                'first_price' => $areaRaw['first_price'][$key],
                'second_price' => $areaRaw['second_price'][$key],
                'area' => $areaRaw['area'][$key],
            ];
        }

        $data['area'] = serialize($areaData);
        return $data;
    }

}