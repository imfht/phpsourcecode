<?php

/**
 * 内容过滤
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class FilterAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteFilter';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '内容筛选',
                'description' => '管理内容筛选模型',
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

    public function _editAssign($info) {
        return [
            'attrList' => target('site/SiteFilterAttr')->loadList([
                'filter_id' => $info['filter_id']
            ])
        ];
    }





}