<?php

/**
 * 广告区域管理
 * @author  Mr.Gkx <189709040@qq.com>
 */

namespace app\site\admin;

class AdvRegionAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteAdvRegion';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '广告区域管理',
                'description' => '管理站点广告区域信息',
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
        return 'region_id asc';
    }

}