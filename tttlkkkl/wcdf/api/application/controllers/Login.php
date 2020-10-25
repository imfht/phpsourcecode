<?php

/**
 *
 * Date: 16-10-9
 * Time: 下午10:13
 * author :李华 yehong0000@163.com
 */
use system\auth\Auth;
use Yaf\Dispatcher;
use Yaf\Registry;

class LoginController extends system\controllers\Web {
    public function init() {
        parent::init();
    }

    /**
     * 登录地址
     */
    public function loginAction() {
        if (Auth::getInstance()->checkLogin()) {
            $this->redirect($_SERVER['REQUEST_URI']);
        } else {
            $url = Auth::getInstance()->getLoginUrl();
            $this->getView()->assign('url', $url);
            Dispatcher::getInstance()->enableView();
        }
    }

    /**
     * 登录回调
     */
    public function callbackAction() {
        Dispatcher::getInstance()->disableView();
        try {
            $redirect_uri = Registry::get('config')->domain->root;
            if (Auth::getInstance()->callback()) {
                $this->redirect($redirect_uri);
            } else {
                $this->redirect('/system/login/login');
            }
        } catch (\Exception $E) {
            echo $E->getMessage();
        }
    }
}