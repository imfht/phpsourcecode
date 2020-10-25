<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin\POST;

/**
 * 模型管理
 */
class Model extends \App\Admin\Common {

    /**
     * 添加模型
     */
    public function action() {
        $this->db()->transaction();
        /**
         * 插入模型信息
         */
        $addModelresult = \Model\ModelManage::addModel();
        if ($addModelresult === false) {
            $this->db()->rollBack();
            $this->error('添加模型失败');
        }

        /**
         * 插入模型菜单
         */
        $addMenuResult = \Model\Menu::insertModelMenu($addModelresult['lang_key'], '9', GROUP . "-{$addModelresult['model_name']}-index");
        if ($addMenuResult === false) {
            $this->db()->rollBack();
            $this->error('插入菜单失败');
        }

        /**
         * 插入初始化的字段
         */
        \Model\ModelManage::setInitField($addModelresult['model_id']);

        $this->db()->commit();

        $initResult = \Model\ModelManage::initModelTable($addModelresult['model_name']);

        $this->success('添加模型成功', $this->url(GROUP . '-Model-index'));
    }

    /**
     * 添加字段
     */
    public function fieldAction() {
        $result = \Model\Field::addField();

        $this->success('添加字段成功', $this->url(GROUP . '-Model-fieldList', array('id' => $result['model_id'])));
    }

}
