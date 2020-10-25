<?php

namespace App\Controller;

use Swoole;

class Page extends Swoole\Controller
{
    /**
     * 默认页
     */
    function index()
    {
        if ($this->isLogin()) {
            $this->http->redirect(WEBROOT . "/home/index");
        }

        if (isPost()) {
            $username = getRequest('username');
            $password = getRequest('password');
            $authCode = getRequest('authCode');
            if (!$this->verifyPicCaptcha($authCode)) {
                $this->tpl->assign('error', '图片验证码不正确');
            } else {
                $config = Swoole::$php->config['user'];
                $auth = new Swoole\Auth($config);
                $flag = $auth->login($username, $password);
                if ($flag) {
                    $user = $this->getUid();
                    $_SESSION["role"] = $user["role"];
                    $this->http->redirect(WEBROOT . "/home/index");
                } else {
                    $this->tpl->assign('error', '账户名或者密码错误');
                }
            }
        }
        $this->tpl->display("page/login.html");
    }

    /**
     * 判断是否登录
     *
     * @return boolean
     */
    function isLogin()
    {
        if (isset($_SESSION["isLogin"]) && $_SESSION["isLogin"] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 登陆页面
     */
    function login()
    {
        if (isPost()) {
            $username = getRequest('username');
            $password = getRequest('password');
            $authCode = getRequest('authCode');
            if (!$this->verifyPicCaptcha($authCode)) {
                $this->tpl->assign('error', '图片验证码不正确');
            } else {
                $config = Swoole::$php->config['user'];
                $auth = new Swoole\Auth($config);
                $flag = $auth->login($username, $password);
                if ($flag) {
                    $user = $auth->getUid();
                    $_SESSION["role"] = $user["role"];
                    $this->http->redirect(WEBROOT . "/home/index");
                } else {
                    $this->tpl->assign('error', '账户名或者密码错误');
                }
            }
        }
        $this->tpl->display("page/login.html");
    }

    /**
     * 获取验证码
     */
    function vcode()
    {
        $this->session->start();
        $this->http->header('Content-Type', 'image/jpeg');
        $result = Swoole\Image::verifycode_gd();
        $_SESSION['authcode'] = $result['code'];
        echo $result['image'];
        //var_dump($result);exit;
    }

    /**
     * 验证图片验证码是否正确
     *
     * @param type $authcode
     * @return boolean
     */
    private function verifyPicCaptcha($authcode)
    {
        Swoole::$php->session->start();
        // 检查验证码
        if (!$authcode) {
            return false;
        } else {
            if ($_SESSION['authcode'] != strtoupper($authcode)) {
                return false;
            }
        }
        return true;
    }
}
