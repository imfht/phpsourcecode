<?php

namespace app\mall\service;
/**
 * 类型接口
 */
class TypeService {

    /**
     * 优惠券接口
     */
    public function getCouponType() {
        return [
            'mall' => [
                'name' => '商品',
                'target' => 'mall/MallOrder',
                'type' => 1
            ],
            'class' => [
                'name' => '类目',
                'target' => 'mall/MallClass',
                'type' => 2,
                'system' => 1
            ],
        ];
    }
}

