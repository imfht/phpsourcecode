<?php

/**
 * 快递配置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class ConfigDeliveryAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderConfigDelivery';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '物流设置',
                'description' => '管理物流配送快递',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexOrder() {
        return 'delivery_id asc';
    }

    protected function _editAssign($info) {
        return array(
            'areaData' => unserialize($info['area'])
        );
    }

    public function area() {
        $this->assign('id', request('get', 'id'));
        $this->dialogDisplay();
    }

    public function deliveryData() {
        $list = target('order/OrderConfigDelivery')->loadList();
        $data = [];
        foreach ($list as $vo) {
            $data[] = [
                'name' => $vo['name'],
                'value' => $vo['delivery_id']
            ];
        }
        $this->success($data);
    }


}