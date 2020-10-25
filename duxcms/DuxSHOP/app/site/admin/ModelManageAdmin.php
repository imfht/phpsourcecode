<?php

/**
 * 模型管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class ModelManageAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteModel';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '扩展模型',
                'description' => '管理系统中内容的扩展模型',
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