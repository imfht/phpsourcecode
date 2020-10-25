<?php

/**
 * 订单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\sale\admin;

class OrderAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SaleOrder';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '返利记录',
                'description' => '管理返利记录',
            ],
            'fun' => [
                'index' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'username' => 'username',
            'sale_status' => 'sale_status',
        ];
    }

    public function _indexWhere($where) {
        if ($where['username']) {
            $where['_sql'] = "C.nickname = '{$where['username']}' OR C.tel= '{$where['username']}' OR C.email= '{$where['username']}' ";
            unset($where['username']);
        }
        switch ($where['sale_status']) {
            case 1:
                $where['sale_status'] = 0;
                break;
            case 2:
                $where['sale_status'] = 1;
                break;
            case 3:
                $where['sale_status'] = 2;
                break;
        }
        return $where;

    }

    public function _indexOrder() {
        return 'create_time desc, id desc';
    }

}