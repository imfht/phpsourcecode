<?php
namespace app\index\controller;

use youwen\think_user\User;
use youwen\think_user\UserCode;
use youwen\think_user\UserInfo;

class Utest
{

    public function sendCode($key='18500818840', $value='359999')
    {
        $codeObj = new UserCode('mysql');
        $ret = $codeObj->setCode($key, $value);
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }

    public function getCode($key='18500818840')
    {
        $codeObj = new UserCode('mysql');
        $ret = $codeObj->getCode($key);
        echo '<pre>';
        print_r( $ret );
        exit('</pre>');
    }

    public function check($key='18500818840', $code='359999')
    {
        $codeObj = new UserCode('mysql');
        $ret = $codeObj->checkCode($key, $code);
        echo '<pre>';
        var_dump( $ret );
        exit('</pre>');
    }

    public function reg($username='18500818840', $password='123456')
    {

        // 验证码校验
        $code = input('code', '123456');
        // $codeObj = new UserCode('mysql');
        // $ret = $codeObj->checkCode($username, $code);
        // if(0 !== $ret){
        //     // 验证码不正确
        // }

        $user = new User();
        // 用户校验
        

        // 用户注册
        $ret = $user->regsiter($username, $password, $password);
        if(0 !== $ret){
            $msg = $user->getErrorMsg($ret);
            var_dump( $msg );
        }
        echo '<pre>';
        var_dump( $ret );
        exit('</pre>');
    }

    public function login($username='18500818840', $password='123456')
    {
        $user = new User();
        $ret = $user->login($username, $password);
        if(0 !== $ret){
            $msg = $user->getErrorMsg($ret);
            var_dump( $msg );
        }

        $userDetail = $user->getUserDetail();

        $obj = \youwen\think_user\LoginSession::getDriver('pc');

        $ret = $obj->setLoginSession($userDetail);
        echo '<pre>';
        print_r( $obj );
        exit('</pre>');
    }

    public function islogin()
    {
        $obj = \youwen\think_user\LoginSession::getDriver('pc');
        $ret = $obj->isLogin();
        echo '<pre>';
        var_dump( $ret );
        var_dump( \think\Session::get('userRow') );

        exit('</pre>');
    }

    public function logout()
    {
        $obj = \youwen\think_user\LoginSession::getDriver('pc');
        $ret = $obj->logout();
        echo '<pre>';
        var_dump( $ret );
        exit('</pre>');
    }
}
