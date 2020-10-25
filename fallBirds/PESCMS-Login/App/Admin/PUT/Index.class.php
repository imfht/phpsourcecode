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

class Index extends \App\Admin\Common {

    /**
     * 更新菜单
     */
    public function menuAction() {
        $result = \Model\Menu::updateMenu();
        $this->success('更新菜单成功', $this->url(GROUP . '-Index-menuList'));
    }

}
