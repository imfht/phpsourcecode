<?php

/**
 * 表单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class FormManageAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteForm';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '表单管理',
                'description' => '管理系统中表单信息',
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