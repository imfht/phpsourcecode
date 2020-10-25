<?php

/**
 * 模型字段
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteModelFieldModel extends SystemModel {

    protected $formLabel = '';
    protected $fieldLabel = '';

    protected $infoModel = [
        'pri' => 'field_id',
        'validate' => [
            'name' => [
                'required' => ['', '字段名称不能为空!', 'must', 'all'],
            ],
            'model_id' => [
                'empty' => ['', '模型ID有误!', 'must', 'all'],
            ],
        ],
        'format' => [
            'sort' => [
                'function' => ['intval', 'all'],
            ]
        ]
    ];

    public function loadList($where = [], $limit = 0, $order = '') {
        return parent::loadList($where, $limit, 'sort asc, field_id asc');
    }

    public function _saveBefore($data) {
        $this->beginTransaction();
        $info = target('site/SiteModel')->getInfo($data['model_id']);
        $this->formLabel = $info['label'];
        $noLabel = ['select', 'table', 'add', 'default'];
        if(in_array($data['label'], $noLabel)) {
            $this->error = '保留字段名,请更换模型标识!';
            return false;
        }
        if($data['field_id']) {
            $info = $this->getInfo($data['field_id']);
            $this->fieldLabel = $info['label'];
        }
        return $data;
    }

    public function _saveAfter($type, $data) {
        $sql = '';
        $field =  call_user_func([target('site/SiteFormFieldType'), $data['type']]);
        $field['decimal'] = $field['decimal'] ? ','.$field['decimal'] : '';
        if(empty($field['default'])) {
            if($field['default'] === 0) {
                $field['default'] = 0;
            }else {
                $field['default'] = "''";
            }
        }
        if ($type == 'add') {
            $sql = "ALTER TABLE {pre}model_{$this->formLabel} ADD {$data['label']} {$field['type']}({$field['len']}{$field['decimal']}) DEFAULT {$field['default']}";
        }
        if ($type == 'edit') {
            if($data['label'] == $this->fieldLabel) {
                $this->commit();
                return true;
            }
            $sql = "ALTER TABLE {pre}model_{$this->formLabel} CHANGE {$this->fieldLabel} {$data['label']} {$field['type']}({$field['len']}{$field['decimal']}) DEFAULT {$field['default']}";
        }
        $status = $this->execute($sql);
        if ($status === false) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }

    public function _delBefore($id) {
        $this->beginTransaction();
        $info = $this->getInfo($id);
        $this->fieldLabel = $info['label'];
        $info = target('site/SiteModel')->getInfo($info['model_id']);
        $this->formLabel = $info['label'];
        return true;
    }

    public function _delAfter($id) {
        $sql = "ALTER TABLE {pre}model_{$this->formLabel} DROP {$this->fieldLabel}";
        $status = $this->execute($sql);
        if ($status === false) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }



}