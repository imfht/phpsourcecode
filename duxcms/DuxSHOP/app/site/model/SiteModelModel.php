<?php

/**
 * 模型管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteModelModel extends SystemModel {

    protected $formLabel = '';

    protected $infoModel = [
        'pri' => 'model_id',
        'validate' => [
            'name' => [
                'len' => ['1, 50', '模型名称输入不正确!', 'must', 'all'],
            ],
            'label' => [
                'required' => ['', '模型标识不能为空!', 'must', 'all'],
                'unique' => ['', '已存在相同的模型!', 'must', 'all']
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
            $this->error = '保留表名,请更换模型标识!';
            return false;
        }
        if ($data['model_id']) {
            $info = $this->getInfo($data['model_id']);
            $this->formLabel = $info['label'];
        }
        return $data;
    }

    public function _saveAfter($type, $data) {
        $sql = '';
        if ($type == 'add') {
            $sql = "CREATE TABLE IF NOT EXISTS `{pre}model_{$data['label']}` (
                    `data_id` int(10) NOT NULL AUTO_INCREMENT,
                    `content_id` int(10) NOT NULL,
                    PRIMARY KEY (`data_id`),
                    KEY `content_id` (`content_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
        }
        if ($type == 'edit') {
            $sql = "ALTER TABLE {pre}model_" . $this->formLabel . " RENAME TO {pre}model_" . $data['label'];
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
        $count = target('site/SiteClass')->countList([
            'model_id' => $id
        ]);
        if($count) {
            $this->error = '请先删除模型下的栏目!';
            return false;
        }
        $this->beginTransaction();
        $info = $this->getInfo($id);
        $this->formLabel = $info['label'];
        return true;
    }

    public function _delAfter($id) {
        $status = target('site/SiteModelField')->where([
            'model_id' => $id
        ])->delete();
        if($status === false) {
            $this->rollBack();
            return false;
        }
        $sql = "DROP TABLE `{pre}model_" . $this->formLabel . "`";
        $status = $this->execute($sql);
        if ($status === false) {
            $this->rollBack();
            return false;
        }
        $this->commit();
        return true;
    }

    public function getHtml($id, $info = [], $layer = 0) {
        $html = '';
        $formFields = target('site/SiteModelField')->loadList(['model_id' => $id]);
        if(empty($formFields)) {
            return '';
        }
        foreach ($formFields as $field) {
            if(empty($info)) {
                $data = $field['default'];
            }else {
                $data = $info[$field['label']];
            }
            $cHtml = call_user_func_array([target('site/SiteFormHtml'), $field['type']], [$field['label'], $field['must'], $field['tip'], $data, $field['config']]);
            if($layer) {
                $html .= target('site/SiteFormHtml')->layerWrap($field['name'], $cHtml);
            }else {
                $html .= target('site/SiteFormHtml')->layer($field['name'], $cHtml);
            }
        }
        return $html;

    }

    public function getContent($modelId, $contentId) {
        if(empty($modelId)) {
            return [];
        }
        $modelInfo = target('site/SiteModel')->getInfo($modelId);
        if(empty($modelInfo)) {
            return [];
        }
        return $this->table('model_' . $modelInfo['label'])->where(['content_id' => $contentId])->find();

    }

}