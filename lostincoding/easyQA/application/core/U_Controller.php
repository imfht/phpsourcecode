<?php

/**
 * 用户的父类控制器,所有需要登录的用户页面控制器必需继承此控制器
 */
class U_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($this->user)) {
            redirect('account/signin');
        }
    }
}
