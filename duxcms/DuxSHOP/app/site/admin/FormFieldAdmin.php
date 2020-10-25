<?php

/**
 * 字段管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class FormFieldAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteFormField';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '字段配置',
                'description' => '管理表单中的字段信息',
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
            'form_id' => 'form_id',
            'keyword' => 'name'
        ];
    }

    protected function _indexWhere($whereMaps) {
        if(!$whereMaps['form_id']) {
            $formList = target('site/SiteForm')->loadList();
            if(empty($formList)) {
                $this->error('请先添加表单!');
            }
            $whereMaps['form_id'] = $formList[0]['form_id'];
        }
        return $whereMaps;
    }

    protected function _indexPage() {
        return 100;
    }

    protected function _indexOrder() {
        return 'sort asc, form_id asc';
    }

    protected function _indexAssign($pageMaps) {
        $formList = target('site/SiteForm')->loadList();
        $id = $pageMaps['form_id'];
        if(empty($id)) {
            $id = $formList[0]['form_id'];
        }
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'formList' => $formList,
            'formId' => $id
        );
    }

    protected function _indexUrl($id) {
        return url('index', array('form_id' => request('post', 'form_id')));
    }


    protected function _addAssign() {
        $formId = request('get', 'form_id', 0);
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'formInfo' => target('site/SiteForm')->getInfo($formId),
            'formId' => $formId
        );
    }

    protected function _editAssign($info) {
        return array(
            'type' => target('site/SiteFormFieldType')->type(),
            'formInfo' => target('site/SiteForm')->getInfo($info['form_id']),
            'formId' => $info['form_id']
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