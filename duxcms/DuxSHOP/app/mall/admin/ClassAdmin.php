<?php

/**
 * 分类管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class ClassAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MallClass';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商品分类',
                'description' => '商品分类管理',
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
            'model_id' => 'model_id',
            'keyword' => 'name'
        ];
    }

    protected function _indexWhere($whereMaps) {
        $whereMaps['A.model_id'] = intval($whereMaps['model_id']);
        return $whereMaps;
    }

    public function _indexPage() {
        return 10000;
    }

    public function _indexData($where, $limit=[], $order) {
        return target($this->_model)->loadTreeList($where, [], $order);
    }

    protected function _indexAssign($pageMaps) {
        return array(
            'modelList' => target('site/SiteModel')->loadList(),
            'modelId' => $pageMaps['model_id']
        );
    }

    protected function _indexUrl($id) {
        return url('index', array('model_id' => request('post', 'model_id')));
    }

    protected function _addAssign() {
        $modelId = request('get', 'model_id', 0, 'intval');
        return array(
            'classList' => target('Mall/MallClass')->loadTreeList(['A.model_id' => $modelId]),
            'filterList' => target('site/SiteFilter')->loadList(),
            'specGroupList' => target('shop/ShopSpecGroup')->loadList(),
            'modelId' => $modelId
        );
    }

    protected function _editAssign($info) {
        return array(
            'classList' => target('Mall/MallClass')->loadTreeList(['A.model_id' => $info['model_id']]),
            'filterList' => target('site/SiteFilter')->loadList(),
            'specGroupList' => target('shop/ShopSpecGroup')->loadList(),
            'modelId' => $info['model_id']
        );
    }

    protected function _delBefore($id) {
        $cat = target($this->_model)->loadTreeList([], 0, '', $id);
        if ($cat) {
            $this->error('清先删除子分类!');
        }
        $count = target('Mall/Mall')->countList([
            'B.class_id' => $id
        ]);
        if ($count > 0) {
            $this->error('请先删除该分类下的内容！');
        }
    }

}