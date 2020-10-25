<?php

namespace app\order\service;
/**
 * 类型接口
 */
class TypeService {

    public function getWaybillType() {
        return array(
            'kdniao' => array(
                'name' => '快递鸟',
                'target' => 'order/Kdniao',
                'desc' => '请自行申请快递鸟接口',
                'url' => 'http://www.kdniao.com/',
                'configRule' => array(
                    'id' => '商户ID',
                    'key' => 'API密钥',
                )
            ),
        );
    }

}

