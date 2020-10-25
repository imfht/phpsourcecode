<?php

/**
 * 通用访问公共控制器
 * Date: 16-10-18
 * Time: 下午9:48
 * author :李华 yehong0000@163.com
 */
namespace system\controllers;
use system\auth\Auth;
class Web extends \Yaf\Controller_Abstract
{
    public function init()
    {
        if(!Auth::checkLogin() && strtolower($this->getRequest()->getControllerName()) != 'login'){
            $this->redirect('system/login/login');
        }
    }
}