<?php

/**
 * 品牌管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\shop\admin;

class BrandAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'ShopBrand';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '品牌管理',
                'description' => '管理商品品牌信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }


}