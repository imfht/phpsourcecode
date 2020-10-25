<?php

/**
 * 表单内容
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class FormAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteFormData';
    protected $action = 'index';
    protected $table = '';
    protected $formName = '';
    protected $formInfo = [];
    protected $formFields = [];

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '表单数据管理',
                'description' => '管理表单数据内容',
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

    public function _indexCount($where) {
        return target($this->_model)->table($this->table)->countList($where);
    }

    public function _indexData($where, $limit, $order) {
        $list = target($this->_model)->table($this->table)->loadList($where, $limit, $order);
        foreach ($this->formFields as $field) {
            if (!$field['show']) {
                continue;
            }
            foreach ($list as $key => $vo) {
                $list[$key][$field['label']] = call_user_func_array([target('site/SiteFormHtmlList'), $field['type']], [$vo[$field['label']], $field['config']]);
            }
        }
        return $list;
    }

    public function _indexAssign() {
        return [
            'formName' => $this->formName,
            'formInfo' => $this->formInfo,
            'formFields' => $this->formFields
        ];
    }


    public function _addAssign() {
        $html = '';
        foreach ($this->formFields as $field) {
            $cHtml = call_user_func_array([target('site/SiteFormHtml'), $field['type']], [$field['label'], $field['must'], $field['tip'], $field['default'], $field['config']]);
            $html .= target('site/SiteFormHtml')->layer($field['name'], $cHtml);
        }
        return [
            'formName' => $this->formName,
            'formInfo' => $this->formInfo,
            'formFields' => $this->formFields,
            'html' => $html
        ];
    }

    public function _editAssign($info) {
        $html = '';
        foreach ($this->formFields as $field) {
            $cHtml = call_user_func_array([target('site/SiteFormHtml'), $field['type']], [$field['label'], $field['must'], $field['tip'], $info[$field['label']], $field['config']]);
            $html .= target('site/SiteFormHtml')->layer($field['name'], $cHtml);
        }
        return [
            'formName' => $this->formName,
            'formInfo' => $this->formInfo,
            'formFields' => $this->formFields,
            'html' => $html
        ];
    }

    public function _editInfo($id) {
        $where = [];
        $where['data_id'] = $id;
        return target($this->_model)->table($this->table)->getWhereInfo($where);
    }

    public function _indexUrl() {
        return url($this->formName);
    }

    public function _indexTpl() {
        return 'index';
    }

    public function del() {
        $id = request('post', 'id', 0, 'intval');
        if (empty($id)) {
            $this->error('ID不能为空！');
        }
        if (!target($this->_model)->table($this->table)->delData($id)) {
            $msg = target($this->_model)->getError();
            if (empty($msg)) {
                $this->error('删除失败！');
            } else {
                $this->error($msg);
            }
        }
        $this->success('删除成功！');
    }

    public function __call($name, $arguments) {
        $this->formName = $name;
        $this->table = 'form_' . $name;
        $action = request('get', 'action', 'index');
        $this->action = $action;
        $this->formInfo = target('site/SiteForm')->getWhereInfo(['label' => $name]);
        if (empty($this->formInfo)) {
            $this->error404();
        }
        $this->infoModule['info']['name'] = $this->formInfo['name'];
        $this->infoModule['info']['description'] = '管理站点中' . $this->formInfo['name'] . '的内容';
        $this->formFields = target('site/SiteFormField')->loadList(['form_id' => $this->formInfo['form_id']]);
        call_user_func([$this, $this->action]);
    }

}