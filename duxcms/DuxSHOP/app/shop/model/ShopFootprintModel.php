<?php

/**
 * 商品足迹
 */
namespace app\shop\model;

use app\system\model\SystemModel;

class ShopFootprintModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'footprint_id'
    ];

    public function loadFootprint($userId, $limit) {
        $footprintInfo = $this->getWhereInfo([
            'user_id' => $userId
        ]);

        $footprintIds = $footprintInfo['ids'];
        if (empty($footprintIds)) {
            return [];
        }
        $shopList = target('shop/Shop')->loadList([
            '_sql' => 'B.shop_id in (' . $footprintIds . ')',
            'A.status' => 0,
        ], $limit, '');
        if(empty($shopList)) {
            return [];
        }
        return $shopList;
    }

}