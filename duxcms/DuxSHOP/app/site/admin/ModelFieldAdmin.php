<?php

/**
 * 字段管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class ModelFieldAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteModelField';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '字段配置',
                'description' => '管理模型中的字段信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    protected function _indexParam() {
        return [
            'model_id' => 'model_id',
            'keyword' => 'name'
        ];
    }

    protected function _indexWhere($whereMaps) {
        if(!$whereMaps['model_id']) {
            $modelList = target('site/SiteModel')->loadList();
            if(empty($modelList)) {
                $this->error('请先添加模型!');
            }
            $whereMaps['model_id'] = $modelList[0]['model_id'];
        }
        return $whereMaps;
    }

    protected function _indexPage() {
        return 100;
    }

    protected function _indexOrder() {
        return 'sort asc, model_id asc';
    }

    protected function _indexAssign($pageMaps) {
        $modelList = target('site/SiteModel')->loadList();
        $id = $pageMaps['model_id'];
        if(empty($id)) {
            $id = $modelList[0]['model_id'];
        }
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'modelList' => $modelList,
            'modelId' => $id
        );
    }

    protected function _indexUrl($id) {
        return url('index', array('model_id' => request('post', 'model_id')));
    }


    protected function _addAssign() {
        $modelId = request('get', 'model_id', 1);
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'modelInfo' => target('site/SiteModel')->getInfo($modelId),
            'modelId' => $modelId
        );
    }

    protected function _editAssign($info) {
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'modelInfo' => target('site/SiteModel')->getInfo($info['model_id']),
            'modelId' => $info['model_id']
        );
    }

    protected function _delBefore($id) {

    }

    public function help() {
        $type = request('post', 'type');
        if(empty($type)){
            $this->error('请选择字段类型!');
        }
        $this->success(target('site/SiteFormFieldHelp')->$type());
    }

    public function map() {
        $this->display();
    }

}