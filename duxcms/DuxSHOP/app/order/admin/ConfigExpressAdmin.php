<?php

/**
 * 快递配置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class ConfigExpressAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderConfigExpress';

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
        return 'sort asc, express_id asc';
    }


}