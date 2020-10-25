<?php
namespace app\run\controller;


use app\common\controller\Run;

class User extends Run
{
    public function initialize()
    {
        $this->Auth->allow(['login', 'ajax_login']);
        call_user_func(array('parent', __FUNCTION__));
    }

    public function login()
    {
        $this->assign->addCss('admin/login');
        $this->assign->addCss('/files/loaders/loaders');
        $this->assign->addJs('jquery.easing.1.3');
        //$this->assign->addJs('admin/login/dat.gui.min', true);
        //$this->assign->addJs('admin/login/toxiclibs.min', true);
        //$this->assign->addJs('admin/login/animitter.min', true);
        //$this->assign->addJs('admin/login/login', true);
        $this->assign->addJs('/files/loaders/loaders.css.js');
        
        if ($this->Auth->user('id')) {
            return $this->message('success', '亲！你已经登录了，无需重新登录', array('back' => false, '返回首页' => array('run/Index/index')));
        }
        $redirect = session('REFERER') ? session('REFERER') : 'run/Index/index';
        if (strpos($redirect, 'run/') === false) {
            $redirect = 'run/Index/index';
        }

        if ($this->request->isPost() && $this->Form->check_token()) {
            if (captcha_check(input('post.captcha'))) {
                $this->Auth->login();
                $logined = $this->Auth->user();

                if ($logined) {
                    if ($logined['status'] == 'verified') {
                        //登录成功
                        \Hook::listen('login', $logined);
                        $this->redirect($redirect);
                    } elseif ($logined['status'] == 'unverified') {
                        $this->assign->error = '用户名未激活';
                        $this->Auth->logout();
                    } else {
                        $this->assign->error = '用户名已禁用';
                        $this->Auth->logout();
                    }
                } else {
                    $this->assign->error = '亲！用户名或者密码有误';
                }
            } else {
                $this->assign->error = '亲！验证码错误了哦';
            }
        }
        return $this->fetch = 'login';
    }
    
    public function ajax_login()
    {
        if (!$this->request->isAjax()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        if (!captcha_check(input('post.captcha'))) {
            return $this->ajax('error', '亲！验证码错误了哦');
        }
        
        $redirect = session('REFERER') ? session('REFERER') : url('run/Index/index');
        if (strpos($redirect, 'run/') === false) {
            $redirect = url('run/Index/index');
        }
        
        $this->Auth->login();
        $logined = $this->Auth->user();
        if ($logined) {
            if ($logined['status'] == 'verified') {
                //登录成功
                \Hook::listen('login', $logined);
                return $this->ajax('success', '<span style="color:#0d957a;">登录成功，页面即将跳转...</span>',['url' => $redirect]);
                
            } elseif ($logined['status'] == 'unverified') {
                $this->Auth->logout();
                return $this->ajax('error', '用户名未激活');
            } else {
                $this->Auth->logout();
                return $this->ajax('error', '用户名已禁用');
            }
        } else {
            return $this->ajax('error', '亲！用户名或者密码有误');
        }
    }
    

    public function logout()
    {
        if ($this->Auth->user('id')) {
            $this->Auth->logout();
        }
            
        $this->redirect('User/login');
    }

    public function modify()
    {
        $this->assignDefault('password', '');
        $this->mdl->form['password']['info'] = '不修改请保持为空';
        return call_user_func(array('parent', __FUNCTION__));
    }

    public function lists()
    {
        if (!$this->local['filter']) {
            $this->local['filter'] = [
                'username',
                'status'
            ];
        }

        if (!$this->local['list_fields']) {
            $this->local['list_fields'] = array(
                'username' => array(
                    'name' => '用户名'
                ),
                'user_group_id',
                //'Member.nickname',
                //'Member.truename',
                //'Member.headimg',
                'status',

                'logined_ip',
                'logined',
                'created'
            );
        }
        
        $this->addItemAction('用户信息', array('Member/modify', ['parent_id' => 'id'], 'parse' => ['parent_id']), '&#xe612;');
        call_user_func(array('parent', __FUNCTION__));
        $this->addAction("登录日志", array('UserLogin/lists'), 'fa-eye');
    }
}
