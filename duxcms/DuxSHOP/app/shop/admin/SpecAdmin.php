<?php

/**
 * 规格管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\shop\admin;

class SpecAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'ShopSpec';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '规格管理',
                'description' => '管理商品规格信息',
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

    public function getList() {
        $classId = request('get', 'class_id', 0, 'intval');
        $app = request('get', 'app', '', 'html_clear');
        target('shop/Spec', 'middle')->setParams([
            'class_id' => $classId,
            'app' => $app
        ])->data()->export(function ($data) {
            $this->success($data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }


}