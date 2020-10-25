<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin\PUT;

/**
 * 模型管理
 */
class Model extends \App\Admin\Common {

    /**
     * 更新模型
     */
    public function action() {
        $model = \Model\ModelManage::findModel($_POST['model_id']);
        \Model\ModelManage::updateModel();


        //更新菜单
        $this->db('menu')->where('menu_name = :old_name')->update(array('menu_name' => $this->p('display_name'), 'noset' => array('old_name' => $model['lang_key'])));

        $this->success('更新模型成功', $this->url(GROUP . '-Model-index'));
    }

    /**
     * 更新字段
     */
    public function fieldAction() {
        $result = \Model\Field::updateField();
        $this->success('更新字段成功', $this->url(GROUP . '-Model-fieldList', array('id' => $result['model_id'])));
    }

}
