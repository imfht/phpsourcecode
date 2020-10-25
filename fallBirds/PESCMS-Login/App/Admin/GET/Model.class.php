<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin\GET;

/**
 * 模型管理
 */
class Model extends \App\Admin\Common {

    /**
     * 模型列表
     */
    public function index() {
        $this->assign('list', \Model\ModelManage::modelList());
        $this->assign('title', \Model\Menu::getTitleWithMenu());
        $this->layout();
    }

    /**
     * 模型添加/编辑
     */
    public function action() {
        $modelId = $this->g('id');
        if (empty($modelId)) {
            $this->assign('method', 'POST');
            $this->assign('title', '添加模型');
        } else {
            $model = \Model\ModelManage::findModel($modelId);
            if (empty($model)) {
                $this->error('不存在的模型');
            }
            $this->assign($model);
            $this->assign('method', 'PUT');
            $this->assign('modelId', $modelId);
            $this->assign('title', "编辑模型 - {$model['model_name']}");
        }
        $this->layout();
    }

    /**
     * 模型字段管理
     */
    public function fieldList() {
        $modelId = $this->isG('id', '请选择模型');
        $model = \Model\ModelManage::findModel($modelId);
        $this->assign('title', "字段管理 - {$model['lang_key']}");
        $this->assign('list', \Model\Field::fieldList($modelId));
        $this->assign('modelId', $modelId);
        $this->layout();
    }

    /**
     * 字段添加/编辑
     */
    public function fieldAction() {
        $fieldId = $this->g('id');
        $modelId = $this->isG('model', '请选择模型');
        $model = \Model\ModelManage::findModel($modelId);

        if (empty($fieldId)) {
            $this->assign('method', 'POST');
            $this->assign('title', "添加字段 - {$model['lang_key']}");
        } else {
            $field = \Model\Field::findField($fieldId);
            if (empty($field)) {
                $this->error('不存在的字段');
            }
            $this->assign($field);
            $this->assign('method', 'PUT');
            $this->assign('title', "编辑字段 - {$model['lang_key']}");
        }

        $fieldTypeOption = \Model\Option::findOption('fieldType');
        $this->assign('fieldTypeList', json_decode($fieldTypeOption['value'], true));

        $this->assign('modelId', $modelId);
        $this->layout();
    }

}
