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

class Index extends \App\Admin\Common {

    /**
     * 添加新菜单
     */
    public function menuAction() {
        $result = \Model\Menu::addMenu();
        $this->success('添加菜单成功', $this->url(GROUP . '-Index-menuList'));
    }
    
    /**
     * 更新管理员密码
     */
    public function updatePassword(){
        $data['account'] = $_SESSION['admin']['user_account'];
        $data['password'] = \Core\Func\CoreFunc::generatePwd($data['account'] . $this->isP('password', '请提交密码'), 'PRIVATE_KEY');
        $this->db('user')->where('user_id = :user_id')->update(array('user_password' => $data['password'], 'noset' => array('user_id' => $_SESSION['admin']['user_id'])));
        
        $this->success('更新密码成功!', $this->url(GROUP.'-Index-index'));
    }

}
