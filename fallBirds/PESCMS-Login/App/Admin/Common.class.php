<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace App\Admin;

abstract class Common extends \Core\Controller\Controller {

    /**
     * 后台管理员信息
     */
    protected $admin;

    public function __init() {
        $login = $this->checkLogin();

        if ($login == FALSE && MODULE != 'Login') {
            $this->jump($this->url(GROUP . '-Login-index'));
        }
    }

    /**
     * 验证是否已登录
     */
    protected function checkLogin() {
        if (!empty($_SESSION['admin'])) {
            $this->admin = $_SESSION['admin'];
            return true;
        }else{
            return false;
        }
    }

}
