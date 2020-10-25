<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\user\user;

/**
 * 用户案例
 */
class Demouser
{

    public function remark()
    {
        $token = $_GET['token'];
        $data['openid'] = $_GET['openid'];
        $data['remark'] = $_GET['remark'];
        $class = new user($token);
        $ret = $class->remark($data['openid'], $data['remark']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function getUserBlackList()
    {
        $token = $_GET['token'];
        // $openid = $_GET['openid'];
        $class = new user($token);
        $ret = $class->getBlackList();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    public function getUserInfo()
    {
        $token = $_GET['token'];
        $openid = $_GET['openid'];
        $class = new user($token);
        $ret = $class->getUserInfo($openid);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function getUser()
    {
        $token = $_GET['token'];
        $class = new user($token);
        $ret = $class->getUsers();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
}
