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

class Login extends \App\Admin\Common {

    public function dologin() {
        $data['account'] = $this->isP('account', '请提交帐号');
        $data['password'] = \Core\Func\CoreFunc::generatePwd($data['account'] . $this->isP('password', '请提交密码'), 'PRIVATE_KEY');
        $checkAccount = $this->db('user')->where('user_account = :account AND user_password = :password AND user_status = 1')->find($data);
        if (empty($checkAccount)) {
            $this->error('帐号或者密码错误');
        }
        $this->setLogin($checkAccount);
        $this->success('登录成功', $this->url(GROUP . '-Index-index'));
    }

    /**
     * 设置登录信息
     * @param type $content 帐号内容
     */
    private function setLogin($content) {
        $_SESSION['admin'] = $content;
    }

}
