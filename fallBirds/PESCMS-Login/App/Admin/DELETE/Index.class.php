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

class Index extends \App\Admin\Common {

    /**
     * 删除菜单
     */
    public function menuAction() {
        $id = $this->isG('id', '请选择要删除的数据!');
        $result = \Model\ModelManage::deleteFromModelId('menu', $id);
        if (empty($result)) {
            $this->error('删除失败');
        } else {
            $this->success('删除成功');
        }
    }

}
