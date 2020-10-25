<?php

/**
 * 运单设置
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderConfigWaybillModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'validate' => [
            'type' => [
                'required' => ['', '类型参数获取不正确!', 'must', 'all'],
            ],
        ],
    ];

    /**
     * 获取配置
     * @param $type
     * @return mixed
     */
    public function getConfig($type) {
        $where = array();
        $where['type'] = $type;
        $info = $this->getWhereInfo($where);
        return unserialize($info['setting']);
    }

    /**
     * 获取服务接口
     */
    public function typeList() {
        $list = hook('service', 'Type', 'Waybill');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

    /**
     * 属性信息
     * @param $type
     * @return mixed
     */
    public function typeInfo($type) {
        $list = $this->typeList();
        return $list[$type];

    }


}