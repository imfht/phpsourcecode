<?php

/**
 * 规格分组
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\shop\admin;

class SpecGroupAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'ShopSpecGroup';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '规格分组',
                'description' => '将规格进行分组处理',
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

    public function dialog() {
        $this->assign('list', target('shop/ShopSpec')->loadList());
        $this->dialogDisplay();
    }

    public function _editAssign($info) {
        $specList = target('shop/ShopSpec')->loadList([
            '_sql' => 'spec_id in (' . $info['spec_ids'] . ')'
        ]);
        return [
            'specList' => $specList ? $specList : []
        ];
    }





}