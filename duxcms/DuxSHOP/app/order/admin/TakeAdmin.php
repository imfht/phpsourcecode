<?php

/**
 * 自提点管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class TakeAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderTake';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '自提点管理',
                'description' => '管理订单自提点',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'status' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexOrder() {
        return 'take_id desc';
    }


}