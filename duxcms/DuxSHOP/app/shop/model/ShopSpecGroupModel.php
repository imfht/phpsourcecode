<?php

/**
 * 规格分组
 */
namespace app\shop\model;

use app\system\model\SystemModel;

class ShopSpecGroupModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'group_id'
    ];

    protected function _saveBefore($data) {
        $data['spec_ids'] = implode(',', $data['spec_ids']);
        return $data;
    }

    public function loadList() {
        $list = parent::loadList();
        if(empty($list)) {
            return [];
        }

        $specList = target('shop/ShopSpec')->loadList();
        $specData = [];
        foreach($specList as $vo) {
            $specData[$vo['spec_id']] = $vo['name'];
        }
        foreach($list as $key => $vo) {
            $specIds = explode(',', $vo['spec_ids']);
            $specName = [];
            foreach($specIds as $specId) {
                $specName[] = $specData[$specId];
            }
            $list[$key]['spec_name'] = implode(',', $specName);
        }
        return $list;
    }

}