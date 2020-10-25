<?php

/**
 * 表单管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteFormModel extends SystemModel {

    protected $formLabel = '';

    protected $infoModel = [
        'pri' => 'form_id',
        'validate' => [
            'name' => [
                'len' => ['1, 50', '表单名称输入不正确!', 'must', 'all'],
            ],
            'label' => [
                'required' => ['', '表单标识不能为空!', 'must', 'all'],
                'unique' => ['', '已存在相同的表单!', 'must', 'all']
            ]
        ],
        'format' => [
            'name' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

    public function _saveBefore($data) {
        $this->beginTransaction();
        $noLabel = ['index', 'edit', 'add', 'del'];
        if(in_array($data['label'], $noLabel)) {
            $this->error = '保留表名,请更换表单标识!';
            return false;
        }
        if ($data['form_id']) {
            $info = $this->getInfo($data['form_id']);
            $this->formLabel = $info['label'];
        }
        return $data;
    }

    public function _saveAfter($type, $data) {
        $sql = '';
        if ($type == 'add') {
            $sql = "CREATE TABLE IF NOT EXISTS `{pre}form_{$data['label']}`
                    ( `data_id` int(10) NOT NULL AUTO_INCREMENT , PRIMARY KEY (`data_id`) )
                    ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8; ";
        }
        if ($type == 'edit') {
            $sql = "ALTER TABLE {pre}form_" . $this->formLabel . " RENAME TO {pre}form_" . $data['label'];
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
        $this->formLabel = $info['label'];
        return true;
    }

    public function _delAfter($id) {
        $status = target('site/SiteFormField')->where([
            'form_id' => $id
        ])->delete();
        if($status === false) {
            $this->rollBack();
            return false;
        }
        $sql = "DROP TABLE `{pre}form_" . $this->formLabel . "`";
        $status = $this->execute($sql);
        if ($status === false) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }

}