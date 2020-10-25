<?php
namespace app\order\service;
/**
 * 快递查询接口
 */
class WaybillService {

    /**
     * 获取推送结构
     */
    public function getTypeWayBill() {
        return array(
            'kdniao' => array(
                'name' => '快递鸟',
                'target' => 'order/Kdniao',
                'desc' => '使用后请修改物流标识为快递鸟标识',
                'configRule' => array(
                    'id' => '商户ID',
                    'key' => 'API密钥',
                )
            ),
        );
    }
}

