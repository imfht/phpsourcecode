<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin\DELETE;

/**
 * 模型管理
 */
class Model extends \App\Admin\Common {

    /**
     * 删除模型
     */
    public function action() {
        $modelId = $this->isG('id', '请选择要删除的数据!');

        $model = \Model\ModelManage::findModel($modelId);
        if (empty($model)) {
            $this->error('模型不存在');
        }

        $this->db()->transaction();

        $deleteModelResult = \Model\ModelManage::deleteModel($modelId);
        if (empty($deleteModelResult)) {
            $this->db()->rollBack();
            $this->error('删除模型失败');
        }

        $deleteModelField = \Model\Field::deleteModelField($modelId);
        if (empty($deleteModelField)) {
            $this->db()->rollBack();
            $this->error('移除模型字段记录失败');
        }

        $deleteMenuResult = \Model\Menu::deleteMenu($model['lang_key']);
        if (empty($deleteMenuResult)) {
            $this->db()->rollBack();
            $this->error('删除菜单失败');
        }

        $this->db()->commit();

        $alterTableResult = \Model\ModelManage::alterTable(strtolower($model['model_name']));
        if (empty($alterTableResult)) {

            $log = new \Expand\Log();
            $failLog = "Alter Model Table Field: {$this->prefix}{$model['model_name']}" . date("Y-m-d H:i:s");
            $log->creatLog('modelError', $failLog);

            $this->error('删除数据库表失败');
        }

        $this->success('删除成功');
    }

    /**
     * 删除字段
     */
    public function fieldAction() {
        $id = $this->isG('id', '请选择要删除的数据!');

        $field = \Model\Field::findField($id);

        if (empty($field)) {
            $this->error('不存在的字段');
        }

        $removeFieldResult = \Model\Field::removeField($id);
        if (empty($removeFieldResult)) {
            $this->error('删除失败');
        }

        $model = \Model\ModelManage::findModel($field['model_id']);

        $alertTableFieldResult = \Model\Field::alertTableField($model['model_name'], $field['field_name']);
        if (empty($alertTableFieldResult)) {

            $log = new \Expand\Log();
            $failLog = "Delete Field: " . strtolower($model['model_name']) . "_{$field['field_name']}, Model:{$model['model_name']}  " . date("Y-m-d H:i:s");
            $log->creatLog('fieldError', $failLog);

            $this->error('移除数据库表字段失败');
        }

        $this->success('删除成功');
    }

}
