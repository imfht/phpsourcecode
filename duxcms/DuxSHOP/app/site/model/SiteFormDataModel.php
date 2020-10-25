<?php

/**
 * 表单数据管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteFormDataModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'data_id'
    ];

    public function saveData($type = 'add', $data = []) {
        if(empty($data)) {
            $data = request('post');
        }
        $formId = $data['form_id'];
        if(empty($formId)){
            return false;
        }
        $formFields = target('site/SiteFormField')->loadList(['form_id' => $formId]);
        $formInfo = target('site/SiteForm')->getInfo($formId);
        foreach($formFields as $field) {
            $data[$field['label']] = call_user_func_array([target('site/SiteFormFieldFormat'), $field['type']], [$data[$field['label']], $field['config']]);
            if($field['must']) {
                $validate = call_user_func_array([target('site/SiteFormFieldValidate'), $field['type']], [$data[$field['label']], $field['config']]);
                if (!$validate) {
                    $this->error = $field['name'] . '输入不正确!';
                    return false;
                }
            }
        }
        if (!$data) {
            return false;
        }
        if ($type == 'add') {
            $id = $this->table('form_' . $formInfo['label'])->add($data);
            $data[$this->primary] = $id;
            if (!$id) {
                return false;
            }
            return $id;
        }
        if ($type == 'edit') {
            if (empty($data[$this->primary])) {
                return false;
            }
            if (!$this->table('form_' . $formInfo['label'])->edit($data)) {
                return false;
            }
            return true;
        }
        return false;
    }
}