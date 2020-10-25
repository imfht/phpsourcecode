<?php

/**
 * 后台的父类控制器,所有需要登录的用户页面控制器必需继承此控制器
 */
class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        //规定用户id为1的用户为管理员
        if (!(isset($this->user) && $this->user['id'] == 1)) {
            redirect('account/signin');
        }
    }
}
